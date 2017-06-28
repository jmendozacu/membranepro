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
 * CustomerRom pfpj_tip_pers attribute frontend
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_CustomerRom_Model_Entity_Address_Attribute_Frontend_Tippers extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
	public function getRenderer() {
		/*$renderer = Mage::getModel($this->getInputRendererClass(),
								   array('values' => $this->getAttribute()->getSource()->getAllOptions()));*/
		/*$renderer = Mage::getModel("customerrom/address_renderer_tippers",
								   array('values' => $this->getAttribute()->getSource()->getAllOptions()));*/
		
		$renderer = Mage::getBlockSingleton("customerrom/address_renderer_tippers");
		$renderer->setData('values', $this->getAttribute()->getSource()->getAllOptions());
		
		return $renderer;
	}
	
	public function getInputRendererClass()
	{
		if ($className = "customerrom/address_renderer_tippers") {
            return Mage::getConfig()->getBlockClassName($className);
        }
        return null;
	}
}