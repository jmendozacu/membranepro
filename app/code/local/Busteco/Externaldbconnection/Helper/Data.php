<?php
class Busteco_Externaldbconnection_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isPost()
	{
		if(Mage::app()->getRequest()->isPost()) return true;
		return false;
	}
	
	public function getIncrementIds()
	{
		if($this->isPost()){
			return Mage::app()->getRequest()->getParams();
		}
	}
	
	public function getIncrementIdByKey($key)
	{
		if($this->isPost()){
			return Mage::app()->getRequest()->getParam($key);
		}
	}
}
	