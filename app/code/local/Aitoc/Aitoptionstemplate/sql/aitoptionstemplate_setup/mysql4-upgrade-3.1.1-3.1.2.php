<?php
/**
 * Custom Options Templates
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitoptionstemplate
 * @version      3.2.2
 * @license:     FoqAPsDYiV5k0g2ao7Zx8pPoJiVNkUYZBpxixzEerG
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$table_prefix = Mage::getConfig()->getTablePrefix();
if(isset($table_prefix) && $table_prefix != '')
{
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/product_option_dependable')};  
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/product_option_dependable')} (
  `row_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL DEFAULT '0',
  `option_id` int(11) unsigned NOT NULL DEFAULT '0',
  `option_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `option_value_id` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`row_id`),
  KEY `product_id` (`product_id`),
  KEY `option_id` (`option_id`),
  CONSTRAINT `AITOC_DEPENDABLE_OPTION_IBFK_1` FOREIGN KEY (`option_id`) REFERENCES `{$installer->getTable('catalog/product_option')}` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/product_option_dependable_child')};  
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/product_option_dependable_child')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `row_id` int(11) unsigned NOT NULL DEFAULT '0',
  `child_value_id` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `row_id` (`row_id`,`child_value_id`),
  KEY `row_id_2` (`row_id`),
  CONSTRAINT `AITOC_DEPENDABLE_OPTION_CHILD_IBFK_1` FOREIGN KEY (`row_id`) REFERENCES `{$installer->getTable('aitoptionstemplate/product_option_dependable')}` (`row_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
"); 
}
$installer->endSetup();