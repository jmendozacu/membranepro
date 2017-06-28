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
 * Overrides default Customer Address Model
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_CustomerRom_Model_Address extends Mage_Customer_Model_Address
{
    protected function _beforeSave()
    {
    	$this->_cleanAddress();
        parent::_beforeSave();
    }

    protected function _cleanAddress()
    {
		foreach ($this->getConfig()->getFields() as $_fieldName => $_fieldConfig) {
			if (in_array($_fieldName, array('pfpj_for_billing', 'pfpj_for_shipping')))
    			continue;
	    	if (!$this->showField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping()))
	    		$this->setData($_fieldName, "");
		}
    }

    public function showField($fieldName, $tip_pers, $for_billing, $for_shipping)
    {
    	$show = false;
    	$state = (!($for_billing == 1 && $for_shipping == 1) ? ($for_billing == 1 ? 'billing' : 'shipping') : 'all');
    	if ($this->getConfig()->showField($fieldName, $tip_pers, $state))
    		$show = true;
    	return $show;
    }

    public function isRequiredField($fieldName, $tip_pers, $for_billing, $for_shipping)
    {
    	$required = false;
    	if (in_array($fieldName, array('pfpj_for_billing', 'pfpj_for_shipping')))
    		return $required;
    	$state = (!($for_billing == 1 && $for_shipping == 1) ? ($for_billing == 1 ? 'billing' : 'shipping') : 'all');
    	if ($this->getConfig()->isFieldRequiredByOption($fieldName, $tip_pers, $state))
    		$required = true;
    	return $required;
    }

    /**
     * Retrive address config object
     *
     * @return PfpjRom_Customer_Model_Address_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('customer/address_config');
    }

    /**
     * Adds validation for tippers.
     *
     * @return bool
     */
    public function validate()
    {
    	$attributes = $this->getAttributes();
    	/*$errors = parent::validate();
    	if (!is_array($errors))
    		$errors = array();*/
    	$errors = array();

    	$helper = Mage::helper('customer');
    	$this->implodeStreetAddress();
    	if (!Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the first name.');
        }

        if (!Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the last name.');
        }

        if (!Zend_Validate::is($this->getStreet(1), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the street.');
        }

        if (!Zend_Validate::is($this->getCity(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the city.');
        }

        if (!Zend_Validate::is($this->getTelephone(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the telephone number.');
        }

        $_havingOptionalZip = Mage::helper('directory')->getCountriesWithOptionalZip();
        if (!in_array($this->getCountryId(), $_havingOptionalZip) && !Zend_Validate::is($this->getPostcode(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the zip/postal code.');
        }

        if (!Zend_Validate::is($this->getCountryId(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the country.');
        }

        if ($this->getCountryModel()->getRegionCollection()->getSize()
               && !Zend_Validate::is($this->getRegionId(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the state/province.');
        }

    	foreach ($this->getConfig()->getFields() as $_fieldName => $_fieldConfig) {
			if ($this->showField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping())) {
				switch ($_fieldName) {
					case 'pfpj_tip_pers':
						if ($this->isRequiredField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping()) && !Zend_Validate::is($this->getData($_fieldName), 'NotEmpty')) {
							$errors[] = $helper->__('Please select Person type.');
						}
					break;

					case 'pfpj_cui':
						if ($this->isRequiredField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping()) && !Zend_Validate::is($this->getData($_fieldName), 'NotEmpty')) {
							$errors[] = $helper->__('Please enter Tax Identification Number.');
						}

						if ($this->getConfig()->isValidation($_fieldName)) {
							$validCif = new PfpjRom_Validate_Cif();
@ $validCif->setTranslator(Mage::app()->getTranslator()->getTranslate());
							if (!$validCif->isValid($this->getData($_fieldName))) {
								$mt = $validCif->getMessageTemplates();
								foreach ($validCif->getMessages() as $messageId => $message) {
									$errors[] = $message;
								}
							}
						}
					break;

					case 'pfpj_reg_com':
						if ($this->isRequiredField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping()) && !Zend_Validate::is($this->getData($_fieldName), 'NotEmpty')) {
							$errors[] = $helper->__('Please enter Number in Trade Register Office.');
						}
					break;

					case 'pfpj_banca':
						if ($this->isRequiredField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping()) && !Zend_Validate::is($this->getData($_fieldName), 'NotEmpty')) {
							$errors[] = $helper->__('Please enter Bank.');
						}
					break;

					case 'pfpj_iban':
						if ($this->isRequiredField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping()) && !Zend_Validate::is($this->getData($_fieldName), 'NotEmpty')) {
							$errors[] = $helper->__('Please enter IBAN.');
						}
					break;

					case 'pfpj_cnp':
						if ($this->isRequiredField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping())) {
							if (!Zend_Validate::is($this->getData($_fieldName), 'NotEmpty')) {
								$errors[] = $helper->__('Please enter Personal Identification Code.');
							}
						}

						if ($this->getConfig()->isValidation($_fieldName)) {
							$validCnp = new PfpjRom_Validate_Cnp();
@ $validCnp->setTranslator(Mage::app()->getTranslator()->getTranslate());
							if (!$validCnp->isValid($this->getData($_fieldName))) {
								$mt = $validCnp->getMessageTemplates();
								foreach ($validCnp->getMessages() as $messageId => $message) {
									$errors[] = $message;
								}
							}
						}
					break;

					case 'pfpj_serienr_buletin':
						if ($this->isRequiredField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping()) && !Zend_Validate::is($this->getData($_fieldName), 'NotEmpty')) {
							$errors[] = $helper->__('Please enter ID Paper Serie/Number.');
						}
					break;

					default:
						if ($this->isRequiredField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping()) && !Zend_Validate::is($this->getData($_fieldName), 'NotEmpty')) {
							$errors[] = $helper->__('Please enter ' . $_fieldName . '.');
						}
				}
			}
		}

		if (empty($errors) || $this->getShouldIgnoreValidation()) {
			return true;
      }

      return $errors;
    }

    public function isAttributeRequired($name) {
    	$is_required = false;
    	$attributes = $this->getAttributes();
    	if (isset($attributes[$name]) && $attributes[$name]->getIsRequired())
    		$is_required = true;
    	return $is_required;
    }

    public function getAttributeByName($name)
    {
    	$attr = null;
    	$attributes = $this->getAttributes();
    	if (isset($attributes[$name]))
    		$attr = $attributes[$name];

    	return $attr;
    }
}