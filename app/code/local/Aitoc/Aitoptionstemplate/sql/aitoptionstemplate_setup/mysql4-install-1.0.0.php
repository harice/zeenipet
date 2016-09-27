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

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/aittemplate')};
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/aittemplate')} (
  `template_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `required_options` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/aitoption2tpl')};  
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/aitoption2tpl')} (
  `option_id` int(10) unsigned NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
    UNIQUE KEY `UNQ_AITOC_TPL_OPTION` (`template_id`,`option_id`),
    CONSTRAINT `FK_AITOC_OPTION_TPL_INDEX_TPL_ENTITY` FOREIGN KEY (`template_id`) REFERENCES `{$installer->getTable('aitoptionstemplate/aittemplate')}` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/aitproduct2tpl')};
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/aitproduct2tpl')} (
  `product_id` int(10) unsigned NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL default '0',
    UNIQUE KEY `UNQ_AITOC_TPL_PRODUCT` (`template_id`,`product_id`),
    KEY `IDX_AITOC_TPL_SORT` (`template_id`,`sort_order`),
    CONSTRAINT `FK_AITOC_PRODUCT_TPL_INDEX_PRODUCT_ENTITY` FOREIGN KEY (`product_id`) REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_AITOC_PRODUCT_TPL_INDEX_TPL_ENTITY` FOREIGN KEY (`template_id`) REFERENCES `{$installer->getTable('aitoptionstemplate/aittemplate')}` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/aitproduct2required')};
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/aitproduct2required')} (
  `product_id` int(10) unsigned NOT NULL,
  `required_options` tinyint(1) unsigned NOT NULL default '0',
   KEY `IDX_AITOC_PROD_REQ` (`product_id`),
   CONSTRAINT `FK_AITOC_PRODUCT_REQUIRED_INDEX_PRODUCT_ENTITY` FOREIGN KEY (`product_id`) REFERENCES `{$installer->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;  
    ");

    $installer->run("
INSERT INTO {$this->getTable('catalog/product')} VALUES(0, 4, 1, 'simple', NULL, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
    ");
    


$installer->endSetup();