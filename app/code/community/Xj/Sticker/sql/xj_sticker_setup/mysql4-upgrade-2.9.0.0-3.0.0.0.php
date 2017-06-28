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

/** @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
/** @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $this->getConnection();
$stickerTable = $this->getTable('xj_sticker/sticker');

$this->startSetup();

//#1. Delete old attribute
/** @var $attributeModel Mage_Eav_Model_Entity_Attribute */
$attributeModel = Mage::getModel('eav/entity_attribute');
$attributeModel->load('xj_sticker_id', 'attribute_code');
if ($attributeModel->getId()) {
    $attributeModel->delete();
}

//#2. Create new columns
/** @var $helper Xj_Sticker_Helper_Data */
$helper = Mage::helper('xj_sticker');
foreach($helper->getAttributes() as $attributeCode) {
    $this->run("ALTER TABLE `{$stickerTable}` ADD `scale_{$attributeCode}` SMALLINT DEFAULT NULL AFTER `position`");
}

//#3, Set new scale size for each sticker
$defaultScale = Xj_Sticker_Model_Sticker::DEFAULT_SCALE;
foreach($helper->getAttributes() as $attributeCode) {
    $this->run("UPDATE `{$stickerTable}` SET`scale_{$attributeCode}` = {$defaultScale}");
}

//#4, Disable old stickers
$this->run("UPDATE `{$stickerTable}` SET `is_active` = " . Xj_Sticker_Model_Sticker::STATUS_DISABLED);

//#5. Drop old columns
$this->run("ALTER TABLE `{$stickerTable}` DROP `image_size_image`");
$this->run("ALTER TABLE `{$stickerTable}` DROP `image_size_thumbnail`");
$this->run("ALTER TABLE `{$stickerTable}` DROP `image_size_small_image`");
$this->run("ALTER TABLE `{$stickerTable}` DROP `opacity`");

$this->endSetup();