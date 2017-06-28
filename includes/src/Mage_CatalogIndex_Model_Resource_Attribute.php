<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Attribute index resource model
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Resource_Attribute extends Mage_CatalogIndex_Model_Resource_Abstract
{
    /**
     * Enter description here ...
     *
     */
    protected function _construct()
    {
        $this->_init('catalogindex/eav', 'index_id');
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $attribute
     * @param unknown_type $filter
     * @param unknown_type $entityFilter
     * @return unknown
     */
    public function getFilteredEntities($attribute, $filter, $entityFilter)
    {
        $select = $this->_getReadAdapter()->select();

        $select
            ->from($this->getMainTable(), 'entity_id')
            ->distinct(true)
            ->where('entity_id in (?)', $entityFilter)
            ->where('store_id = ?', $this->getStoreId())
            ->where('attribute_id = ?', $attribute->getId())
            ->where('value = ?', $filter);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $attribute
     * @param unknown_type $entitySelect
     * @return unknown
     */
    public function getCount($attribute, $entitySelect)
    {
        $select = clone $entitySelect;
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $fields = array('count'=>'COUNT(index.entity_id)', 'index.value');

        $select->columns($fields)
            ->join(array('index'=>$this->getMainTable()), 'index.entity_id=e.entity_id', array())
            ->where('index.store_id = ?', $this->getSt