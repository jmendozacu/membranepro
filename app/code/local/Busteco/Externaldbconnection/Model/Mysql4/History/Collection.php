<?php
class Busteco_Externaldbconnection_Model_Mysql4_History_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('externaldbconnection/history');
    }
}