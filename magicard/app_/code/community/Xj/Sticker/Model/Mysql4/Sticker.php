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
 * @category    Xj
 * @package     Xj_Sticker
 * @copyright   Copyright (c) 2012 Xj
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sticker item resource model
 *
 * @author Xj
 */
class Xj_Sticker_Model_Mysql4_Sticker
extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_fields = array(
        'sticker_id',
        'product_id',
    );

    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('xj_sticker/sticker', 'sticker_id');
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return Xj_Sticker_Model_Mysql4_Sticker
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeSave($object);

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        return $this;
    }

    /**
     * @return string
     */
    protected function _getLinkTable()
    {
        return $this->getTable('xj_sticker/sticker_product');
    }

    /**
     * @param Mage_Catalog_Model_Product $object
     * @return Xj_Sticker_Model_Sticker
     */
    public function getSelectedStickersToProduct(Mage_Catalog_Model_Product $object, $distinct = false)
    {
        return $this->getReadConnection()->fetchCol($this->_getSelectedStickersToProductSelect($object));
    }

    /**
     * @param Mage_Catalog_Model_Product $object
     * @return Varien_Db_Select
     */
    protected function _getSelectedStickersToProductSelect(Mage_Catalog_Model_Product $object)
    {
        $select = $this->getReadConnection()
            ->select()
            ->from($this->_getLinkTable(), array('sticker_id'))
            ->where('product_id = ?', $object->getId());
        return $select;
    }

    /**
     * @param Mage_Catalog_Model_Product $object
     * @return Xj_Sticker_Model_Mysql4_Sticker
     */
    public function assignStickersToProduct($object)
    {
        if (null === $object->getXjStickers()) {
            return $this;
        }
        $oldStickers = $this->getSelectedStickersToProduct($object);
        $newStickers = $object->getXjStickers();

        if (!empty($oldStickers)) {
            $this->_deleteStickersToProduct($oldStickers, $object);
        }
        if (!empty($newStickers)) {
            $this->_insertStickersToProduct($newStickers, $object);
        }
        return $this;
    }

    /**
     * @param array $stickerIds
     * @param Mage_Catalog_Model_Product $object
     * @return Xj_Sticker_Model_Mysql4_Sticker
     */
    protected function _insertStickersToProduct(array $stickerIds, Mage_Catalog_Model_Product $object)
    {
        $data = $this->_prepareInsertDataByStickers($stickerIds, $object);
        $this->_getWriteAdapter()->insertArray($this->_getLinkTable(), $this->_fields, $data);
        return $this;
    }

    /**
     * @param array $stickerIds
     * @param Mage_Catalog_Model_Product $object
     * @return Xj_Sticker_Model_Mysql4_Sticker
     */
    protected function _deleteStickersToProduct(array $stickerIds, Mage_Catalog_Model_Product $object)
    {
        $where = $this->_deleteStickersToProductWhere($stickerIds, $object);
        $this->_getWriteAdapter()->delete($this->_getLinkTable(), $where);
        return $this;
    }

    /**
     * @param array $stickerIds
     * @param Mage_Catalog_Model_Product $object
     * @return string
     */
    protected function _deleteStickersToProductWhere(array $stickerIds, Mage_Catalog_Model_Product $object)
    {
        $where = $this->_getWriteAdapter()->quoteInto('product_id = ?', $object->getId());
        $where.= $this->_getWriteAdapter()->quoteInto(' AND sticker_id IN (?)', $stickerIds);
        return $where;
    }

    /**
     * @param array $stickerIds
     * @param Mage_Catalog_Model_Product $object
     * @return array
     */
    protected function _prepareInsertDataByStickers(array $stickerIds, Mage_Catalog_Model_Product $object)
    {
        $data = array();
        foreach($stickerIds as $stickerId) {
            $data[] = array('sticker_id' => $stickerId, 'product_id' => $object->getId());
        }
        return $data;
    }
}
