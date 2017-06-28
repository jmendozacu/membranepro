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
 * Adds most used order address fields on romanian stores.
 *
 * @category   PfpjRom
 * @package    PfpjRom_CustomerRom
 * @author     Daniel Ifrim
 */

$installer = $this;
/* @var $installer PfpjRom_SalesRom_Model_Entity_Setup */
$installer->startSetup();

foreach ($installer->getTippersConfig()->getFields() as $fieldName => $fieldConfig) {
	$installer->getConnection()->addColumn($installer->getTable('sales_flat_order_address'), $fieldName, 'varchar(255) NULL');
	$installer->getConnection()->addColumn($installer->getTable('sales_flat_quote_address'), $fieldName, 'varchar(255) NULL');
}

$installer->endSetup();