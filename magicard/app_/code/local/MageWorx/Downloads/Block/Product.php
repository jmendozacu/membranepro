<?php

/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Downloads extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
class MageWorx_Downloads_Block_Product extends Mage_Core_Block_Template {

    protected $_template = 'downloads/block_file_links.phtml';

    protected function _prepareLayout() {
        $this->setData('template', $this->_template);
        $helper = Mage::helper('downloads');
        if (!$helper->isEnabled()) {
            return $this;
        }
        if ($productBlock = $this->getLayout()->getBlock('product.info')) {
            $product = $productBlock->getProduct();
        } else {
            return $this;
        }

        $productDownloadsTitle = trim($this->helper('catalog/output')->productAttribute($product, $product->getDownloadsTitle(), 'downloads_title'));
        if ($productDownloadsTitle) {
            $this->setTitle($productDownloadsTitle);
        } else {
            $title = trim($this->getTitle());
            if (empty($title)) {
                $this->setTitle(Mage::helper('downloads')->getProductDownloadsTitle());
            }
        }

        $productId = (int) $product->getId();
        $ids = Mage::getResourceSingleton('downloads/relation')->getFileIds($productId);

        if (is_array($ids) && $ids) {
            $files = Mage::getResourceSingleton('downloads/files_collection');
            $files->addResetFilter()
                    ->addFilesFilter($ids)
                    ->addStatusFilter()
                    ->addCategoryStatusFilter()
                    ->addStoreFilter()
                    ->addSortOrder();
            $this->setItems($files->getItems());
        }

        if ($this->getNameInLayout() != 'downloads.tab') {
            $position = $helper->getBlockPosition();
            if (in_array(1, $position)) {
                $productBlock->append($this, 'other');
            }
            if (in_array(2, $position)) {
                if ($additionalBlock = $this->getLayout()->getBlock('product.info.additional')) {
                    $additionalBlock->insert($this, '', false, 'downloads');
                }
            }
            if (in_array(3, $position)) {
                if ($tabsBlock = $this->getLayout()->getBlock('product.info.tabs')) {
                    $tabsBlock->addTab('downloads.tab', $this->getTitle(), $this->getType(), $this->_template);
                }
            }
        }
        return $this;
    }

}
