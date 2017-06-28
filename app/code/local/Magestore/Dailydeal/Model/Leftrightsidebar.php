<?php

class Magestore_Dailydeal_Model_Leftrightsidebar
{
    public function toOptionArray()
    {
        $options = array(
					array('value'=>1,'label'=> Mage::helper('dailydeal')->__('Left sidebar')),
					array('value'=>2,'label'=> Mage::helper('dailydeal')->__('Right sidebar')),
				);
		
		return $options;
    }
}