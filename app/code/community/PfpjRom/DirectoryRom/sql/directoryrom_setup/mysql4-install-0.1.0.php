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
 * Adds regions(judete) of Romania.
 *
 * @category   PfpjRom
 * @package    PfpjRom_DirectoryRom
 * @author     Daniel Ifrim
 */

$installer = $this;
/* @var $installer PfpjRom_DirectoryRom_Model_Setup */
$installer->startSetup();

// Do not delete RO regions that already exists in db and bypass:
if (!$installer->checkJudeteAlreadyExists())
{
/*$installer->run("
DELETE FROM `{$installer->getTable('directory_country_region')}` WHERE country_id = 'RO';

DELETE 
	`{$installer->getTable('directory_country_region_name')}`,
	`{$installer->getTable('directory_country_region')}` 
FROM `{$installer->getTable('directory_country_region')}`
	LEFT JOIN `{$installer->getTable('directory_country_region_name')}`
		ON `{$installer->getTable('directory_country_region')}`.`region_id`=`{$installer->getTable('directory_country_region_name')}`.`region_id`
WHERE
	`{$installer->getTable('directory_country_region')}`.`country_id`='RO';

");*/

// Note: dirname(__FILE__) is used instead of __DIR__ as the latter was not
// available prior to PHP 5.3.0.
$fp = fopen( dirname(__FILE__) . '/districts-0.1.0.txt', 'rb');

while ($row = fgets($fp)) {
	$row_list = explode(",", trim(trim(trim($row), ")"), "("));
	foreach ($row_list as $key => $value)
		$row_list[$key] = trim(trim($value), "'");

    $installer->run("
    	INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES 
    		('" . $row_list[0] . "', '" . $row_list[1] . "', '" . $row_list[2] . "')
    	;
		INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
			('en_US', LAST_INSERT_ID(), '" . $row_list[3] . "'),
			('ro_RO', LAST_INSERT_ID(), '" . $row_list[2] . "');
    ");
}

fclose($fp);
} else {
	// TO DO: to come.
}

$installer->endSetup();

/***********************************************************
# Eye checker
# The query bellow should tell you if romanian districts in your store db are ok.
# 
# The number of rows returned by the query should be:
#	 
#	47(number of districts) X 2 (number of locale) = 94 rows.
# 
# Just to be safe check each resulted row.
# ->

SELECT
	`dcr`.`region_id` AS `[Region Id]`,
	`dcr`.`country_id` AS `[Country Code]`,
	`dcr`.`code` AS `[District Code]`,
	`dcr`.`default_name` AS `[Default Name]`,

	`dcrn`.`locale` AS `[Language Locale Code]`,
	`dcrn`.`region_id` AS `[PROOF - Region Id]`,
	`dcrn`.`name` AS `Translation of [Default Name] - [Locale Name]`
FROM `directory_country_region` AS `dcr`
	LEFT JOIN `directory_country_region_name` AS `dcrn`
		ON `dcr`.`region_id` = `dcrn`.`region_id`
WHERE `dcr`.`country_id` = 'RO'
ORDER BY `dcr`.`region_id` ASC
LIMIT 0,1000
***********************************************************/