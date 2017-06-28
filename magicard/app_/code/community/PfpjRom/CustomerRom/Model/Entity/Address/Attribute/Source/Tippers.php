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
 * CustomerRom pfpj_tip_pers attribute source
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_CustomerRom_Model_Entity_Address_Attribute_Source_Tippers extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
	const NATURAL_PERSON = 1;
    const LEGAL_ENTITY = 2;
    
    public function getNaturalPersonValue()
    {
    	return self::NATURAL_PERSON;
    }
    
    public function getLegalEntityValue()
    {
    	return self::LEGAL_ENTITY;
    }
    
    public function isNatualPerson($value)
    {
    	return (intval($value) === self::NATURAL_PERSON || empty($value) ? true : false);
    }
    
    public function isLegalEntity($value)
    {
    	return !$this->isNatualPerson($value);
    }
    
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
            					    0 => array('value' => self::LEGAL_ENTITY,
            					    		   'label' => Mage::helper('customerrom')->__('Legal Entity')),
            					    /*1 => array('value' => self::NATURAL_PERSON,
            								   'label' => Mage::helper('customerrom')->__('Natural Person'))*/
            					    );
        }
        return $this->_options;
    }

    public function getOptionText($value)
    {
        if (!$this->_options) {
          $this->_options = $this->getAllOptions();
        }
        foreach ($this->_options as $option) {
          if ($option['value'] == $value) {
            return $option['label'];
          }
        }
        return false;
    }
}