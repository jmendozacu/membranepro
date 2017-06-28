<?php

class Magestore_Dailydeal_Block_Adminhtml_Dailydeal_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('dailydeal_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('dailydeal')->__('Deal Information'));
	}

	protected function _beforeToHtml(){
		$dailydeal = $this->getDailydeal();
		if( ! $dailydeal || ($dailydeal && ($dailydeal->getStatus()=='' )))
		{  
	  	$this->addTab('form_listproduct', array(
          'label'     => Mage::helper('dailydeal')->__('Select Product'),
          'title'     => Mage::helper('dailydeal')->__('Select Product'),
		  'class'     => 'ajax',
		  'url'   => $this->getUrl('*/*/listproduct',array('_current'=>true,'id'=>$this->getRequest()->getParam('id'))),
	  	));	
		}
        $this->addTab('form_section', array(
          'label'     => Mage::helper('dailydeal')->__('General Information'),
          'title'     => Mage::helper('dailydeal')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit_tab_form')->toHtml(),
	  	));
		if( ! $dailydeal || ($dailydeal && ($dailydeal->getStatus() !='')))
		{
		$this->addTab('form_listorder', array(
          'label'     => Mage::helper('dailydeal')->__('Sold Items'),
          'title'     => Mage::helper('dailydeal')->__('Sold Items'),
          'content'   => $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit_tab_listorder')->toHtml(),
	  	));
    	}
        return parent::_beforeToHtml();
    }
    public function getDailydeal()     
    { 
        if (!$this->hasData('dailydeal_data')) {
                $this->setData('dailydeal_data', Mage::registry('dailydeal_data'));
        }
        return $this->getData('dailydeal_data');   
    }
}