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
 * Setup model used at installation time
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_CustomerRom_Model_Entity_Setup extends Mage_Customer_Model_Entity_Setup
{
    /**
     * Gets tippers config.
     *
     * @return PfpjRom_CustomerRom_Model_Address_Tippers_Config
     */
    public function getTippersConfig() {
    	return Mage::getSingleton("customer/address_config");
    }
    
    /**
     * Gets first option_id for an attribute.
     *
     * @param array $attribute
     * @param string $option_value
     * @return int option_id
     */
    public function getAttributeOptionIdByValue($attribute, $option_value)
    {
    	$intOptionId = null;
		$optionTable        = $this->getTable('eav/attribute_option');
		$optionValueTable   = $this->getTable('eav/attribute_option_value');
		$select = $this->getConnection()->select()
	        ->from(array('ao' => $optionTable), array(
	            	'option_id'	=> 'option_id'))
			->joinLeft(array('aov' => $optionValueTable),
					"`ao`.`option_id`=`aov`.`option_id`",
					array())
            ->where('ao.attribute_id=?', $attribute['attribute_id'])
            ->where('aov.store_id=?', 0)
            ->where('aov.value=?', $option_value)
            ->limit(1);

	    $query = $this->getConnection()->query($select);
		$row = $query->fetch();
		if (isset($row['option_id']))
			$intOptionId = (int) $row['option_id'];
		
		return $intOptionId;
    }
}