<?php

class Magestore_Dailydeal_Block_Adminhtml_Randomdeal_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'dailydeal';
		$this->_controller = 'adminhtml_randomdeal';
		
		$this->_updateButton('save', 'label', Mage::helper('dailydeal')->__('Save Deal Generator'));
		$this->_updateButton('delete', 'label', Mage::helper('dailydeal')->__('Delete Deal Generator'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('randomdeal_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'randomdeal_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'randomdeal_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

    public function getHeaderText()
    {
        if( Mage::registry('randomdeal_data') && Mage::registry('randomdeal_data')->getId() ) {
            if(Mage::registry('randomdeal_data')->getStatus() == 5)
			{
				return Mage::helper('dailydeal')->__("Patten Information");			
			} else {
				return Mage::helper('dailydeal')->__("Edit Deal Generator for '%s'", $this->htmlEscape(Mage::registry('randomdeal_data')->getProductName()));
			}
		} else {
            return Mage::helper('dailydeal')->__('Add Generator');
        }
    }
    public function getRandomdeal()     
    { 
        if (!$this->hasData('randomdeal_data')) 
		{
            $this->setData('randomdeal_data', Mage::registry('randomdeal_data'));
        }
        return $this->getData('randomdeal_data');
    }
}