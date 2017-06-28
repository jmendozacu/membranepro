<?php
 
class Busteco_Externaldbconnection_Model_Mysql4_Grid extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('externaldbconnection/grid', 'entity_id');
    }
} 