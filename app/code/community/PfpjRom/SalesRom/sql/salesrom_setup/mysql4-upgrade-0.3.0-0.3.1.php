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
 * Updates label for pfpj_cui from 'Tax Registration Number'(CUI) to 'Tax Identification Number'(CIF).
 *
 * @category   PfpjRom
 * @package    PfpjRom_SalesRom
 * @author     Daniel Ifrim
 */

$installer = $this;
/* @var $installer PfpjRom_SalesRom_Model_Entity_Setup */
$installer->startSetup();

$entity_type_code = 'order_address';

$installer->updateAttribute($entity_type_code, 'pfpj_cui', 'frontend_label', 'Tax Identification Number');

$installer->endSetup();