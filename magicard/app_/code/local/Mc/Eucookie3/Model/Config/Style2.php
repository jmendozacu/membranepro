<?php
/*------------------------------------------------------------------------
 * @version		$Id$
 * @author		Qubesys Technologies Pvt.Ltd (info@qubesys.com)
 * @category	Mc
 * @package		Mc_Eucookie3
 * @copyright	Copyright (C) 2013 - 2014 Qubesys Technologies Pvt.Ltd. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/ 
class Mc_Eucookie3_Model_Config_Style2 
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'left', 'label'=>Mage::helper('adminhtml')->__('Left')),
            array('value'=>'right', 'label'=>Mage::helper('adminhtml')->__('Right')),
           
            
            
        );
    }

}
