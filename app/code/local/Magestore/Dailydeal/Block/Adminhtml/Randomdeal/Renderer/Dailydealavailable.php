<?php
class Magestore_Dailydeal_Block_Adminhtml_Randomdeal_Renderer_Dailydealavailable extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row){
        $dailydealId = $row->getProductId();
        $str='';
        $dailydeal=Mage::getModel('dailydeal/dailydeal')->load($dailydealId);
        if (!$dailydealId){
            $str= $this->__('There is no daily deal available!');
        }  else {
         $str .='<a href="'.$this->getUrl('dailydealadmin/adminhtml_dailydeal/edit/', array('id' => $dailydealId)).'">'.$dailydeal->getTitle().'</a></br>';
        }

        return $str;
    }
}