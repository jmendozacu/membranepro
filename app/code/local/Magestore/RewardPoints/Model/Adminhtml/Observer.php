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
 * RewardPoints Adminhtml Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_Adminhtml_Observer
{
    /**
     * process event: adminhtml_customer_save_after
     * 
     * @param type $observer
     */
    public function customerSaveAfter($observer)
    {
        $customer   = $observer['customer'];
        $params     = Mage::app()->getRequest()->getParam('rewardpoints');
        if (empty($params['admin_editing'])) {
            return ;
        }
        
        // Update reward account settings
        $rewardAccount = Mage::getModel('rewardpoints/customer')->load($customer->getId(), 'customer_id');
        $rewardAccount->setCustomerId($customer->getId());
        if (!$rewardAccount->getId()) {
            $rewardAccount->setData('point_balance', 0)
                ->setData('holding_balance', 0)
                ->setData('spent_balance', 0);
        }
        $params['is_notification'] = empty($params['is_notification']) ? 0 : 1;
        $params['expire_notification'] = empty($params['expire_notification']) ? 0 : 1;
        $rewardAccount->setData('is_notification', $params['is_notification'])
            ->setData('expire_notification', $params['expire_notification']);
        $rewardAccount->save();
        
        // Create transactions for customer if need
        if (!empty($params['change_balance'])) {
            try {
                Mage::helper('rewardpoints/action')->addTransaction('admin',
                    $customer,
                    new Varien_Object(array(
                        'point_amount'  => $params['change_balance'],
                        'title'         => $params['change_title'],
                        'expiration_day'=> (int)$params['expiration_day'],
                    ))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('rewardpoints')->__("An error occurred while changing the customer's point balance.")
                );
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }
    
    /**
     * process event to force create credit memo when purchase order by points
     * 
     * @param type $observer
     */
    public function salesOrderLoadAfter($observer)
    {
        $order = $observer['order'];
        if ($order->getRewardpointsDiscount() < 0.0001
            || Mage::app()->getStore()->roundPrice($order->getGrandTotal()) > 0
            || $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED
            || $order->isCanceled()
            || $order->canUnhold()
        ) {
            return $this;
        }
        foreach ($order->getAllItems() as $item) {
            if (($item->getQtyInvoiced() - $item->getQtyRefunded() - $item->getQtyCanceled()) > 0) {
                $order->setForcedCanCreditmemo(true);
                return $this;
            }
        }
    }
    
    /**
     * process event to turn off forced credit memo of order
     * 
     * @param type $observer
     */
    public function salesOrderCreditmemoRefund($observer)
    {
        $creditmemo = $observer['creditmemo'];
        $order      = $creditmemo->getOrder();
        if ($order->getRewardpointsDiscount() && $order->getForcedCanCreditmemo()) {
            $order->setForcedCanCreditmemo(false);
        }
    }
}
