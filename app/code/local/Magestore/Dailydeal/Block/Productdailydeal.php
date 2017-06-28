<?php

class Magestore_Dailydeal_Block_Productdailydeal extends Mage_Core_Block_Template

{
    public function getProduct() {
		return Mage::registry('current_product');
	}
    public function getDailydealByProduct($productId){
		if(!Mage::registry('is_random_dailydeal'))
    	Mage::helper('dailydeal')->updateDailydealStatus();
        $dailydeal=Mage::getModel('dailydeal/dailydeal')->getDailydealByProduct($productId);
        return $dailydeal;
    }
}