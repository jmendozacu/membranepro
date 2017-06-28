<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPoints Calculator Helper
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Helper_Calculator extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ROUNDING_METHOD  = 'rewardpoints/earning/rounding_method';
    
    /**
     * Rounding number by reward points configuration
     * 
     * @param mixed $number
     * @param mixed $store
     * @return int
     */
    public function round($number, $store = null)
    {
        switch (Mage::getStoreConfig(self::XML_PATH_ROUNDING_METHOD, $store)) {
            case 'floor':
                return floor($number);
            case 'ceil':
                return ceil($number);
        }
        return round($number);
    }
}
