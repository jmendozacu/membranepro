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
 * Stickers collection
 *
 * @author Xj
 */
class Xj_Sticker_Model_Mysql4_Sticker_Collection
extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_collectAll = false;

    /**
     * Define collection model
     */
    protected function _construct()
    {
        $this->_init('xj_sticker/sticker');
    }

    /**
     * @return string
     */
    protected function _getLinkTable()
    {
        return $this->getTable('xj_sticker/sticker_product');
    }

    /**
     * @param $flag
     * @return Xj_Sticker_Model_Mysql4_Sticker_Collection
     */
    public function setCollectAll($flag)
    {
        $this->_collectAll = $flag;
        return $this;
    }

    /**
     * Retrieve item id
     *
     * @param Varien_Object $item
     * @return mixed
     */
    protected function _getItemId(Varien_Object $item)
    {
        if ($this->_collectAll) {
            return null;
        }
        return $item->getId();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('sticker_id', 'title');
    }

    /**
     * @return Xj_Sticker_Model_Mysql4_Sticker_Collection
     */
    public function useActive()
    {
        $this->addFilter('main_table.is_active', Xj_Sticker_Model_Sticker::STATUS_ENABLED);
        return $this;
    }

    /**
     * @param $productIds
     * @return Xj_Sticker_Model_Mysql4_Sticker_Collection
     */
    public function useProduct($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = array((int) $productIds);
        }

        $cond = 'main_table.sticker_id=ps.sticker_id AND ps.product_id IN (?)';
        $cond = $this->getConnection()->quoteInto($cond, $productIds);

        $this->getSelect()->join(array('ps' => $this->_getLinkTable()), $cond, array('product_id'));

        return $this;
    }

    /**
     * @param string $key
     * @param string $valueKey
     * @return array
     */
    public function toMultiArray($key = 'sticker_id', $valueKey = 'sticker_id')
    {
        $options = array();
        foreach($this as $item) {
            $options[$item->getData($key)][$item->getData($valueKey)] = $item->getData();
        }
        return $options;
    }
}
     