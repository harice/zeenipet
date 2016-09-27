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
DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_option')};  
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_option')} (
 `reserve_id` int(10) NOT NULL AUTO_INCREMENT,
 `option_id` int(10) unsigned NOT NULL,
 `product_id` int(10) unsigned NOT NULL DEFAULT '0',
 `type` varchar(50) NOT NULL,
 `is_require` tinyint(1) NOT NULL DEFAULT '1',
 `sku` varchar(64) NOT NULL,
 `max_characters` int(10) unsigned DEFAULT NULL,
 `file_extension` varchar(50) DEFAULT NULL,
 `image_size_x` smallint(5) unsigned NOT NULL,
 `image_size_y` smallint(5) unsigned NOT NULL,
 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`reserve_id`)
) ENGINE=InnoDB   DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_option2template')};
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_option2template')}(
`reserve_id` int(10) NOT NULL AUTO_INCREMENT,  
 `option_id` int(10) NOT NULL,
 `template_id` int(10) NOT NULL ,
  PRIMARY KEY (`reserve_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_optionprice')}; 
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_optionprice')} (
 `reserve_id` int(10) NOT NULL AUTO_INCREMENT,
 `option_price_id` int(10) unsigned NOT NULL,
 `option_id` int(10) unsigned NOT NULL DEFAULT '0',
 `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
 `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
 `price_type` enum('fixed','percen') NOT NULL DEFAULT 'fixed',
  PRIMARY KEY (`reserve_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_optiontemplate')}; 
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_optiontemplate')} (
 `reserve_id` int(10) NOT NULL AUTO_INCREMENT,
 `template_id` int(10) NOT NULL,
 `title` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `is_active` tinyint(1) NOT NULL,
 `required_options` tinyint(1) NOT NULL,
  PRIMARY KEY (`reserve_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_optiontitle')};  
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_optiontitle')} (
 `reserve_id` int(10) NOT NULL AUTO_INCREMENT,
 `option_title_id` int(10) unsigned NOT NULL,
 `option_id` int(10) unsigned NOT NULL DEFAULT '0',
 `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
 `title` varchar(255) NOT NULL,
  PRIMARY KEY (`reserve_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_option_typeprice')};  
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_option_typeprice')} (
 `reserve_id` int(10) NOT NULL AUTO_INCREMENT,
 `option_type_price_id` int(10) unsigned NOT NULL,
 `option_type_id` int(10) unsigned NOT NULL DEFAULT '0',
 `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
 `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
 `price_type` enum('fixed','percen') NOT NULL DEFAULT 'fixed',
  PRIMARY KEY (`reserve_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_option_typetitle')};
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_option_typetitle')} (
 `reserve_id` int(10) NOT NULL AUTO_INCREMENT,
 `option_type_title_id` int(10) unsigned NOT NULL,
 `option_type_id` int(10) unsigned NOT NULL DEFAULT '0',
 `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
 `title` varchar(255) NOT NULL,
  PRIMARY KEY (`reserve_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_option_typevalue')}; 
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_option_typevalue')} (
 `reserve_id` int(10) NOT NULL AUTO_INCREMENT,
 `option_type_id` int(10) unsigned NOT NULL,
 `option_id` int(10) unsigned NOT NULL DEFAULT '0',
 `sku` varchar(64) NOT NULL,
 `sort_order` int(10) unsigned NOT NULL DEFAULT '0', 
 PRIMARY KEY (`reserve_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_required')};
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_required')} (
 `reserve_id` int(10) NOT NULL AUTO_INCREMENT,
 `product_id` int(10) NOT NULL,
 `required_options` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`reserve_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_template')};
CREATE TABLE IF NOT EXISTS {$this->getTable('aitoptionstemplate/reserve_catalog_product_template')} (
 `reserve_id` int(10) NOT NULL AUTO_INCREMENT,
 `product_sku` varchar(64) DEFAULT NULL,
 `template_id` int(10) NOT NULL,
 `sort_order` int(10) NOT NULL,
  PRIMARY KEY (`reserve_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

Mage::getConfig()->getResourceModel()->loadToXml(Mage::getConfig());
$defaultProductId = (int)Mage::getConfig()->getNode('general/aitoptionstemplate/default_product_id', 'default');

$product = Mage::getModel('catalog/product');
$product->setId($defaultProductId);
$product->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID);

$product->addAttributeUpdate(
    'name',
    '!DO NOT DELETE ME! AITOC Custom Options Templates special product',
    Mage_Core_Model_App::ADMIN_STORE_ID
);

$product->addAttributeUpdate(
    'visibility',
    Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE,
    Mage_Core_Model_App::ADMIN_STORE_ID
);

$product->addAttributeUpdate(
    'status',
    Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
    Mage_Core_Model_App::ADMIN_STORE_ID
);

$installer->endSetup();