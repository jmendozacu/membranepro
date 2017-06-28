<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('orderfollowupemail_log')};

CREATE TABLE {$this->getTable('orderfollowupemail_log')} (
  `log_id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `processed_date` DATETIME DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	");

$installer->run("
	INSERT INTO {$this->getTable('orderfollowupemail_log')} (
		order_id,
		processed_date
	) (
		SELECT
			`main_table`.`entity_id`,
			NOW()
		FROM
			{$this->getTable('sales/order')} AS `main_table`
	)
");

$installer->endSetup();

?>