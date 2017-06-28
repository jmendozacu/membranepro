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
 * Overrides Order create address form
 *
 * @author      Daniel Ifrim
 */
class PfpjRom_AdminhtmlRom_Block_SalesRom_Order_Create_Form_Address extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Address
{
	protected function _prepareForm()
    {
        if (!$this->_form) {
            $this->_form = new Varien_Data_Form();
            $fieldset = $this->_form->addFieldset('main', array('no_container'=>true));
            $addressModel = Mage::getModel('customer/address');

            foreach ($addressModel->getAttributes() as $attribute) {
                if ($attribute->hasData('is_visible') && !$attribute->getIsVisible()) {
                    continue;
                }
                if ($inputType = $attribute->getFrontend()->getInputType()) {
                    $element = $fieldset->addField($attribute->getAttributeCode(), $inputType,
                        array(
                            'name'  => $attribute->getAttributeCode(),
                            'label' => $this->__($attribute->getFrontend()->getLabel()),
                            'class' => $attribute->getFrontend()->getClass(),
                            'required' => $attribute->getIsRequired(),
                        )
                    )
                    ->setEntityAttribute($attribute);

                    if ('street' === $element->getName()) {
                        $lines = Mage::getStoreConfig('customer/address/street_lines', $this->getStoreId());
                        $element->setLineCount($lines);
                    }


/* [start] PfpjRom edit */

                    if ($inputType == 'select' || $inputType == 'multiselect' || $inputType == 'radios') {
                        $element->setValues($attribute->getFrontend()->getSelectOptions());
                    }

/* [end] PfpjRom edit */


                }
            }

            if ($regionElement = $this->_form->getElement('region')) {
                $regionElement->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/customer_edit_renderer_region')
                );
            }
            if ($regionElement = $this->_form->getElement('region_id')) {
                $regionElement->setNoDisplay(true);
            }


/* [start] PfpjRom add */

            //$customerAddress = Mage::getModel('customer/address');
            $_attributes = $addressModel->getAttributes();

            if ($pfpjTipPersElement = $this->_form->getElement('pfpj_tip_pers')) {
				$pfpjTipPersElement->setRenderer(Mage::getModel('adminhtmlrom/customerrom_renderer_tippers'));
	    		$pfpjTipPersElement->setValues($_attributes['pfpj_tip_pers']->getSource()->getAllOptions());
	    		$pfpjTipPersElement->setValue($this->getTipPersDefaultValue($_attributes));
	        }

	        if ($pfpjForBillingElement = $this->_form->getElement('pfpj_for_billing')) {
	        	if ($this->getIsBilling()) {
					$pfpjForBillingElement->setRenderer(Mage::getModel('adminhtmlrom/customerrom_renderer_forbilling'));
					$value = $addressModel->getConfig()->getDefaultByOptionValue('pfpj_for_billing', $pfpjTipPersElement->getValue(), $this->getIsBilling() ? 'billing' : 'shipping');
					$pfpjForBillingElement->setValue($value);
	        	} else {
	        		$fieldset->removeField('pfpj_for_billing');
	        	}
	        }

	        if ($pfpjForShippingElement = $this->_form->getElement('pfpj_for_shipping')) {
	        	if ($this->getIsShipping()) {
					$pfpjForShippingElement->setRenderer(Mage::getModel('adminhtmlrom/customerrom_renderer_forshipping'));
					$value = $addressModel->getConfig()->getDefaultByOptionValue('pfpj_for_shipping', $pfpjTipPersElement->getValue(), $this->getIsShipping() ? 'shipping' : 'billing');
					$pfpjForShippingElement->setValue($value);
	        	} else {
	        		$fieldset->removeField('pfpj_for_shipping');
	        	}
	        }

	        if ($addressModel->getConfig()->isValidation('pfpj_cnp')) {
	        	if ($pfpjCnpElement = $this->_form->getElement('pfpj_cnp')) {
		        	$pfpjCnpElement->addClass('validate-pfpj-cnp');
		        }
	        }

	        if ($addressModel->getConfig()->isValidation('pfpj_cui')) {
	        	if ($pfpjCuiElement = $this->_form->getElement('pfpj_cui')) {
		        	$pfpjCuiElement->addClass('validate-pfpj-cif');
		        }
	        }

/* [end] PfpjRom add */


/* [start] PfpjRom edit */

            // 1.3.2.4			$this->_form->unsetValues($this->getEmptyValues($_attributes));
            // 1.4.0.1, 1.4.1.0 default	$this->_form->setValues($this->getFormValues());
            $this->_form->setValues($this->getEmptyValues($_attributes));

/* [end] PfpjRom edit */


        }
        return $this;
    }

    public function getEmptyValues($_attributes = null)
    {
    	$values = $this->getFormValues();

    	if (is_null($_attributes)) {
    		$_attributes = Mage::getModel('customer/address')->getAttributes();
    	}
    	if (!isset($values['pfpj_tip_pers']) || !(isset($values['pfpj_tip_pers']) && $values['pfpj_tip_pers'] != ""))
    		$values['pfpj_tip_pers'] = $this->getTipPersDefaultValue($_attributes);

    	if ($this->getIsBilling() && !isset($values['pfpj_for_billing']) || (isset($values['pfpj_for_billing']) && $values['pfpj_for_billing'] === "") || (isset($values['pfpj_for_billing']) && is_null($values['pfpj_for_billing'])))
    		$values['pfpj_for_billing'] = $this->getForBillingDefaultValue($values['pfpj_tip_pers'], $_attributes);

    	if ($this->getIsShipping() && !isset($values['pfpj_for_shipping']) || (isset($values['pfpj_for_shipping']) && $values['pfpj_for_shipping'] === "") || (isset($values['pfpj_for_shipping']) && is_null($values['pfpj_for_shipping'])))
    		$values['pfpj_for_shipping'] = $this->getForShippingDefaultValue($values['pfpj_tip_pers'], $_attributes);

    	return $values;
    }

    public function getTipPersDefaultValue($_attributes = null)
    {
    	$customerAddress = Mage::getModel('customer/address');
    	if (is_null($_attributes)) {
    		$_attributes = $customerAddress->getAttributes();
    	}
    	return $_attributes['pfpj_tip_pers']->getDefaultValue();
    }

    public function getForBillingDefaultValue($tippers_value, $_attributes = null)
    {
    	$customerAddress = Mage::getModel('customer/address');
    	if (is_null($_attributes)) {
    		$_attributes = $customerAddress->getAttributes();
    	}
    	return $customerAddress->getConfig()->getDefaultByOptionValue('pfpj_for_billing', $tippers_value, $this->getIsBilling() ? 'billing' : 'shipping');
    }

	public function getForShippingDefaultValue($tippers_value, $_attributes = null)
    {
    	$customerAddress = Mage::getModel('customer/address');
    	if (is_null($_attributes)) {
    		$_attributes = $customerAddress->getAttributes();
    	}
    	return $customerAddress->getConfig()->getDefaultByOptionValue('pfpj_for_shipping', $tippers_value, $this->getIsShipping() ? 'shipping' : 'billing');
    }
}