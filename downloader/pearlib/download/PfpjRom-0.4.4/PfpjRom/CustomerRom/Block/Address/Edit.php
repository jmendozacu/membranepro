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
 * Overrides Customer Address Edit Block
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_CustomerRom_Block_Address_Edit extends Mage_Customer_Block_Address_Edit
{
	public function getTipPersRenderer($name = '', $input_radio_class = 'radio', $label_radio_class = '')
	{
		$attribute = $this->getAddress()->getAttributeByName('pfpj_tip_pers');
		$value = $this->getAddress()->getPfpjTipPers();
		if (is_null($value))
			$value = $attribute->getDefaultValue();

		if ($name == '')
			$name = $attribute->getAttributeCode();

		$renderer = $attribute->getFrontend()->getRenderer();
		$renderer->setValue($value);
		$renderer->setName($name);
		$renderer->setHtmlId($name);
		$renderer->addClass($input_radio_class);
		if ($label_radio_class != '')
			$renderer->addLabelClass($label_radio_class);

		$form = new Varien_Data_Form();
		$renderer->setForm($form);
		
		return $renderer;
	}
	
	public function getForBillingRenderer($tip_pers_value, $is_default_billing = false, $name = '', $input_checkbox_class = 'checkbox', $label_checkbox_class = '')
	{
		$attribute = $this->getAddress()->getAttributeByName('pfpj_for_billing');
		if ($is_default_billing == true)
			$value = 1;
		else
			$value = $this->getAddress()->getPfpjForBilling();

		if ($value === "" || is_null($value)) {
			$value = $this->getConfig()->getDefaultByOptionValue('pfpj_for_billing', $tip_pers_value, 'billing');
		}

		if ($name == '')
			$name = $attribute->getAttributeCode();

		$is_checked = false;
		if ($value == 1)
			$is_checked = true;

		$renderer = $attribute->getFrontend()->getRenderer();
		$renderer->setValue($value);
		$renderer->setIsChecked($is_checked);
		$renderer->setName($name);
		$renderer->setHtmlId($name);
		$renderer->addClass($input_checkbox_class);
		if ($label_checkbox_class != '')
			$renderer->addLabelClass($label_checkbox_class);
		
		$form = new Varien_Data_Form();
		$renderer->setForm($form);
		
		return $renderer;
	}
	
	public function getForShippingRenderer($tip_pers_value, $is_default_shipping = false, $name = '', $input_checkbox_class = 'checkbox', $label_checkbox_class = '')
	{
		$attribute = $this->getAddress()->getAttributeByName('pfpj_for_shipping');
		if ($is_default_shipping == true)
			$value = 1;
		else
			$value = $this->getAddress()->getPfpjForShipping();
			
		if ($value === "" || is_null($value)) {
			$value = $this->getConfig()->getDefaultByOptionValue('pfpj_for_shipping', $tip_pers_value, 'shipping');
		}
		
		if ($name == '')
			$name = $attribute->getAttributeCode();
		
		$is_checked = false;
		if ($value == 1)
			$is_checked = true;

		$renderer = $attribute->getFrontend()->getRenderer();
		$renderer->setValue($value);
		$renderer->setIsChecked($is_checked);
		$renderer->setName($name);
		$renderer->setHtmlId($name);
		$renderer->addClass($input_checkbox_class);
		if ($label_checkbox_class != '')
			$renderer->addLabelClass($label_checkbox_class);
		
		$form = new Varien_Data_Form();
		$renderer->setForm($form);
		
		return $renderer;
	}
	
	/**
	 * Gets customer address model config.
	 *
	 * @return PfpjRom_CustomerRom_Model_Address_Config
	 */
	public function getConfig()
	{
		return $this->getAddress()->getConfig();
	}
	
	/*public function getFieldName($key) {
		$_attributes = $this->getAddress()->getAttributes();
		return $_attributes[$key]->getName();
	}*/
}