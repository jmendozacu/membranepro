<?php

class Cybertech_Newsletterpopup_Block_Subscribe extends Mage_Newsletter_Block_Subscribe
{
	const actionDisplayPopup = 'display-popup';
	const actionDisplayDelayedPopup = 'display-delayed-popup';
	
	public $cookie_validity_days;
	public $status;
	public $views_to_show_after;
	public $show_delayed_popup;
	public $delay_time;

	public $action;	// store the action about to be made
	
	public $COOKIE_NAME;
	public $COOKIE_PERIOD;
	
	public function _construct()
	{
		$this->readSettings();
		
		parent::_construct();
	}
	
	public function readSettings()
	{
		$storeUrl = Mage::getUrl('');
		
		$this->cookie_validity_days = 365;	// 1 year before re-showing the popup
		
		$this->status = true;
		$this->views_to_show_after = 3;
		$this->show_delayed_popup = true;
		$this->delay_time = 4;
				
		$this->COOKIE_NAME = Mage::app()->getStore()->getCode().'-newsletterpopup';
		$this->COOKIE_PERIOD = time()+60*60*24*$this->cookie_validity_days;
	}
	
	public function displayNewsletterPopup()
	{
		$allowedCatIdArr = array(3,24);
		$currentCategory = Mage::registry('current_category');
		$currentCategoryId = is_object($currentCategory) ? $currentCategory->getId() : 0;

		if($this->status == true && in_array($currentCategoryId,$allowedCatIdArr)) { // only in plugin enabled

			$this->action = '';
			
			$displayDelayedPopup = $this->needToDisplayDelayedPopup($this->COOKIE_NAME,$this->views_to_show_after);

			if($displayDelayedPopup)
				$this->action = $this::actionDisplayDelayedPopup;
			else {
				$displayPopup = $this->needToDisplayPopup($this->COOKIE_NAME,$this->views_to_show_after);
				if($displayPopup) // if popup needs to be displayed without delay
					$this->action = $this::actionDisplayPopup;
			}
			
			// don't use Magento cookie wrappers; the expirate date doesn't work on set !!!
			//$numberOfViews = (int)Mage::getModel('core/cookie')->get($this->COOKIE_NAME);
			//Mage::getModel('core/cookie')->set($this->COOKIE_NAME,++$numberOfViews,$this->COOKIE_PERIOD);
			$numberOfViews = $_COOKIE[$this->COOKIE_NAME];
			setcookie($this->COOKIE_NAME, ++$numberOfViews, $this->COOKIE_PERIOD, "/");
		}
	}
	
	public function needToDisplayPopup($COOKIE_NAME,$VIEWS_TO_SHOW_AFTER=1)
	{
		// ensure the popup wasn't displayed before
		if((int)$_COOKIE[$COOKIE_NAME."-d"])
			return false;
		
		// don't use Magento cookie wrappers; the expirate date doesn't work on set !!!
		//$numberOfViews = (int)Mage::getModel('core/cookie')->get($COOKIE_NAME);
		$numberOfViews = (int)$_COOKIE[$COOKIE_NAME];
		if($numberOfViews == $VIEWS_TO_SHOW_AFTER-1)
			return true;
		else
			return false;
	}
	
	public function needToDisplayDelayedPopup($COOKIE_NAME,$VIEWS_TO_SHOW_AFTER=1)
	{
		// ensure the popup wasn't displayed before
		if((int)$_COOKIE[$COOKIE_NAME."-d"])
			return false;
		
		// don't use Magento cookie wrappers; the expirate date doesn't work on set !!!
		//$numberOfViews = (int)Mage::getModel('core/cookie')->get($COOKIE_NAME);
		$numberOfViews = (int)$_COOKIE[$COOKIE_NAME];
		if($numberOfViews < $VIEWS_TO_SHOW_AFTER-1)
			return true;
		else
			return false;
	}
}