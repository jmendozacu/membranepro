<?php

class Magestore_Dailydeal_Block_Adminhtml_Randomdeal extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_randomdeal';
		$this->_blockGroup = 'dailydeal';
		$this->_headerText = Mage::helper('dailydeal')->__('Generator Manager');
		$this->_addButtonLabel = Mage::helper('dailydeal')->__('Add Generator');
		parent::__construct();
	}
}