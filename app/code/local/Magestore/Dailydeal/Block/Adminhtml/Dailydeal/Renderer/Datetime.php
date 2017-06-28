<?php
class Magestore_Dailydeal_Block_Adminhtml_Dailydeal_Renderer_Datetime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){
                $time=$row->getStartTime();
		return $time;
	}
}