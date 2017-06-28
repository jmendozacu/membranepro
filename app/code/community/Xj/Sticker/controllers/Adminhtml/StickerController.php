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

class Xj_Sticker_Adminhtml_StickerController
	extends Xj_Core_Controller_Adminhtml_AbstractController
{
    protected $_msgTitle = 'Stickers';
    protected $_msgHeader = 'Manage Stickers';
    protected $_msgItemDoesNotExist = 'The Sticker item does not exist.';
    protected $_msgItemNotFound = 'Unable to find the sticker item #%s.';
    protected $_msgItemEdit = 'Edit Sticker Item';
    protected $_msgItemNew = 'New Sticker Item';
    protected $_msgItemSaved = 'The Sticker item has been saved.';
    protected $_msgItemDeleted = 'The Sticker item has been deleted';
    protected $_msgError = 'An error occurred while edit the Sticker item.';
    protected $_msgErrorItems = 'An error occurred while edit the Sticker items %s.';
    protected $_msgItems = 'The Sticker items (#%s) has been';

    protected $_menuActive = 'xj/manage_sticker';
    protected $_aclSection = 'manage_sticker';

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _getItemModel()
    {
        return Mage::getModel('xj_sticker/sticker');
    }

    /**
     * @param Mage_Core_Model_Abstract $model
     * @return Xj_Sticker_Adminhtml_StickerController
     */
    protected function _registerItem(Mage_Core_Model_Abstract $model)
    {
        Mage::register('.sticker.item', $model);
        return $this;
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
     * Grid with serializer ajax action
     */
    public function stickergridAction()
    {
        $this->_loadLayouts();
    }

    /**
     * Grid only ajax action
     */
    public function stickergridonlyAction()
    {
        $this->_loadLayouts();
    }
}
