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

/** @var $helper Xj_Sticker_Helper_Data */
$helper = Mage::helper('xj_sticker');

//tables definition
$stickerTable = $this->getTable('xj_sticker/sticker');
$stickerProductTable = $this->getTable('xj_sticker/sticker_product');
$productTable = $this->getTable('catalog/product');


//create table for sticker
$this->run("
	DROP TABLE IF EXISTS `{$stickerTable}`;
	CREATE TABLE `{$stickerTable}` (
	    `sticker_id`   int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Sticker ID',
	    `is_active`  tinyint DEFAULT 0,
	    `title`      varchar(255) DEFAULT NULL,
        `image`      varchar(255) DEFAULT NULL,
        `position`   varchar(32),
		`created_at` timestamp NULL DEFAULT NULL COMMENT 'Creation Time',
  		`updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Update Time',
		PRIMARY KEY (`sticker_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stickers Entity Table';
");

//create new columns
foreach($helper->getAttributes() as $attributeCode) {
    $this->run("ALTER TABLE `{$stickerTable}` ADD `scale_{$attributeCode}` SMALLINT DEFAULT NULL AFTER `position`");
}

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
$this->run("INSERT INTO `{$stickerTable}` (`sticker_id`, `is_active`, `title`, `image`, `position`, `scale_media_gallery`, `scale_thumbnail`, `scale_small_image`, `scale_image`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hot', '/h/o/hot-icon-3.png', 'top-left', 40, 40, 40, 40, '2013-05-30 02:00:43', '2013-05-30 02:00:43'),
(2, 1, '5% Green', '/5/-/5-icon-1.png', 'top-left', 40, 40, 40, 40, '2013-05-30 04:42:40', '2013-05-30 04:47:03'),
(3, 1, '10% Purple', '/1/0/10-icon-1.png', 'bottom-center', 40, 40, 40, 40, '2013-05-30 04:48:36', '2013-05-30 04:48:36'),
(4, 1, 'Special 1', '/s/p/special-offer-2.png', 'top-left', 40, 40, 40, 40, '2013-05-30 04:50:53', '2013-05-30 04:50:53'),
(5, 1, 'Deal Red', '/d/e/deal-1.png', 'top-right', 40, 40, 40, 40, '2013-05-30 04:51:16', '2013-05-30 04:51:16');
");
$this->endSetup();