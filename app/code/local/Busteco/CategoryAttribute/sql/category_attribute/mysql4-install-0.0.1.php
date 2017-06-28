<?php
$this->startSetup();

$this->addAttribute('catalog_category', 'filter_products_by_attribute', array(
    'group'            => 'Product Filters',
    'label'            => 'Add Product Filter By',
    'type'             => 'text',
    'input'            => 'multiselect',
    'backend'          => 'eav/entity_attribute_backend_array',
    'source'           => 'categoryattribute/resource_productattributes',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'required'         => false,
    'default'          => '',
    'visible'          => true,
    'visible_on_front' => true,
    'user_defined'     => true,
    'sort_order'       => 5,
    'note'             => '',
));
 
$this->endSetup();