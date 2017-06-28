<?php
$this->startSetup();

$this->addAttribute('catalog_category', 'filter_products_exclude_attr', array(
    'group'            => 'Product Filters',
    'label'            => 'Exclude options from filter',
    'type'             => 'varchar',
    'input'            => 'text',
    'backend'          => '',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'required'         => false,
    'default'          => '',
    'visible'          => true,
    'visible_on_front' => true,
    'user_defined'     => true,
    'sort_order'       => 10,
    'note'             => 'Add option labels separated by comma. Ex. Autocolant, Ecologic, Securitate vizuala',
));
 
$this->endSetup();