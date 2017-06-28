<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_System_Config_Source_NextPrev
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('catalog')->__('Yes')),
            array('value' => 2, 'label' => Mage::helper('seosuite')->__('Yes, except filtered pages of the layered navigation')),
            array('value' => 0, 'label' => Mage::helper('catalog')->__('No')),
        );
    }
}