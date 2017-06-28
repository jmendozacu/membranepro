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

//create table for sticker
$this->run("
	DROP TABLE IF EXISTS `{$stickerTable}`;
	CREATE TABLE `{$stickerTable}` (
	    `sticker_id`   int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Sticker ID',
	    `is_active`  tinyint DEFAULT 0,
	    `title`      varchar(255) DEFAULT NULL,

	    `opacity`    int DEFAULT 0,
        `image`      varchar(255) DEFAULT NULL,
        `position`   varchar(32),

	    `image_size_image`       varchar(16) DEFAULT null,
	    `image_size_thumbnail`   varchar(16) DEFAULT null,
	    `image_size_small_image` varchar(16) DEFAULT null,

		`created_at` timestamp NULL DEFAULT NULL COMMENT 'Creation Time',
  		`updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Update Time',
		PRIMARY KEY (`sticker_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stickers Entity Table';
");

$this->endSetup();