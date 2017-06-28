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
 * @package    PfpjRom_CustomerRom
 * @copyright  Copyright (c) Daniel Ifrim
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Overrides default Customer Address Controller
 *
 * @author     Daniel Ifrim
 */

require_once('app/code/core/Mage/Customer/controllers/AddressController.php');

class PfpjRom_CustomerRom_AddressController extends Mage_Customer_AddressController
{
	public function formPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        // Save data
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer/address')
                ->setData($this->getRequest()->getPost())
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false))


/* [start] PfpjRom add */

                ->setPfpjForBilling($this->getRequest()->getParam('pfpj_for_billing', 0))
                ->setPfpjForShipping($this->getRequest()->getParam('pfpj_for_shipping', 0));

/* [end] PfpjRom add */


            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) {
                $customerAddress = $this->_getSession()->getCustomer()->getAddressById($addressId);
                if ($customerAddress->getId() && $customerAddress->getCustomerId() == $this->_getSession()->getCustomerId()) {
                    $address->setId($addressId);
                }
                else {
                    $address->setId(null);
                }
            }
            else {
                $address->setId(null);
            }
            try {
                $accressValidation = $address->validate();
                if (true === $accressValidation) {
                    $address->save();
                    $this->_getSession()->addSuccess($this->__('The address has been saved.'));
                    $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                    return;
                } else {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                    if (is_array($accressValidation)) {
                        foreach ($accressValidation as $errorMessage) {
                            $this->_getSession()->addError($errorMessage);
                        }
                    } else {
                        $this->_getSession()->addError($this->__('Cannot save the address.'));
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save address.'));
            }
        }
        $this->_redirectError(Mage::getUrl('*/*/edit', array('id'=>$address->getId())));
    }

}