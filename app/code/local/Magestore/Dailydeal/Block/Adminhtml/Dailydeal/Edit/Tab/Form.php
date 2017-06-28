
<?php

class Magestore_Dailydeal_Block_Adminhtml_Dailydeal_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{ 
	public function __construct()
	{
		parent::__construct();
	}
 
  public function getDailydeal()     
  { 
        if (!$this->hasData('dailydeal_data')) 
        {
            $this->setData('dailydeal_data', Mage::registry('dailydeal_data'));
        }
        return $this->getData('dailydeal_data');   
  }
	protected function _prepareForm()
	{
	  $form = new Varien_Data_Form();
            $this->setForm($form);
            $fieldset = $form->addFieldset('dailydeal_edit', array('legend'=>Mage::helper('dailydeal')->__('Deal information')));
      
	  $image_calendar = Mage::getBaseUrl('skin').'adminhtml/default/default/images/grid-cal.gif';
	  $data = $this->getDailydeal();
	  $disabled = false;
	  $disabled = ($data['status'] == 4) ? true : $disabled;
	
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('dailydeal')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
		      'disabled'  => $disabled,
		      'note'      => '',
      ));
	  $note='';
      if($data['product_id'])
      $note= '<a target="_blank" href="'. $this->getUrl('adminhtml/catalog_product/edit',array('id'=>$data->getProductId())) .'">'. $this->__('view') .'</a>
							<input type="hidden" name="product_id" id="product_id" value="'. $data->getProductId() .'">';
      $fieldset->addField('product_name', 'text', array(
          'label'     => Mage::helper('dailydeal')->__('Product Name'),
          'class'     => 'required-entry',
          'required'  => true,
		      'readonly'  => 'readonly',
          'name'      => 'product_name',
          'disabled'  => $disabled,
		      'note'      =>$note,
	    ));	 

      $fieldset->addField('thumbnail_image', 'image', array(
			    'label'		=> Mage::helper('dailydeal')->__('Thumbnail image'),
			    'required'	=> false,
          'note'          =>'Leave blank if you want to use product image.',
			    'name'		=> 'thumbnail',
          'disabled'  => $disabled,
		  ));

	    $fieldset->addField('save', 'text', array(
          'label'     => Mage::helper('dailydeal')->__('Save'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'save',
		      'disabled'  => $disabled,
    		  'note'      => 'Example 10 (as 10%)',
      ));

      $fieldset->addField('quantity', 'text', array(
          'label'     => Mage::helper('dailydeal')->__('Quantity'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'quantity',
		      'disabled'  => $disabled,		  
      ));
      try {
        $data['start_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(strtotime($data['start_time'])));
        $data['close_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(strtotime($data['close_time'])));
      } catch (Exception $e) {
        
      }
      $note = $this->__('The current server time is').': '.$this->formatTime(now(),Mage_Core_Model_Locale::FORMAT_TYPE_SHORT,true);
      $fieldset->addField('start_time', 'date', array(
          'label'     => Mage::helper('dailydeal')->__('Start time'),
          'name'      => 'start_time',
          'input_format'  => Varien_Date::DATETIME_INTERNAL_FORMAT,
          'image' => $image_calendar,
          'format'    =>Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
          'time' => true,
          'required'  => true,
          'disabled'  => $disabled,
      ));
      $fieldset->addField('close_time', 'date', array(
          'label'     => Mage::helper('dailydeal')->__('Close time'),
          'name'      => 'close_time',
          'input_format'  => Varien_Date::DATETIME_INTERNAL_FORMAT,
          'image' => $image_calendar,
          'format'    =>Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
          'time' => true,
          'required'  => true,
          'disabled'  => $disabled,
          'note'=>$note,
      ));
    if (Mage::app()->isSingleStoreMode()){
      $fieldset->addField('store_id','hidden',array(
        'name'  => 'stores[]',
        'value' => Mage::app()->getStore(true)->getId(),
        'disabled'  => $disabled,
      ));
      $data['store_id'] = Mage::app()->getStore(true)->getId();
    } else {
      $fieldset->addField('store_id','multiselect',array(
        'name'  => 'stores[]',
        'label'   => Mage::helper('dailydeal')->__('Store View'),
        'title'   => Mage::helper('dailydeal')->__('Store View'),
        'required'  => true,
        'values'  => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
        'disabled'  => $disabled,
      ));
    }
    $arrayName = array(
        1 => 'Coming',
        3 => 'Active',
        4 => 'Expired',
        2 => 'Disable',
      );
    if (!$data['status']){
        $status= array(
        1 => 'Enable',
        2 => 'Disable',
      );
    }elseif ($data['status']==2){
      $status=Mage::helper('dailydeal')->getOptionStatus();
    }else{
      $status= array(
        1 => $arrayName[$data['status']],
        2 => 'Disable',
      );
    }
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('dailydeal')->__('Status'),
          'name'      => 'status_form',
          'values'    => $status,
		      'disabled'  => $disabled,		  
      ));
	
      if ( Mage::getSingleton('adminhtml/session')->getDailydealData() )
      {
          $data = Mage::getSingleton('adminhtml/session')->getDailydealData();
          Mage::getSingleton('adminhtml/session')->setDailydealData(null);
      } elseif ( Mage::registry('dailydeal_data') ) {
          $data = Mage::registry('dailydeal_data')->getData();
      }
	  if($data)
	  {
	    if(isset($data['product_id']) && $data['product_id'])
		  {
			 $product = Mage::getModel('catalog/product')->load($data['product_id']);
			 $data['product_name'] = $product->getName();
		  }
		  $form->setValues($data);
	  }
	  
    return parent::_prepareForm();
	
	}	
}