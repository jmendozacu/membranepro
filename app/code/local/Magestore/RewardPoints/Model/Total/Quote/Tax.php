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
 * Rewardpoints Rewrite to caculate taxt for discount
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_Total_Quote_Tax extends Mage_Tax_Model_Sales_Total_Quote_Tax
{
    
    /**
     * Calculate tax for Quote (total)
     * 
     * @param type $item
     * @param type $rate
     * @param type $taxGroups
     * @return Magestore_RewardPoints_Model_Total_Quote_Tax
     */
	 /*
    protected function _aggregateTaxPerRate($item, $rate, &$taxGroups) {
        $discount       = $item->getDiscountAmount();
        $baseDiscount   = $item->getBaseDiscountAmount();
        if($item->getIsPriceInclTax()){
            $item->setRewardPointsDiscountTax($this->_calculator->calcTaxAmount($item->getRewardpointsDiscount(), $rate, false, false));
            $item->setRewardPointsBaseDiscountTax($this->_calculator->calcTaxAmount($item->getRewardpointsBaseDiscount(), $rate, false, false));
        }
        $item->setDiscountAmount($discount+$item->getAffiliateplusAmount()+ $item->getRewardpointsDiscount() + $item->getRewardPointsDiscountTax());
        $item->setBaseDiscountAmount($baseDiscount+$item->getBaseAffiliateplusAmount()+ $item->getRewardpointsDiscount() + $item->getRewardPointsBaseDiscountTax());
        
        parent::_aggregateTaxPerRate($item, $rate, $taxGroups);
        
        $afterDiscount = (bool)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $this->_store);
        if($item->getIsPriceInclTax() && $afterDiscount){
            foreach ($this->_hiddenTaxes as $key => $taxInfoItem) {
                if (isset($taxInfoItem['item']) && $item->getId() == $taxInfoItem['item']->getId() && $taxInfoItem['value'] >= $item->getRewardPointsDiscountTax() && $item->getRewardPointsDiscountTax() >0) {
                    $this->_hiddenTaxes[$key]['value'] = $taxInfoItem['value'] - $item->getRewardPointsDiscountTax();
                    $this->_hiddenTaxes[$key]['base_value'] = $taxInfoItem['base_value'] - $item->getRewardPointsBaseDiscountTax();
                    break;
                }
            }
            //fix 1.4
            if($item->getHiddenTaxAmount()){
                $item->setHiddenTaxAmount($item->getHiddenTaxAmount() - $item->getRewardPointsDiscountTax());
                $item->setBaseHiddenTaxAmount($item->getBaseHiddenTaxAmount() - $item->getRewardPointsBaseDiscountTax());
            }
        }
        
        $item->setDiscountAmount($discount);
        $item->setBaseDiscountAmount($baseDiscount);
        return $this;
    }
    */
    /**
     * Calculate tax for each product
     * 
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param type $rate
     * @return Magestore_RewardPoints_Model_Total_Quote_Tax
     */
	 /*
    protected function _calcUnitTaxAmount(Mage_Sales_Model_Quote_Item_Abstract $item, $rate) {
        $discount       = $item->getDiscountAmount();
        $baseDiscount   = $item->getBaseDiscountAmount();
        if($item->getIsPriceInclTax()){
            $item->setRewardPointsDiscountTax($this->_calculator->calcTaxAmount($item->getRewardpointsDiscount(), $rate, false, false));
            $item->setRewardPointsBaseDiscountTax($this->_calculator->calcTaxAmount($item->getRewardpointsBaseDiscount(), $rate, false, false));
        }
        $item->setDiscountAmount($discount+$item->getAffiliateplusAmount()+ $item->getRewardpointsDiscount() + $item->getRewardPointsDiscountTax());
        $item->setBaseDiscountAmount($baseDiscount+$item->getBaseAffiliateplusAmount()+ $item->getRewardpointsDiscount() + $item->getRewardPointsBaseDiscountTax());
        
        parent::_calcUnitTaxAmount($item, $rate);
        
        $afterDiscount = (bool)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $this->_store);
        if($item->getIsPriceInclTax() && $afterDiscount){
            foreach ($this->_hiddenTaxes as $key => $taxInfoItem) {
                if (isset($taxInfoItem['item']) && $item->getId() == $taxInfoItem['item']->getId() && $taxInfoItem['value'] >= $item->getRewardPointsDiscountTax() && $item->getRewardPointsDiscountTax() >0) {
                    $this->_hiddenTaxes[$key]['value'] = $taxInfoItem['value'] - $item->getRewardPointsDiscountTax();
                    $this->_hiddenTaxes[$key]['base_value'] = $taxInfoItem['base_value'] - $item->getRewardPointsBaseDiscountTax();
                    break;
                }
            }
            //fix 1.4
            if($item->getHiddenTaxAmount()){
                $item->setHiddenTaxAmount($item->getHiddenTaxAmount() - $item->getRewardPointsDiscountTax());
                $item->setBaseHiddenTaxAmount($item->getBaseHiddenTaxAmount() - $item->getRewardPointsBaseDiscountTax());
            }
        }
        
        $item->setDiscountAmount($discount);
        $item->setBaseDiscountAmount($baseDiscount);
        return $this;
    }
    */
    /**
     * Calculate tax for each item
     * 
     * @param type $item
     * @param type $rate
     * @return Magestore_RewardPoints_Model_Total_Quote_Tax
     */
	 /*
    protected function _calcRowTaxAmount($item, $rate) {
        $discount       = $item->getDiscountAmount();
        $baseDiscount   = $item->getBaseDiscountAmount();
        if($item->getIsPriceInclTax()){
            $item->setRewardPointsDiscountTax($this->_calculator->calcTaxAmount($item->getRewardpointsDiscount(), $rate, false, false));
            $item->setRewardPointsBaseDiscountTax($this->_calculator->calcTaxAmount($item->getRewardpointsBaseDiscount(), $rate, false, false));
        }
        $item->setDiscountAmount($discount+$item->getAffiliateplusAmount()+ $item->getRewardpointsDiscount() + $item->getRewardPointsDiscountTax());
        $item->setBaseDiscountAmount($baseDiscount+$item->getBaseAffiliateplusAmount()+ $item->getRewardpointsDiscount() + $item->getRewardPointsBaseDiscountTax());
        
        parent::_calcRowTaxAmount($item, $rate);
        
        $afterDiscount = (bool)Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $this->_store);
        if($item->getIsPriceInclTax() && $afterDiscount){
            foreach ($this->_hiddenTaxes as $key => $taxInfoItem) {
                if (isset($taxInfoItem['item']) && $item->getId() == $taxInfoItem['item']->getId() && $taxInfoItem['value'] >= $item->getRewardPointsDiscountTax() && $item->getRewardPointsDiscountTax() >0) {
                    $this->_hiddenTaxes[$key]['value'] = $taxInfoItem['value'] - $item->getRewardPointsDiscountTax();
                    $this->_hiddenTaxes[$key]['base_value'] = $taxInfoItem['base_value'] - $item->getRewardPointsBaseDiscountTax();
                    break;
                }
            }
            //fix 1.4
            if($item->getHiddenTaxAmount()){
                $item->setHiddenTaxAmount($item->getHiddenTaxAmount() - $item->getRewardPointsDiscountTax());
                $item->setBaseHiddenTaxAmount($item->getBaseHiddenTaxAmount() - $item->getRewardPointsBaseDiscountTax());
            }
        }
        
        $item->setDiscountAmount($discount);
        $item->setBaseDiscountAmount($baseDiscount);
        return $this;
    }
    */
    /**
     * Calculate tax for shipping amount
     * 
     * @param Mage_Sales_Model_Quote_Address $address
     * @param type $taxRateRequest
     */
    protected function _calculateShippingTax(Mage_Sales_Model_Quote_Address $address, $taxRateRequest) {
        $discount       = $address->getShippingDiscountAmount();
        $baseDiscount   = $address->getBaseShippingDiscountAmount();
        $taxRateRequest->setProductClassId($this->_config->getShippingTaxClass($this->_store));
        if($address->getIsShippingInclTax()){
            $address->setRewardpointsDiscountTaxForShipping($this->_calculator->calcTaxAmount($address->getRewardpointsAmount(), $this->_calculator->getRate($taxRateRequest), false, false));
            $address->setRewardpointsBaseDiscountTaxForShipping($this->_calculator->calcTaxAmount($address->getRewardpointsBaseAmount(), $this->_calculator->getRate($taxRateRequest), false, false));
        }
        $address->setShippingDiscountAmount($discount+$address->getRewardpointsAmount()+$address->getRewardpointsDiscountTaxForShipping());
        $address->setBaseShippingDiscountAmount($baseDiscount+$address->getRewardpointsBaseAmount()+$address->getRewardpointsBaseDiscountTaxForShipping());
        
        parent::_calculateShippingTax($address, $taxRateRequest);
        
        if($address->getIsShippingInclTax() && $address->getRewardpointsDiscountTaxForShipping() > 0){
            $length = count($this->_hiddenTaxes);
            if($this->_hiddenTaxes[$length-1]['value']>0){
                $this->_hiddenTaxes[$length-1]['value'] = $this->_hiddenTaxes[$length-1]['value'] - $address->getRewardpointsDiscountTaxForShipping();
                $this->_hiddenTaxes[$length-1]['base_value'] = $this->_hiddenTaxes[$length-1]['base_value'] - $address->getRewardpointsBaseDiscountTaxForShipping();
            }
            
            //fix 1.4
            if($address->getShippingHiddenTaxAmount()){
                $address->setShippingHiddenTaxAmount($address->getShippingHiddenTaxAmount() - $address->getRewardpointsDiscountTaxForShipping());
                $address->setBaseShippingHiddenTaxAmount($address->getBaseShippingHiddenTaxAmount() - $address->getRewardpointsBaseDiscountTaxForShipping());
            }
        }
        
        $address->setShippingDiscountAmount($discount);
        $address->setBaseShippingDiscountAmount($baseDiscount);
        return $this;
    }
}
