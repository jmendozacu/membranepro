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
 * Stickers list admin grid
 *
 * @author Xj
 */
class Xj_Sticker_Block_Adminhtml_Sticker_Grid
    extends Xj_Sticker_Block_Adminhtml_Grid_Abstract
{
    protected $_gridId = 'stickerGrid';
    protected $_entityIdField = 'sticker_id';
    protected $_itemParam = 'sticker_id';
    protected $_formFieldName = 'sticker';

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return !$this->_getAclHelper()->isActionAllowed('manage_sticker');
    }

    /**
     * @return Xj_Sticker_Block_Adminhtml_Sticker_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumnAfter('position', array(
            'header' => $this->_getHelper()->__('Position'),
            'index'  => 'position',
            'type'    => 'options',
            'options' => $this->_getItemModel()->getAvailablePositions(),
            'width'   => 120,
            ''
        ), $this->_columnPrefix.'title');

        return parent::_prepareColumns();
    }
}
