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

class Xj_Sticker_Block_Adminhtml_Product_Edit_Tabs_Sticker_Grid
    extends Xj_Sticker_Block_Adminhtml_Sticker_Grid
{
    protected $_formFieldName = 'sticker';
    protected $_isTabGrid = true;
    protected $_columnPrefix = 'stickers_';
    protected $_checkboxFieldName = 'stickers_in_selected';

    //protected $_defaultLimit    = 1;

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/stickergridonly', array('_current' => true, '_secure'=>true));
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return !$this->_getAclHelper()->isActionAllowed('manage_sticker/assign');
    }

    /**
     * Retrieve selected items
     *
     * @return array
     */
    public function getSelectedLinks()
    {
        if (null !== $this->_selectedLinks) {
            return $this->_selectedLinks;
        }

        $this->_selectedLinks = array();
        $productId = $this->getRequest()->getParam('product_id');
        $product = null;
        if ($productId) {
            $product = $this->_getProductItem()->load($productId);
        }

        if ($product) {
            $this->_selectedLinks = $this->_getItemModel()->getSelectedStickersToProduct($product);
        }

        return $this->_selectedLinks;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProductItem()
    {
        return Mage::getModel('catalog/product');
    }
}
