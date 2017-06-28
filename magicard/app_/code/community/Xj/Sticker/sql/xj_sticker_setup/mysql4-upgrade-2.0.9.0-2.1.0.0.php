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

/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$this->startSetup();

//tables definition
$stickerTable = $this->getTable('xj_sticker/sticker');
$stickerProductTable = $this->getTable('xj_sticker/sticker_product');
$productTable = $this->getTable('catalog/product');

//create table for sticker
$this->run("
	DROP TABLE IF EXISTS `{$stickerProductTable}`;
	CREATE TABLE `{$stickerProductTable}` (
	    `product_id`   int(10) unsigned NOT NULL COMMENT 'Product ID',
	    `sticker_id`   int(10) unsigned NOT NULL COMMENT 'Sticker ID',
		PRIMARY KEY (`product_id`, `sticker_id`),
  		CONSTRAINT `FK_STICKER_PRODUCT_ID_CATALOG_PRODUCT_ENTITY_ID` FOREIGN KEY (`product_id`) REFERENCES `{$productTable}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  		CONSTRAINT `FK_STICKER_STICKER_ID_STICKER_STICKER_ID` FOREIGN KEY (`sticker_id`) REFERENCES `{$stickerTable}` (`sticker_id`) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stickers To Products Table';
");

$this->endSetup();