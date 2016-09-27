<?php
/**
 * Humsayaa AbsolutePricing
 *
 * @category    Mage
 * @package     Mage_Resource_Sql_Setup
 * @author      AbsolutePricing Team <enquiries@absolutepricing.com>
 *
 * Alters Table - catalog_product_option_price
 *
 * Alters Table - catalog_product_option_type_price
 *
*/

$installer = $this;
$this->startSetup();
$installer->run("
ALTER TABLE {$this->getTable('catalog_product_option_price')}
    CHANGE `price_type` `price_type` ENUM('fixed','percent','absolute') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'fixed' NOT NULL;
	
ALTER TABLE {$this->getTable('catalog_product_option_type_price')}
    CHANGE `price_type` `price_type` ENUM('fixed','percent','absolute') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'fixed' NOT NULL;
");
$this->endSetup();
