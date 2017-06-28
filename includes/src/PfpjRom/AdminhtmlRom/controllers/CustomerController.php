<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   PfpjRom
 * @package    PfpjRom_AdminhtmlRom
 * @copyright  Copyright (c) Daniel Ifrim
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Override Customer admin controller save action
 * - just sets pfpj_for_billing, pfpj_for_shipping to 0 when empty
 *
 * @category    PfpjRom
 * @package     PfpjRom_AdminhtmlRom
 * @author      Daniel Ifrim
 */
include_once("Mage/Adminhtml/controllers/CustomerController.php");
class PfpjRom_AdminhtmlRom_CustomerController extends Mage_Adminhtml_CustomerController
{
	/**
     * Save customer action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            $redirectBack   = $this->getRequest()->getParam('back', false);
            $this->_initCustomer('customer_id');
            /** @var Mage_Customer_Model_Customer */
            $customer = Mage::registry('current_customer');
            // Prepare customer saving data
            if (isset($data['account'])) {
                if (isset($data['account']['email'])) {
                    $data['account']['email'] = trim($data['account']['email']);
                }
                $customer->addData($data['account']);
            }
            // unset template data
            if (isset($data['address']['_template_'])) {
                unset($data['address']['_template_']);
            }

            $modifiedAddresses = array();

            if (! empty($data['address'])) {
                foreach ($data['address'] as $index => $addressData) {
                    if (($address = $customer->getAddressItemById($index))) {
                        $addressId           = $index;
                        $modifiedAddresses[] = $index;
                    } else {
                        $address   = Mage::getModel('customer/address');
                        $addressId = null;


/* [start] PfpjRom add */

                        if (!isset($addressData['pfpj_for_billing']))
							$addressData['pfpj_for_billing'] = 0;
						if (!isset($addressData['pfpj_for_shipping']))
							$addressData['pfpj_for_shipping'] = 0;

/* [end] PfpjRom add */


                        $customer->addAddress($address);
                    }

                    $address->setData($addressData)
                            ->setId($addressId)
                            ->setPostIndex($index); // We need set post_index for detect default addresses
                }
            }
            // not modified customer addresses mark for delete
            foreach ($customer->getAddressesCollection() as $customerAddress) {
                if ($customerAddress->getId() && ! in_array($customerAddress->getId(), $modifiedAddresses)) {
                    $customerAddress->setData('_deleted', true);
                }
            }

            if(isset($data['subscription'])) {
                $customer->setIsSubscribed(true);
            } else {
                $customer->setIsSubscribed(false);
            }

            $isNewCustomer = !$customer->getId();
            try {
                if ($customer->getPassword() == 'auto') {
                    $sendPassToEmail = true;
                    $customer->setPassword($customer->generatePassword());
                }

                // force new customer active
                if ($isNewCustomer) {
                    $customer->setForceConfirmed(true);
                }

                Mage::dispatchEvent('adminhtml_customer_prepare_save',
                    array('customer' => $customer, 'request' => $this->getRequest())
                );

                $customer->save();
                // send welcome email
                if ($customer->getWebsiteId() && ($customer->hasData('sendemail') || isset($sendPassToEmail))) {
                    $storeId = $customer->getSendemailStoreId();
                    if ($isNewCustomer) {
                        $customer->sendNewAccountEmail('registered', '', $storeId);
                    }
                    // confirm not confirmed customer
                    elseif ((!$customer->getConfirmation())) {
                        $customer->sendNewAccountEmail('confirmed', '', $storeId);
                    }
                }

                // TODO? Send confirmation link, if deactivating account

                if ($newPassword = $customer->getNewPassword()) {
                    if ($newPassword == 'auto') {
                        $newPassword = $customer->generatePassword();
                    }
                    $customer->changePassword($newPassword);
                    $customer->sendPasswordReminderEmail();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The customer has been saved.'));
                Mage::dispatchEvent('adminhtml_customer_save_after',
                    array('customer' => $customer, 'request' => $this->getRequest())
                );

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id'    => $customer->getId(),
                        '_current'=>true
                    ));
                    return;
                }
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id'=>$customer->getId())));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/customer'));
    }

}
