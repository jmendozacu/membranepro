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
 * @package    PfpjRom_CheckoutRom
 * @copyright  Copyright (c) Daniel Ifrim
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Overrides default model
 *
 * @author     Daniel Ifrim
 */

class PfpjRom_CheckoutRom_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    /**
     * Save billing address information to quote
     * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveBilling($data, $customerAddressId)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }

/* Cybertech Magento 1.6 correction
 * Don't check if address_id is present [because it always is - in new versions of Magento every customer has a default (empty) address assigned, pre-filled only with name]
 * We will check if the address is new, by checking if the city and street are empty [if they are, it means the address is the default (auto-generated) one]
 */
if($customerAddressId):
	$customerAddressCheck = Mage::getModel('customer/address')->load($customerAddressId);
	//$addressPersType = $customerAddressCheck->getPfpjTipPers();
	$addressCity = $customerAddressCheck->getCity();
	$addressStreetArr = $customerAddressCheck->getStreet();
	$addressStreet = $addressStreetArr[0];
endif;
/* END Cybertech correction */
        
        $address = $this->getQuote()->getBillingAddress();
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        //if (!empty($customerAddressId)) {	// Cybertech removed this condition
        if (!empty($customerAddressId) && !empty($addressCity) && !empty($addressStreet)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => $this->_helper->__('Customer Address is not valid.')
                    );
                }

                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
                $addressForm->setEntity($address);
                $addressErrors  = $addressForm->validateData($address->getData());
                if ($addressErrors !== true) {
                    return array('error' => 1, 'message' => $addressErrors);
                }
            }
        } else {
        	if(!$customerAddressId) unset($data['address_id']);
            $address->addData($data);
            $addressForm->setEntity($address);
            // emulate request object
            $addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
            $addressErrors  = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                return array('error' => 1, 'message' => $addressErrors);
            }
            $addressForm->compactData($addressData);
            //unset billing address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                if (!isset($data[$attribute->getAttributeCode()])) {
                    $address->setData($attribute->getAttributeCode(), NULL);
                }
            }

            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
        }

        // validate billing address
        if (($validateRes = $address->validate()) !== true) {
            return array('error' => 1, 'message' => $validateRes);
        }

        $address->implodeStreetAddress();

        if (true !== ($result = $this->_validateCustomerData($data))) {
            return $result;
        }

        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
/* [start] PfpjRom edit */
                //return array('error' => 1, 'message' => $this->_customerEmailExistsMessage);
                return array('error' => 1, 'message' => $this->_helper->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.'));
/* [end] PfpjRom edit */
            }
        }

        if (!$this->getQuote()->isVirtual()) {
            /**
             * Billing address using options
             */
            $usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;

            switch($usingCase) {
                case 0:
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shipping->setSameAsBilling(0);
                    break;
                case 1:
                    $billing = clone $address;
                    $billing->unsAddressId()->unsAddressType();
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shippingMethod = $shipping->getShippingMethod();

                    // don't reset original shipping data, if it was not changed by customer
                    foreach ($shipping->getData() as $shippingKey => $shippingValue) {
                        if (!is_null($shippingValue)
                            && !is_null($billing->getData($shippingKey))
                            && !isset($data[$shippingKey])) {
                            $billing->unsetData($shippingKey);
                        }
                    }
                    $shipping->addData($billing->getData())
                        ->setSameAsBilling(1)
/* [start] PfpjRom add */
                        ->setPfpjForBilling(0)
/* [end] PfpjRom add */
						->setSaveInAddressBook(0)
                        ->setShippingMethod($shippingMethod)
                        ->setCollectShippingRates(true);
                    $this->getCheckout()->setStepData('shipping', 'complete', true);
                    break;
            }
        }

        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        $this->getCheckout()
            ->setStepData('billing', 'allow', true)
            ->setStepData('billing', 'complete', true)
            ->setStepData('shipping', 'allow', true);

        return array();
    }

	
	
	
    /**
     * Save checkout shipping address
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveShipping($data, $customerAddressId)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        
/* Cybertech Magento 1.6 correction
 * Don't check if address_id is present [because it always is - in new versions of Magento every customer has a default (empty) address assigned, pre-filled only with name]
 * We will check if the address is new, by checking if the city and street are empty [if they are, it means the address is the default (auto-generated) one]
 */
	$customerAddressCheck = Mage::getModel('customer/address')->load($customerAddressId);
	//$addressPersType = $customerAddressCheck->getPfpjTipPers();
	$addressCity = $customerAddressCheck->getCity();
	$addressStreetArr = $customerAddressCheck->getStreet();
	$addressStreet = $addressStreetArr[0];
/* END Cybertech correction */
        
        $address = $this->getQuote()->getShippingAddress();
/* [start] PfpjRom add */
        $address->setPfpjForBilling(0);
/* [end] PfpjRom add */

        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm    = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        //if (!empty($customerAddressId)) {	// Cybertech removed this condition
        if (!empty($customerAddressId) && !empty($addressCity) && !empty($addressStreet)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => $this->_helper->__('Customer Address is not valid.')
                    );
                }

                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
                $addressForm->setEntity($address);
                $addressErrors  = $addressForm->validateData($address->getData());
                if ($addressErrors !== true) {
                    return array('error' => 1, 'message' => $addressErrors);
                }
            }
        } else {
        	if(!$customerAddressId) unset($data['address_id']);
        	$address->addData($data);
            $addressForm->setEntity($address);
            // emulate request object
            $addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
            $addressErrors  = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                return array('error' => 1, 'message' => $addressErrors);
            }
            $addressForm->compactData($addressData);
            // unset shipping address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                if (!isset($data[$attribute->getAttributeCode()])) {
                    $address->setData($attribute->getAttributeCode(), NULL);
                }
            }

            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
            $address->setSameAsBilling(empty($data['same_as_billing']) ? 0 : 1);
        }

        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        if (($validateRes = $address->validate())!==true) {
            return array('error' => 1, 'message' => $validateRes);
        }
		
/* [start] PfpjRom add */
        $address->setPfpjForBilling(0);
/* [end] PfpjRom add */

        $this->getQuote()->collectTotals()->save();

        $this->getCheckout()
            ->setStepData('shipping', 'complete', true)
            ->setStepData('shipping_method', 'allow', true);

        return array();
    }

}