<?php

class Magestore_Dailydeal_Block_Adminhtml_Randomdeal_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{ 
	public function __construct()
	{
		parent::__construct();
	}
 
  public function getRandomdeal()     
  { 
        if (!$this->hasData('randomdeal_data')) {
            $this->setData('randomdeal_data', Mage::registry('randomdeal_data'));
        }
        return $this->getData('randomdeal_data');   
  }
	protected function _prepareForm()
	{
      
	  $form = new Varien_Data_Form();
            $this->setForm($form);
            $fieldset = $form->addFieldset('randomdeal_edit', array('legend'=>Mage::helper('dailydeal')->__('Patten Information')));
      
	  $image_calendar = Mage::getBaseUrl('skin').'adminhtml/default/default/images/grid-cal.gif';
	  $data = $this->getRandomdeal();
	  $disabled = false;
	  $disabled = ($data['status'] == 4) ? true : $disabled;
	
    $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('dailydeal')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
		      'disabled'  => $disabled,
		      'note'      => 'Example {{product_name}} sale off {{save}}',
            ));
    if ((($this->getRandomdeal()->getStatus())!='')){
       
                $productIds =$this->getRandomdeal()->getProducts();
                $productIds=explode(",",$productIds);
                $products=Mage::getResourceModel('catalog/product_collection')
                        ->addFieldToFilter('entity_id', array('in'=>$productIds))
                        ->addAttributeToSelect('*');
                $str='';
                $i=1;
                       foreach ($products as $product) {
                           $str =$str.$i.'.  <a target="_blank" href="'. $this->getUrl('adminhtml/catalog_product/edit',array('id'=>$product->getEntityId())) .'">'.  $this->__($product->getName()) .'</a></br>';
                           $i++;
                       }
      $fieldset->addField('product_name', 'note', array(
          'label'     => Mage::helper('dailydeal')->__('Product Name'),
          'text'      =>$str,
	  ));
  }

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
    		  'note'      => '0-10: select randomly a value from 0% to 10% for each deal.</br>
							  2; 5; 10: select randomly one of 2%, 5%, 10% for each deal.</br>
							  10: set the fixed rate of 10% for each deal.',
      ));

      $fieldset->addField('quantity', 'text', array(
          'label'     => Mage::helper('dailydeal')->__('Quantity'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'quantity',
		      'disabled'  => $disabled,
          'note'      =>'10–50: select randomly a quantity from 10 to 50 for each deal.</br>
						10; 20; 50: select randomly a quantity of 10, 20, 50 for each deal.</br>
						20: set a fixed quantity of 20 for each deal.'		  
      ));

      try {
        $data['start_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(strtotime($data['start_time'])));
        $data['close_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(strtotime($data['close_time'])));
      } catch (Exception $e) {
        
      }
      $note = $this->__('Current time on the server ').': '.$this->formatTime(now(),Mage_Core_Model_Locale::FORMAT_TYPE_SHORT,true);
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
      $fieldset->addField('deal_time', 'text', array(
          'label'     => Mage::helper('dailydeal')->__('Deal time'),
          'required'  => true,
          'name'      => 'deal_time',
          'note'      =>'Operating time of each deal (hour)',
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
        'disabled'  => $disabled,
        'values'  => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
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
      
		if($data)
    {
      $form->setValues($data);
    }

      if ( Mage::getSingleton('adminhtml/session')->getRandomdealData() )
      {
          $data = Mage::getSingleton('adminhtml/session')->getRandomdealData();
          Mage::getSingleton('adminhtml/session')->setRandomdealData(null);
      } elseif ( Mage::registry('randomdeal_data') ) {
          $data = Mage::registry('randomdeal_data')->getData();
      }
      return parent::_prepareForm();
	}	
}