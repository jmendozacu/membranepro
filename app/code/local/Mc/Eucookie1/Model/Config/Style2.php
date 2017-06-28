<?php
/*------------------------------------------------------------------------
 * @version		$Id$
 * @author		Qubesys Technologies Pvt.Ltd (info@qubesys.com)
 * @category	Mc
 * @package		Mc_Eucookie1
 * @copyright	Copyright (C) 2010 - 2011 Open Source Matters. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/ 
class Mc_Eucookie1_Model_Config_Style2 
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>Mage::helper('adminhtml')->__('Bottom Left')),
            array('value'=>'2', 'label'=>Mage::helper('adminhtml')->__('Bottom Right')),
            array('value'=>'3', 'label'=>Mage::helper('adminhtml')->__('Left Side')),
            array('value'=>'4', 'label'=>Mage::helper('adminhtml')->__('Right Side')),
           
            
            
        );
    }

}
