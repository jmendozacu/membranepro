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
 * Block that display the ShareThis links 
 * 
 * @category   WS
 * @package    WS_ShareThis
 * @author     Willoughby Stewart <digitalmedia@wsa.net.uk>
 */
class WS_ShareThis_Block_Links extends Mage_Core_Block_Template
{	   
	/**
     * Check whether the link code is enabled using the backend configuration
     *
     * @return boolean
     */
    public function hasLinkActive($linkCode) {    	
    	$functionality = Mage::getStoreConfig("catalog/sharethis/links");    
       	return in_array($linkCode,explode(',',$functionality));
    }
    
    /**
     * Return the publisher key that is created on the ShareThis website 
     * http://sharethis.com/publishers/get-sharing-button
     **/
    public function getKey() {
    	return Mage::getModel('sharethis/setup')->getPublisherKey();
    }
}