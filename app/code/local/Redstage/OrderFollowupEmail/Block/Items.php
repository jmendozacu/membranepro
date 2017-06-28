<?php

class Redstage_OrderFollowupEmail_Block_Items extends Mage_Sales_Block_Items_Abstract {

//	protected $_quote = null;

	public function __construct(){
		parent::__construct();
		$this->addItemRender( 'default', 'sales/order_email_items_order_default', 'orderfollowupemail/items/default.phtml' );
	}

/*	public function getQuote(){
		if( !$this->_quote && $this->getCart()->getId() ){
			Mage::app()->setCurrentStore(1);
			$this->_quote = Mage::getModel('sales/quote')->loadByCustomer( $this->getCart()->getCustomerId() );
		}
		return $this->_quote;
	}

	public function getCustomer(){
		if( null === $this->_customer ){
			$this->_customer = Mage::getModel('customer/customer')->load( $this->getCart()->getCustomerId() );
		}
		return $this->_customer;
	}*/

}