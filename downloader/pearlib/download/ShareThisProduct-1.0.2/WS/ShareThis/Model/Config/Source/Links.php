<?php
/**
 * Willouhby Stewart
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
 * @category    WS
 * @package     WS_ShareThis
 * @copyright   Copyright (c) 2010 Willoughby Stewart (http://www.wsa.net.uk)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ShareThis list of functionality
 * 
 * @category   WS
 * @package    WS_ShareThis
 * @author     Willoughby Stewart <digitalmedia@wsa.net.uk>
 */
class WS_ShareThis_Model_Config_Source_Links extends Mage_Core_Model_Config_Data
{
    protected $_options;

    public function toOptionArray($isMultiselect)
    {
        if (!$this->_options) {
            $this->_options = array(
            	array('value' => 'sharethis', 'label'=>Mage::helper('sharethis')->__('ShareThis hook')),
	            array('value' => 'facebook', 'label'=>Mage::helper('sharethis')->__('Facebook')),
	            array('value' => 'twitter', 'label'=>Mage::helper('sharethis')->__('Twitter')),
	            array('value' => 'myspace', 'label'=>Mage::helper('sharethis')->__('My Space')),
	            array('value' => 'email', 'label'=>Mage::helper('sharethis')->__('Email'))
	        );
        }
        $options = $this->_options;
        return $options;
    }
}
