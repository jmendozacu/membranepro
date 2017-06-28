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
 * Overrides Customer Address Helper
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_CustomerRom_Helper_Tippers extends Mage_Core_Helper_Abstract
{
	public function getJsonConfig($state = 'all', $prefixId = "", $suffixId = "")
    {
    	$config = array();
    	$configModel = $this->getConfig();
    	
    	$config["trigger"] = $prefixId.'pfpj_tip_pers';
        
    	$options = array();
    	$configModelOptions = $configModel->getOptions();
		foreach ($configModel->getOptions() as $_optionConfig)
			$options[$_optionConfig->getValue()] = $configModel->isOptionDefaultByValue($_optionConfig->getValue());
		$config["options"] = $options;
		
		foreach ($configModel->getOptions() as $_optionConfig) {
			if ($configModel->isOptionDefaultByValue($_optionConfig->getValue())) {
				$config["default_option"] = $_optionConfig->getValue();
				break;
			}
		}
		
		$fields = array();
		foreach ($configModel->getFields() as $_fieldConfig) {
			if (!$configModel->isFieldDisabled($_fieldConfig->getName())) {
				$_fieldOptions = array();
				foreach ($_fieldConfig->getOptions() as $_fieldOptionName => $_fieldOptionConfig) {
					$_optionValue = $configModelOptions[$_fieldOptionName]->getValue();
					$_fieldOptions[$_optionValue] = array();
					
					if ($state == 'billing' && $_fieldConfig->getName() == 'pfpj_for_billing') {
						$_fieldOptions[$_optionValue]["billing"] = array(
							"show" => $_fieldOptionConfig["billing"]->getShow(),
							"required" => $_fieldOptionConfig["billing"]->getRequired(),
							"defaultValue" => $_fieldOptionConfig["billing"]->getDefault());
						if ($state == 'billing') {
							$_fieldOptions[$_optionValue]["billing"]["show"] = 0;
							$_fieldOptions[$_optionValue]["billing"]["defaultValue"] = 1;
						}
					} elseif ($state == 'shipping' && $_fieldConfig->getName() == 'pfpj_for_shipping') {
						$_fieldOptions[$_optionValue]["shipping"] = array(
							"show" => $_fieldOptionConfig["shipping"]->getShow(),
							"required" => $_fieldOptionConfig["shipping"]->getRequired(),
							"defaultValue" => $_fieldOptionConfig["shipping"]->getDefault());
						if ($state == 'shipping') {
							$_fieldOptions[$_optionValue]["shipping"]["show"] = 0;
							$_fieldOptions[$_optionValue]["shipping"]["defaultValue"] = 1;
						}
					} else if($state == 'all' || !(($state == 'billing' && $_fieldConfig->getName() == 'pfpj_for_shipping') || ($state == 'shipping' && $_fieldConfig->getName() == 'pfpj_for_billing'))) {
						$_fieldOptions[$_optionValue] = array(
							"billing" => array(
								"show" => $_fieldOptionConfig["billing"]->getShow(),
								"required" => $_fieldOptionConfig["billing"]->getRequired(),
								"defaultValue" => $_fieldOptionConfig["billing"]->getDefault()),
							"shipping" => array(
								"show" => $_fieldOptionConfig["shipping"]->getShow(),
								"required" => $_fieldOptionConfig["shipping"]->getRequired(),
								"defaultValue" => $_fieldOptionConfig["shipping"]->getDefault()),
						);
					}
				}
				$fields[$prefixId.$_fieldConfig->getName().$suffixId] = $_fieldOptions;
			}
		}
		$config["fields"] = $fields;
		
        return Zend_Json::encode($config);
    }
    
    public function getConfig()
    {
    	return Mage::getSingleton("customer/address_config");
    }
}