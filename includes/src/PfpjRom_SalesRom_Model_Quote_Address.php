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
 * @package    PfpjRom_SalesRom
 * @copyright  Copyright (c) Daniel Ifrim
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Overrides Sales Quote address model
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_SalesRom_Model_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    protected function _beforeSave()
    {
    	$this->_cleanAddress();
        parent::_beforeSave();
    }
    
    protected function _cleanAddress()
    {
    	if (is_null($this->getPfpjTipPers())) {
    		$this->setPfpjTipPers($this->getAttributeByName('pfpj_tip_pers')->getDefaultValue());
    	}
    	$customerAddress = Mage::getModel('customerrom/address');
		foreach ($this->getConfig()->getFields() as $_fieldName => $_fieldConfig) {
			if (in_array($_fieldName, array('pfpj_for_billing', 'pfpj_for_shipping')))
    			continue;
	    	if (!$customerAddress->showField($_fieldName, $this->getPfpjTipPers(), $this->getPfpjForBilling(), $this->getPfpjForShipping()))
	    		$this->setData($_fieldName, "");
		}
		//$this->setData("pfpj_cui", $this->getData("pfpj_for_billing"));
    }

    public function getAttributeByName($attribute_code)
    {
    	$addressModel = $this->exportCustomerAddress();
		$attribute = $addressModel->getAttributeByName($attribute_code);
		return $attribute;
    }
    
    function isAttributeRequired($name)
    {
    	return $addressModel = $this->exportCustomerAddress()->isAttributeRequired($name);
    }
    
    public function validate()
    {
    	$addressModel = $this->exportCustomerAddress();
    	return $addressModel->validate();
    }
}