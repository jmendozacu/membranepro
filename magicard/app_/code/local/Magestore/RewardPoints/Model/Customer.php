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
 * Rewardpoints Customer Information Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_Customer extends Mage_Core_Model_Abstract
{
    /**
     * Redefine event Prefix, event object
     * 
     * @var string
     */
    protected $_eventPrefix = 'rewardpoints_customer';
    protected $_eventObject = 'rewardpoints_customer';
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/customer');
    }
    
    /**
     * Get Customer Model
     * 
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->hasData('customer')) {
            $this->setData('customer',
                Mage::getModel('customer/customer')->load($this->getData('customer_id'))
            );
        }
        return $this->getData('customer');
    }
}
