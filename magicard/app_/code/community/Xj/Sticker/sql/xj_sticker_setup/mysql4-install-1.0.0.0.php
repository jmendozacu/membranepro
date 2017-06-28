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

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'xj_sticker_id',
    array(
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,

        'input'         => 'multiselect',
        'type'          => 'text',
        'label'         => 'Product Stickers',
        'source'        => 'xj_sticker/product_attribute_source_sticker',
        'backend'       => 'xj_sticker/product_attribute_backend_sticker',

		'used_in_product_listing' => true, //add to collection
        'visible'           => true, //add to admin UI
        'visible_on_front'  => false,
        'required'          => false,
        'user_defined'      => false,
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'unique'            => false,
    ));

$installer->endSetup();