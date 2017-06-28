<?php

class TM_AjaxSearch_Model_Mysql4_Product_Collection extends
Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
// Mage_CatalogSearch_Model_Resource_Fulltext_Collection
{
    public function addSearchFilter($query)
    {
        return $this->setQueryFilter($query);
    }

    protected function _getEnabledAttributteCodes()
    {
        $attributes = array('name');
        $searchAttributes = Mage::getStoreConfig('tm_ajaxsearch/general/attributes');
        if (!empty($searchAttributes)) {
            $attributes = explode(',', $searchAttributes);
        }
        return $attributes;
    }

    public function setQueryFilter($query)
    {
        $attributes = $this->_getEnabledAttributteCodes();
        $andWhere = array();
        $orWhere = false;

        /* @var $stringHelper Mage_Core_Helper_String */
        $stringHelper = Mage::helper('core/string');

        $words = $stringHelper->splitWords($query, true);
//        $words = explode(' ', trim($query));
        if (empty($words) || empty($query)) {
            return $this;
        }
        foreach ($attributes as $attribute) {

            $this->addAttributeToSelect($attribute, true);
            foreach ($words as $word) {
                $andWhere[] = $this->_getAttributeConditionSql(
                    $attribute,
                    array('like' => '%' . $word . '%')
                );
            }
            if ($orWhere) {
                $this->getSelect()->orWhere(implode(' AND ', $andWhere));
            } else {
                $this->getSelect()->where(implode(' AND ', $andWhere));
                $orWhere = true;
            }

            $andWhere = array();
        }

        return $this;
    }

    protected function _preparePriceExpressionParameters($select)
    {
        $wheres = $select->getPart(Zend_Db_Select::WHERE);
        foreach ($wheres as $i => $where) {
            if (strstr($where, 'sku')) {
                unset($wheres[$i]);
            }
        }
        $select->reset(Zend_Db_Select::WHERE);
        $select->setPart(Zend_Db_Select::WHERE, $wheres);

        // prepare response object for event
        $response = new Varien_Object();
        $response->setAdditionalCalculations(array());
        $tableAliases = array_keys($select->getPart(Zend_Db_Select::FROM));
        if (in_array(self::INDEX_TABLE_ALIAS, $tableAliases)) {
            $table = self::INDEX_TABLE_ALIAS;
        } else {
            $table = reset($tableAliases);
        }

        // prepare event arguments
        $eventArgs = array(
            'select'          => $select,
            'table'           => $table,
            'store_id'        => $this->getStoreId(),
            'response_object' => $response
        );
        Mage::dispatchEvent('catalog_prepare_price_select', $eventArgs);

        $additional   = join('', $response->getAdditionalCalculations());
        $this->_priceExpression = $table . '.min_price';
        $this->_additionalPriceExpression = $additional;
        $this->_catalogPreparePriceSelect = clone $select;
        return $this;
    }
}
