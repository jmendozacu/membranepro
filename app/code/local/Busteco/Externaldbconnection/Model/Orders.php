<?php
 
class Busteco_Externaldbconnection_Model_Orders extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('externaldbconnection/orders');
    }
}