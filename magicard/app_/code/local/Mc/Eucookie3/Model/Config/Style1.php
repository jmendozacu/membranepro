<?php
/*------------------------------------------------------------------------
 * @version		$Id$
 * @author		Qubesys Technologies Pvt.Ltd (info@qubesys.com)
 * @category	Mc
 * @package		Mc_Eucookie3
 * @copyright	Copyright (C) 2013 - 2014 Qubesys Technologies Pvt.Ltd. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/ 
class Mc_Eucookie3_Model_Config_Style1 
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'bottom', 'label'=>Mage::helper('adminhtml')->__('Bottom')),
            array('value'=>'top', 'label'=>Mage::helper('adminhtml')->__('Top')),
            
            
        );
    }

}
