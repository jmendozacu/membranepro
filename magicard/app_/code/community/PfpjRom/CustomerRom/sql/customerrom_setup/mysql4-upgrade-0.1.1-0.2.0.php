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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) Daniel Ifrim
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adds most used customer address fields in Romania's stores.
 *
 * @category   PfpjRom
 * @package    PfpjRom_CustomerRom
 * @author     Daniel Ifrim
 */

$installer = $this;
/* @var $installer PfpjRom_CustomerRom_Model_Entity_Setup */
$installer->startSetup();

$customer_address_entity_type_id = $installer->getEntityTypeId('customer_address');

$installer->updateAttribute('customer_address', 'pfpj_tip_pers', 'backend', 'customerrom/entity_address_attribute_backend_tippers');

$installer->addAttribute('customer_address', 'pfpj_for_billing', array(
    'type' => 'int', // same as default
    'input' => 'checkbox', // same as default
    'backend' => 'customerrom/entity_address_attribute_backend_forbilling',
    'frontend' => 'customerrom/entity_address_attribute_frontend_forbilling',
    'source' => 'eav/entity_attribute_source_boolean',
    'input_renderer' => 'customerrom/address_renderer_forbilling',
    'label'    => 'Is address for billing',
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'    => (!$installer->getTippersConfig()->isFieldDisabled('pfpj_for_billing')),
    'required' => $installer->getTippersConfig()->isFieldRequired('pfpj_for_billing'),
    'user_defined' => true,
    'visible_on_front' => false, // same as default
    'used_for_price_rules' => true, // same as default
    'position' => 1,
    'sort_order' => 30,
    'default' => 0,
));
$for_billing_attribute = $installer->getAttribute($customer_address_entity_type_id, 'pfpj_for_billing');

$installer->addAttributeOption(array(
	'attribute_id' => $for_billing_attribute['attribute_id'],
	'value' => array(0 => array(0 => 0, 1 => 0)),
	'order' => array(0 => 4),
	'delete' => array(0 => ''),
));

$installer->addAttributeOption(array(
	'attribute_id' => $for_billing_attribute['attribute_id'],
	'value' => array(0 => array(0 => 1, 1 => 1)),
	'order' => array(0 => 8),
	'delete' => array(0 => ''),
));
/*$intOptionId = $installer->getAttributeOptionIdByValue($for_billing_attribute, $installer->getTippersConfig()->getDefaultValue('pfpj_for_billing'));
$installer->updateAttribute('customer_address', 'pfpj_for_billing', 'default_value', $intOptionId);*/
$installer->updateAttribute('customer_address', 'pfpj_for_billing', 'default_value', $installer->getTippersConfig()->getDefaultValue('pfpj_for_billing'));



$installer->addAttribute('customer_address', 'pfpj_for_shipping', array(
    'type' => 'int', // same as default
    'input' => 'checkbox', // same as default
    'backend' => 'customerrom/entity_address_attribute_backend_forshipping',
    'frontend' => 'customerrom/entity_address_attribute_frontend_forshipping',
    'source' => 'eav/entity_attribute_source_boolean',
    'input_renderer' => 'customerrom/address_renderer_forshipping',
    'label'    => 'Is address for shipping',
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'    => (!$installer->getTippersConfig()->isFieldDisabled('pfpj_for_shipping')),
    'required' => $installer->getTippersConfig()->isFieldRequired('pfpj_for_shipping'),
    'user_defined' => true,
    'visible_on_front' => false, // same as default
    'used_for_price_rules' => true, // same as default
    'position' => 1,
    'sort_order' => 31,
    'default' => 1,
));
$for_shipping_attribute = $installer->getAttribute($customer_address_entity_type_id, 'pfpj_for_shipping');

$installer->addAttributeOption(array(
	'attribute_id' => $for_shipping_attribute['attribute_id'],
	'value' => array(0 => array(0 => 0, 1 => 0)),
	'order' => array(0 => 4),
	'delete' => array(0 => ''),
));

$installer->addAttributeOption(array(
	'attribute_id' => $for_shipping_attribute['attribute_id'],
	'value' => array(0 => array(0 => 1, 1 => 1)),
	'order' => array(0 => 8),
	'delete' => array(0 => ''),
));
/*$intOptionId = $installer->getAttributeOptionIdByValue($for_shipping_attribute, $installer->getTippersConfig()->getDefaultValue('pfpj_for_shipping'));
$installer->updateAttribute('customer_address', 'pfpj_for_shipping', 'default_value', $intOptionId);*/
$installer->updateAttribute('customer_address', 'pfpj_for_shipping', 'default_value', $installer->getTippersConfig()->getDefaultValue('pfpj_for_shipping'));

$installer->endSetup();