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
class Magestore_RewardPoints_Model_Total_Creditmemo_Point extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect total when create Creditmemo
     * 
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getRewardpointsDiscount() < 0.0001) {
            return ;
        }
        if ($this->isLast($creditmemo)) {
            $baseDiscount   = $order->getRewardpointsBaseDiscount();
            $discount       = $order->getRewardpointsDiscount();
            foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
                if ($baseDiscount > 0.0001) {
                    $baseDiscount   -= $existedCreditmemo->getRewardpointsBaseDiscount();
                    $discount       -= $existedCreditmemo->getRewardpointsDiscount();
                }
            }
        } else {
            $orderTotal = $order->getGrandTotal() + $order->getRewardpointsDiscount();
            $ratio      = $creditmemo->getGrandTotal() / $orderTotal;
            $baseDiscount   = $order->getRewardpointsBaseDiscount() * $ratio;
            $discount       = $order->getRewardpointsDiscount() * $ratio;
            
            $maxBaseDiscount   = $order->getRewardpointsBaseDiscount();
            $maxDiscount       = $order->getRewardpointsDiscount();
            foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
                if ($maxBaseDiscount > 0.0001) {
                    $maxBaseDiscount    -= $existedCreditmemo->getRewardpointsBaseDiscount();
                    $maxDiscount        -= $existedCreditmemo->getRewardpointsDiscount();
                }
            }
            if ($baseDiscount > $maxBaseDiscount) {
                $baseDiscount   = $maxBaseDiscount;
                $discount       = $maxDiscount;
            }
        }
        
        if ($baseDiscount > 0.0001) {
            if ($creditmemo->getBaseGrandTotal() <= $baseDiscount) {
                $creditmemo->setRewardpointsBaseDiscount($creditmemo->getBaseGrandTotal());
                $creditmemo->setRewardpointsDiscount($creditmemo->getGrandTotal());
                $creditmemo->setBaseGrandTotal(0.0);
                $creditmemo->setGrandTotal(0.0);
            } else {
                $creditmemo->setRewardpointsBaseDiscount($baseDiscount);
                $creditmemo->setRewardpointsDiscount($discount);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseDiscount);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $discount);
            }
            $creditmemo->setAllowZeroGrandTotal(true);
        }
    }
    
    /**
     * check credit memo is last or not
     * 
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return boolean
     */
    public function isLast($creditmemo)
    {
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }
}
