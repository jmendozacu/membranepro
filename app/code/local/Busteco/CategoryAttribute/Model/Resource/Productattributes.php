<?php
class Busteco_CategoryAttribute_Model_Resource_Productattributes extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllVisibleProductAttributes()
    {
        $attributes = Mage::getSingleton('eav/config')->getEntityType('catalog_product')
            ->getAttributeCollection()
            //->setAttributeGroupFilter(array('in' => array('24', '25', '51')))
            ->addFieldToFilter('is_visible', 1)
            ->addFieldToFilter('is_visible_on_front', 1);
        
        $attributes->getSelect()->order('frontend_label', 'asc');
        
        if ($attributes->getSize()) {
            return $attributes;
        }
        
        return false;
    }
    
    
    public function getAllOptions()
    {
        $storeCode = Mage::app()->getStore()->getCode();
        $attributes = $this->getAllVisibleProductAttributes();
        
        if ($attributes && is_null($this->_options)) {
            
            $this->_options = array(
                array(
                    'label' => '',
                    'value' =>  ''
                )
            );
            
            foreach ($attributes as $attribute) {
                
                $this->_options[] = array(
                                        'label' => $attribute->getStoreLabel($storeCode),
                                        'value' =>  $attribute->getAttributeCode()
                                    );
            }
            
        }
        
        return $this->_options;
    }
 
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
 
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        $adminStore  = Mage_Core_Model_App::ADMIN_STORE_ID;
        $valueTable1 = $this->getAttribute()->getAttributeCode() . '_t1';
        $valueTable2 = $this->getAttribute()->getAttributeCode() . '_t2';
 
        $collection->getSelect()->joinLeft(
            array($valueTable1 => $this->getAttribute()->getBackend()->getTable()),
            "`e`.`entity_id`=`{$valueTable1}`.`entity_id`"
            . " AND `{$valueTable1}`.`attribute_id`='{$this->getAttribute()->getId()}'"
            . " AND `{$valueTable1}`.`store_id`='{$adminStore}'",
            array()
        );
 
        if ($collection->getStoreId() != $adminStore) {
            $collection->getSelect()->joinLeft(
                array($valueTable2 => $this->getAttribute()->getBackend()->getTable()),
                "`e`.`entity_id`=`{$valueTable2}`.`entity_id`"
                . " AND `{$valueTable2}`.`attribute_id`='{$this->getAttribute()->getId()}'"
                . " AND `{$valueTable2}`.`store_id`='{$collection->getStoreId()}'",
                array()
            );
            $valueExpr = new Zend_Db_Expr("IF(`{$valueTable2}`.`value_id`>0, `{$valueTable2}`.`value`, `{$valueTable1}`.`value`)");
 
        } else {
            $valueExpr = new Zend_Db_Expr("`{$valueTable1}`.`value`");
        }
 
 
 
        $collection->getSelect()
            ->order($valueExpr, $dir);
 
        return $this;
    }
 
    public function getFlatColums()
    {
        $columns = array(
            $this->getAttribute()->getAttributeCode() => array(
                'type'      => 'int',
                'unsigned'  => false,
                'is_null'   => true,
                'default'   => null,
                'extra'     => null
            )
        );
        return $columns;
    }
 
 
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}