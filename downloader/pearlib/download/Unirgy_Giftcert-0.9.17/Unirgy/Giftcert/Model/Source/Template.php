<?php

class Unirgy_Giftcert_Model_Source_Template
    extends Mage_Adminhtml_Model_System_Config_Source_Email_Template
{
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}