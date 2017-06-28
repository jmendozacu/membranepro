<?php
 
class Busteco_Externaldbconnection_Model_Mysql4_Orders extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('externaldbconnection/orders', 'entity_id');
    }
} 