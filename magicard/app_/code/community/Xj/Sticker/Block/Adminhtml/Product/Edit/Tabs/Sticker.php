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

class Xj_Sticker_Block_Adminhtml_Product_Edit_Tabs_Sticker
    extends Mage_Core_Block_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return $this->_getHelper()->__('Related XJ Stickers');
    }

    public function getTabTitle()
    {
        return $this->_getHelper()->__('Related XJ Stickers');
    }

    public function canShowTab()
    {
        return $this->_getAclHelper()->isActionAllowed('manage_sticker/assign');
    }

    public function isHidden()
    {
        return !$this->canShowTab();
    }

    public function getTabUrl()
    {
        return $this->getUrl('xj_sticker/adminhtml_sticker/stickergrid',
            array('product_id' => $this->_getProduct()->getId(), '_secure'=>true)
        );
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * @return Xj_Sticker_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('xj_sticker');
    }

    /**
     * @return Xj_Sticker_Helper_Admin
     */
    protected function _getAclHelper()
    {
        return Mage::helper('xj_sticker/admin');
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return MAge::registry('current_product');
    }
}
