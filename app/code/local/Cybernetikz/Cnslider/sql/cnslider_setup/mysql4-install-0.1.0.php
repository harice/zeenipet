<?php
$installer = $this;
$installer->startSetup(); 
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('cybernetikz_cnslider/slider')} (
  `slider_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `slider_image` varchar(250) NOT NULL,
  `link_url` varchar(250) DEFAULT NULL,
  `content` text DEFAULT NULL,  
  `store_id` int(10) DEFAULT '0',
  `is_active` BINARY(1) DEFAULT '1',
  `sort_order` int(10) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Creation Time',
  PRIMARY KEY  (`slider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();