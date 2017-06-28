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
 * Rewardpoints Spend for Order by Point Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_Total_Quote_Earning
    // extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Change collect total to Event to ensure earning is last runned total
     * 
     * @param type $observer
     */
    public function salesQuoteCollectTotalsAfter($observer)
    {
        $quote = $observer['quote'];
        foreach ($quote->getAllAddresses() as $address) {
            if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
                continue;
            }
            $this->collect($address, $quote);
        }
    }
    
    /**
     * collect reward points that customer earned (per each item and address) total
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @param Mage_Sales_Model_Quote $quote
     * @return Magestore_RewardPoints_Model_Total_Quote_Point
     */
    public function collect($address, $quote)
    {
        if (!Mage::helper('rewardpoints')->isEnable($quote->getStoreId())) {
            return $this;
        }
        // get points that customer can earned by Rates
        $earningPoints = Mage::helper('rewardpoints/calculation_earning')->getRateEarningPoints(
            $quote->getBaseGrandTotal(),
            $quote->getStoreId()
        );
        if ($earningPoints > 0) {
            $address->setRewardpointsEarn($earningPoints);
        }
        
        Mage::dispatchEvent('rewardpoints_collect_earning_total_points_before', array(
            'address'   => $address,
        ));
        
        // Update earning point for each items
        $this->_updateEarningPoints($address);
        
        Mage::dispatchEvent('rewardpoints_collect_earning_total_points_after', array(
            'address'   => $address,
        ));
        
        return $this;
    }
    
    /**
     * update earning points for address items
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Magestore_RewardPoints_Model_Total_Quote_Earning
     */
    protected function _updateEarningPoints($address)
    {
        $items = $address->getAllItems();
        $earningPoints = $address->getRewardpointsEarn();
        if (!count($items) || $earningPoints <= 0) {
            return $this;
        }
        
        // Calculate total item prices
        $baseItemsPrice = 0;
        $totalItemsQty  = 0;
        $isBaseOnQty    = false;
        foreach ($items as $item) {
            if ($item->getParentItemId()) continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemsPrice += $item->getQty() * ($child->getQty() * $child->getBasePriceInclTax() - $child->getBaseDiscountAmount());
                    $totalItemsQty += $item->getQty() * $child->getQty();
                }
            } elseif ($item->getProduct()) {
                $baseItemsPrice += $item->getQty() * $item->getBasePriceInclTax() - $item->getBaseDiscountAmount();
                $totalItemsQty += $item->getQty();
            }
        }
        $baseItemsPrice += $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount();
        if ($baseItemsPrice < 0.0001) {
            $isBaseOnQty = true;
        }
        
        // Update for items
        foreach ($items as $item) {
            if ($item->getParentItemId()) continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemPrice = $item->getQty() * ($child->getQty() * $child->getBasePriceInclTax() - $child->getBaseDiscountAmount());
                    $itemQty = $item->getQty() * $child->getQty();
                    if ($isBaseOnQty) {
                        $itemEarning = round($itemQty * $earningPoints / $totalItemsQty, 0, PHP_ROUND_HALF_DOWN);
                    } else {
                        $itemEarning = round($baseItemPrice * $earningPoints / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                    }
                    $child->setRewardpointsEarn($itemEarning);
                }
            } elseif ($item->getProduct()) {
                $baseItemPrice = $item->getQty() * $item->getBasePriceInclTax() - $item->getBaseDiscountAmount();
                $itemQty = $item->getQty();
                if ($isBaseOnQty) {
                    $itemEarning = round($itemQty * $earningPoints / $totalItemsQty, 0, PHP_ROUND_HALF_DOWN);
                } else {
                    $itemEarning = round($baseItemPrice * $earningPoints / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                }
                $item->setRewardpointsEarn($itemEarning);
            }
        }
        
        return $this;
    }
}
