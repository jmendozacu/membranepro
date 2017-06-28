<?php

$installer = $this;
$installer->startSetup();
try{
$installer->run("
ALTER TABLE {$this->getTable('sales/order')}
  DROP COLUMN `dailydeals`;    
");
}catch(Exception $e){}
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('dailydeal')};
CREATE TABLE {$this->getTable('dailydeal')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `products` varchar(255) NULL,
  `products_deal` varchar(255) NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `thumbnail_image` varchar(255) NULL,
  `save` varchar(255) NOT NULL default '0',
  `deal_price` decimal(12,2) NOT NULL,
  `quantity` varchar(255) NOT NULL default '0',
  `sold` int(11) NOT NULL default '0',
  `start_time` datetime NULL,
  `deal_time` int(11) NULL,
  `time_left` datetime NULL,
  `close_time` datetime NULL,
  `status` int(1) NOT NULL default '1',
  `store_id` text NULL,
  `process` int(1) NOT NULL default '0',
  `is_random` int(1) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE {$this->getTable('sales/order')}
  ADD COLUMN `dailydeals` varchar(255) NULL;
    ");

$installer->endSetup(); 