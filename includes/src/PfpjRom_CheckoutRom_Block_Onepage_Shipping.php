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
 * Overrides Checkout Opc Shipping
 *
 * @category   PfpjRom
 * @package    PfpjRom_CheckoutRom
 * @author     Daniel Ifrim
 */

class PfpjRom_CheckoutRom_Block_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    public function getTipPersRenderer($name = '', $html_id = '', $input_radio_class = 'radio', $label_radio_class = '', $html_attributes = array())
	{
		$attribute = $this->getAddress()->getAttributeByName('pfpj_tip_pers');
		$value = $this->getAddress()->getPfpjTipPers();
		if (is_null($value))
			//$value = $attribute->getDefaultValue();
			$value = 2; // PJ

		if ($name == '')
			$name = $attribute->getAttributeCode();
		if ($html_id == '')
			$html_id = $name;

		$renderer = $attribute->getFrontend()->getRenderer();
		$renderer->setValue($value);
		$renderer->setName($name);
		$renderer->setHtmlId($html_id);
		$renderer->addClass($input_radio_class);
		foreach ($html_attributes as $k => $v) {
			$renderer->setData($k, $v);
		}
		if ($label_radio_class != '')
			$renderer->addLabelClass($label_radio_class);
		
		$form = new Varien_Data_Form();
		$renderer->setForm($form);
		
		return $renderer;
	}
	
	public function getForShippingRenderer($tip_pers_value, $name = '', $html_id = '', $input_checkbox_class = 'checkbox', $label_checkbox_class = '')
	{
		$attribute = $this->getAddress()->getAttributeByName('pfpj_for_shipping');
		$value = 2;
		/*$value = $this->getAddress()->getPfpjForShipping();
		if ($value === "" || is_null($value)) {
			$value = $this->getConfig()->getDefaultByOptionValue('pfpj_for_shipping', $tip_pers_value, 'shipping');
		}*/
		
		if ($name == '')
			$name = $attribute->getAttributeCode();
		
		$is_checked = false;
		if ($value == 2)
			$is_checked = true;

		$renderer = $attribute->getFrontend()->getRenderer();
		$renderer->setValue($value);
		$renderer->setIsChecked($is_checked);
		$renderer->setName($name);
		$renderer->setHtmlId($html_id);
		$renderer->addClass($input_checkbox_class);
		if ($label_checkbox_class != '')
			$renderer->addLabelClass($label_checkbox_class);
		
		$form = new Varien_Data_Form();
		$renderer->setForm($form);
		
		return $renderer;
	}
	
	public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
            	if ($address->getPfpjForShipping() == 1)
	                $options[] = array(
	                    'value'=>$address->getId(),
	                    'label'=>$address->format('oneline')
	                );
            }

            $addressId = $this->getAddress()->getId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }
	
    /**
	 * Gets sales quote address model config(customerrom address config).
	 *
	 * @return PfpjRom_CustomerRom_Model_Address_Config
	 */
	public function getConfig()
	{
		return $this->getAddress()->getConfig();
	}
}