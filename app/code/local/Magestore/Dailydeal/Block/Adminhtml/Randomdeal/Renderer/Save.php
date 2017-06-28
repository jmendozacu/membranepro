<?php
class Magestore_Dailydeal_Block_Adminhtml_Randomdeal_Renderer_Save extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
                $save=$row->getSave();
		return $save.'%';
	}
}