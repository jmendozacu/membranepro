<?php
class Magestore_Dailydeal_Block_Adminhtml_Dailydeal_Renderer_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){							
		return '<a href="'.$this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getProductId())).'">'.$row->getProductName().'</a>';
	}
}