<?php
class Magestore_Dailydeal_Block_Dailydealsidebar extends Mage_Core_Block_Template
{
	public function _construct() {
        $this->setTemplate('dailydeal/sidebar.phtml');
		return parent::_construct();
	}
    public function getSidebarProductCollection(){
		if(!Mage::registry('is_random_dailydeal'))
		Mage::helper('dailydeal')->updateDailydealStatus();
        $products=Mage::getModel('dailydeal/dailydeal')->getSidebarProductCollection();
        return $products;
    }
    public function getDailydealByProduct($productId){
        $dailydeal=Mage::getModel('dailydeal/dailydeal')->getDailydealByProduct($productId);
        return $dailydeal;
    }
}