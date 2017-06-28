<?php

class Magestore_Dailydeal_IndexController extends Mage_Core_Controller_Front_Action
{
    protected function _initAction(){
		$this->loadLayout();
		$this->renderLayout();
		return $this;
	}
	public function indexAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
		if(!Mage::registry('is_random_dailydeal'))
        Mage::helper('dailydeal')->updateDailydealStatus();
		$this->loadLayout();
		$this->renderLayout();
	}
}