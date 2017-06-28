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
 * Custmer addresses forms.
 * Overrides tippers behaviour.
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_AdminhtmlRom_Block_CustomerRom_Edit_Tab_Addresses extends Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses
{
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customerrom/tab/addresses.phtml');
    }

    public function initForm()
    {
    	parent::initForm();
    	$form = $this->getForm();

    	$customerAddress = Mage::getModel('customer/address');
    	if ($pfpjTipPersElement = $form->getElement('pfpj_tip_pers')) {
			$_attribute = $customerAddress->getAttributeByName('pfpj_tip_pers');
			$pfpjTipPersElement->setRenderer(Mage::getModel('adminhtmlrom/customerrom_renderer_tippers'));
    		$pfpjTipPersElement->setValues($_attribute->getSource()->getAllOptions());
    		$pfpjTipPersElement->setValue($_attribute->getDefaultValue());
        }

        if ($pfpjForBillingElement = $form->getElement('pfpj_for_billing')) {
			//$_attribute = $customerAddress->getAttributeByName('pfpj_for_billing');
			$pfpjForBillingElement->setRenderer(Mage::getModel('adminhtmlrom/customerrom_renderer_forbilling'));
			$value = $customerAddress->getConfig()->getDefaultByOptionValue('pfpj_for_billing', $pfpjTipPersElement->getValue(), 'all');
			$pfpjForBillingElement->setValue($value);
        }

        if ($pfpjForShippingElement = $form->getElement('pfpj_for_shipping')) {
			//$_attribute = $customerAddress->getAttributeByName('pfpj_for_shipping');
			$pfpjForShippingElement->setRenderer(Mage::getModel('adminhtmlrom/customerrom_renderer_forshipping'));
			$value = $customerAddress->getConfig()->getDefaultByOptionValue('pfpj_for_shipping', $pfpjTipPersElement->getValue(), 'all');
			$pfpjForShippingElement->setValue($value);
        }

        if ($customerAddress->getConfig()->isValidation('pfpj_cnp')) {
	        if ($pfpjCnpElement = $form->getElement('pfpj_cnp')) {
	        	$pfpjCnpElement->addClass('validate-pfpj-cnp');
	        }
        }

        if ($customerAddress->getConfig()->isValidation('pfpj_cui')) {
	        if ($pfpjCuiElement = $form->getElement('pfpj_cui')) {
	        	$pfpjCuiElement->addClass('validate-pfpj-cif');
	        }
        }

        $this->setForm($form);

        return $this;
    }

    public function getEmptyValues()
    {
    	$values = array();

    	$customerAddress = Mage::getModel('customer/address');

    	$_attributes = $customerAddress->getAttributes();
    	$values['pfpj_tip_pers'] = $_attributes['pfpj_tip_pers']->getDefaultValue();
    	$values['pfpj_for_billing'] = $customerAddress->getConfig()->getDefaultByOptionValue('pfpj_for_billing', $values['pfpj_tip_pers'], 'all');
    	$values['pfpj_for_shipping'] = $customerAddress->getConfig()->getDefaultByOptionValue('pfpj_for_shipping', $values['pfpj_tip_pers'], 'all');

    	return $values;
    }
}