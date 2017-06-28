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

$entity_type_code = 'customer_address';

$entityTypeId = $installer->getEntityTypeId($entity_type_code);
$pfpj_attr_list = array(
	'pfpj_tip_pers' => 29,
	'pfpj_cui'=> 32,
	'pfpj_reg_com'=> 33,
	'pfpj_banca'=> 34,
	'pfpj_iban'=> 35,
	'pfpj_cnp'=> 36,
	'pfpj_serienr_buletin'=> 37,
	'pfpj_for_billing'=> 30,
	'pfpj_for_shipping'=> 31
);
$sets = $installer->_conn->fetchAll('select * from '.$installer->getTable('eav/attribute_set').' where entity_type_id=?', $entityTypeId);
foreach ($pfpj_attr_list as $code => $sortOrder) {
	$installer->updateAttribute($entity_type_code, $code, 'is_user_defined', 0);
	
	$pfpj_attr = $installer->getAttribute($entityTypeId, $code);
	foreach ($sets as $set) {
        $installer->addAttributeToSet($entityTypeId, $set['attribute_set_id'], $installer->_generalGroupName, $code, $sortOrder);
    }
}

$installer->updateAttribute($entity_type_code, 'pfpj_cnp', 'is_visible', 1);
$installer->updateAttribute($entity_type_code, 'pfpj_cnp', 'is_required', 0);
$installer->updateAttribute($entity_type_code, 'pfpj_serienr_buletin', 'is_visible', 0);
$installer->updateAttribute($entity_type_code, 'pfpj_serienr_buletin', 'isrequired', 0);

// rollback to default since Magento added feature postal code optional (Configuration > General > Postal code is optional for countries ...)
$installer->updateAttribute($entity_type_code, 'postcode', 'is_required', 1);

$installer->endSetup();