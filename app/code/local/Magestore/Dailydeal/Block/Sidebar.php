<?php
class Magestore_Dailydeal_Block_Sidebar extends Mage_Core_Block_Template
{
	public function _construct() {
		return parent::_construct();
	}
    public function displayOnLeftSidebarBlock() {
		$block = $this->getParentBlock();
		
		if($block) {
            $temp = !($this->getlink()=='indexindexdailydealdailydeal');
			if($temp && Mage::helper('dailydeal')->isDisplayOnSidebar() && Mage::helper('dailydeal')->displayOnLeftRightSideBar() == 1)
            {
					
				$sidebarBlock = $this->getLayout()
							->createBlock('dailydeal/dailydealsidebar');
				$block->insert($sidebarBlock, '', false, 'dailydeal-sidebar');
			}
		}
	}
    public function displayOnRightSidebarBlock() {
		$block = $this->getParentBlock();
		if($block) {
            $temp = !($this->getlink()=='indexindexdailydealdailydeal');
			if($temp && Mage::helper('dailydeal')->isDisplayOnSidebar() && Mage::helper('dailydeal')->displayOnLeftRightSideBar() == 2)
            {
				$sidebarBlock = $this->getLayout()
							->createBlock('dailydeal/dailydealsidebar');
				$block->insert($sidebarBlock, '', false, 'dailydeal-sidebar');
			}
		}
	}
    public function getlink(){
        $link=Mage::app()->getRequest()->getControllerName().
        Mage::app()->getRequest()->getActionName().
        Mage::app()->getRequest()->getRouteName().
        Mage::app()->getRequest()->getModuleName();
        return $link;
    }
}