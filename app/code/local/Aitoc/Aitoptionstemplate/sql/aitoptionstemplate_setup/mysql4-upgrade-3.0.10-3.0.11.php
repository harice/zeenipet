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
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('aitoptionstemplate/reserve_catalog_product_option_typeprice')}
    CHANGE COLUMN `price_type` `price_type` enum('fixed','percent') NOT NULL DEFAULT 'fixed';
");
$installer->endSetup();