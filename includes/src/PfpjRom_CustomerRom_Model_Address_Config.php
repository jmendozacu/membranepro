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
 * Extends default config for address with some methods for tippers.
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_CustomerRom_Model_Address_Config extends Mage_Customer_Model_Address_Config
{
	protected $_tippers_options;
    protected $_tippers_fields;

    public function __construct()
    {
        parent::__construct();
        $this->getOptions();
        $this->getFields();
    }

	public function isFieldDisabled($name)
	{
		return $this->_tippers_fields[$name]->getDisabled();
	}

	public function getOptionByValue($value) {
		$return = null;
		foreach ($this->_tippers_options as $_key => $_option) {
			if (intval($_option->getValue()) === intval($value)) {
				$return = $_option;
				break;
			}
		}
		return $return;
	}

	public function isFieldRequired($name)
	{
		return $this->_tippers_fields[$name]->getRequired();
	}

	public function isValidation($name)
	{
		return $this->_tippers_fields[$name]->getIsValidation();
	}

	public function getDefaultValue($name)
	{
		return $this->_tippers_fields[$name]->getDefault();
	}

	public function isFieldRequiredByOption($name, $option_value, $state)
	{
		$_fieldOption = $this->_tippers_fields[$name]->getOptions();
		$_fieldOption = $_fieldOption[$this->getOptionByValue($option_value)->getName()];
		if ($state != 'all')
			$required = $_fieldOption[$state]->getRequired();
		else
			$required = $_fieldOption['billing']->getRequired() || $_fieldOption['shipping']->getRequired();
		return $required;
	}

	public function showField($name, $option_value, $state)
	{
		$_fieldOption = $this->_tippers_fields[$name]->getOptions();
		$_fieldOption = $_fieldOption[$this->getOptionByValue($option_value)->getName()];
		if ($state != 'all')
			$show = $_fieldOption[$state]->getShow();
		else
			$show = $_fieldOption['billing']->getShow() || $_fieldOption['shipping']->getShow();
		return $show;
	}

	public function isOptionDefaultByValue($option_value)
	{
		return $this->getOptionByValue($option_value)->getDefault();
	}

	public function getDefaultByOptionValue($name, $option_value, $state)
	{
		$_fieldOption = $this->_tippers_fields[$name]->getOptions();
		$_fieldOption = $_fieldOption[$this->getOptionByValue($option_value)->getName()];
		if ($state != 'all')
			$default = $_fieldOption[$state]->getDefault();
		else
			$default = $_fieldOption['billing']->getDefault() || $_fieldOption['shipping']->getDefault();
		return $default;
	}

	/**
	 * Get confgured options for tippers.
	 *
	 * @return array()
	 */
	public function getOptions()
	{
		if(is_null($this->_tippers_options)) {
			$constant_class = get_class(Mage::getModel("customerrom/entity_address_attribute_source_tippers"));
			foreach($this->getNode('tippers/options')->children() as $optionName=>$optionConfig) {
				$option = new Varien_Object();
                $option->setName($optionName)->
                	setConst($constant_class."::".strtoupper((string)$optionConfig->const))->
                	setValue(constant($option->getConst()))->
                	setDefault($optionConfig->is('default'));

                $this->_tippers_options[$optionName] = $option;
			}
		}
		return $this->_tippers_options;
	}

	/**
	 * Get attached/configured fields.
	 *
	 * @return array()
	 */
	public function getFields()
	{
		if(is_null($this->_tippers_fields)) {
            $this->_tippers_fields = array();
            foreach($this->getNode('tippers/fields')->children() as $fieldName=>$fieldConfig) {
                $field = new Varien_Object();
                $fieldOptions = array();
                $field->setName($fieldName)
                    ->setDisabled($fieldConfig->is('disabled'))
                    ->setOptions($fieldOptions);

				$emptyValue = ''; $nonEmptyValue = $emptyValue;
				if ($fieldName == 'pfpj_for_billing' || $fieldName == 'pfpj_for_shipping') {
					$emptyValue = 0;
					$nonEmptyValue = 1;
				}

				foreach ($this->_tippers_options as $_option) {
					$optionName = $_option->getName();
					if (($fieldOption = $fieldConfig->$optionName)) {
						$fieldOptions[$optionName]['billing'] = new Varien_Object();
						$fieldOptions[$optionName]['billing']->setShow($fieldOption->billing->is('show'))
							->setRequired(($fieldOption->billing->is('show') == false ? false : $fieldOption->billing->is('required')))
							->setDefault((empty($fieldOption->billing->default) ? $emptyValue : (int) $fieldOption->billing->default));
						$fieldOptions[$optionName]['shipping'] = new Varien_Object();
						$fieldOptions[$optionName]['shipping']->setShow($fieldOption->shipping->is('show'))
							->setRequired(($fieldOption->shipping->is('show') == false ? false : $fieldOption->shipping->is('required')))
							->setDefault((empty($fieldOption->shipping->default) ? $emptyValue : (int) $fieldOption->shipping->default));
					} else {
						$fieldOptions[$optionName]['billing'] = new Varien_Object();
						$fieldOptions[$optionName]['billing']->setShow(false)
							->setRequired(false)
							->setDefault($emptyValue);
						$fieldOptions[$optionName]['shipping'] = new Varien_Object();
						$fieldOptions[$optionName]['shipping']->setShow(false)
							->setRequired(false)
							->setDefault($emptyValue);
					}
				}

				if ((int)$fieldConfig->is->disabled !== 0 && !$field->getDisabled()) {
					$show = false;
					foreach ($fieldOptions as $fieldOption) {
						if ($fieldOption['billing']->getShow() || $fieldOption['shipping']->getShow()) {
							$show = true;
							break;
						}
					}
					if (!$show)
						$field->setDisabled(true);
				}

				if ($field->getDisabled()) {
					foreach ($fieldOptions as $fieldOption) {
						$fieldOption['billing']->setShow(false);
						$fieldOption['billing']->setRequired(false);
						$fieldOption['billing']->setDefault($emptyValue);
						$fieldOption['shipping']->setShow(false);
						$fieldOption['shipping']->setRequired(false);
						$fieldOption['shipping']->setDefault($emptyValue);
					}
					$field->setRequired(false);
					$field->setDefault($emptyValue);
				} else {
					$required = (true && count($fieldOptions) == count($this->_tippers_options));
					foreach ($fieldOptions as $fieldOption) {
						if (!$fieldOption['billing']->getRequired() || !$fieldOption['shipping']->getRequired()) {
							$required = false;
						}
					}
					$field->setRequired($required);

					$fieldDefaultValue = (count($fieldOptions) > 0 ? $nonEmptyValue : $emptyValue);
					if ($fieldDefaultValue != $emptyValue && ($fieldName == 'pfpj_for_billing' || $fieldName == 'pfpj_for_shipping')) {
						// not empty(1) for both legality and natural - and
						foreach ($fieldOptions as $fieldOption) {
							// not empty(1) at least for billing or shipping - or
							if (!($fieldOption['billing']->getDefault() == $nonEmptyValue || $fieldOption['shipping']->getDefault() == $nonEmptyValue)) {
								$fieldDefaultValue = $emptyValue;
								break;
							}
						}
					}
					$field->setDefault($fieldDefaultValue); // use on addresses that might be both for shipping and billing
				}

				$field->setIsValidation(false);
				if (!$field->getDisabled()) {
                	if ((int)$fieldConfig->is('validation')) {
						$field->setIsValidation(true);
                	}
                }

				$field->setOptions($fieldOptions);
                $this->_tippers_fields[$fieldName] = $field;
            }
        }

        return $this->_tippers_fields;
	}
}