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
/* @var $this Mage_Core_Model_Resource_Setup */
$this->startSetup();

Mage::getConfig()->getResourceModel()->loadToXml(Mage::getConfig());
$defaultProductId = (int)Mage::getConfig()->getNode('general/aitoptionstemplate/default_product_id', 'default');

//$product = Mage::getModel('catalog/product')->load($defaultProductId);
$type = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
if (!empty($type))
{
    $this->run("
    	UPDATE {$this->getTable('catalog/product')} SET entity_type_id = {$type->getEntityTypeId()}, attribute_set_id = {$type->getDefaultAttributeSetId()} WHERE entity_id = {$defaultProductId};
    ");
}

$attr = Mage::getModel('catalog/entity_attribute')->loadByCode($type->getEntityTypeId(), 'status');

//fix for when install multilocation and option templates at the same time than option temlate use not created yet table of multilocation.
try
{
    $this->run("
        INSERT IGNORE INTO {$this->getTable('cataloginventory/stock_item')} (product_id, stock_id) VALUES ({$defaultProductId}, 1);
    ");
}
catch (Exception $e)
{
    $tableName = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item');

    $this->run("
    	INSERT IGNORE INTO {$tableName}(product_id,stock_id) VALUES({$defaultProductId}, 1);
    ");
}

$store_id = Mage_Core_Model_App::ADMIN_STORE_ID;
$this->run("
    INSERT IGNORE INTO {$this->getTable('catalog_product_entity_int')}(entity_type_id,attribute_id,store_id,entity_id,value) VALUES({$type->getEntityTypeId()},{$attr->getId()},{$store_id},{$defaultProductId}, 1);
");

$attr = Mage::getModel('catalog/entity_attribute')->loadByCode($type->getEntityTypeId(),'visibility');

$this->run("
    INSERT IGNORE INTO {$this->getTable('catalog_product_entity_int')}(entity_type_id,attribute_id,store_id,entity_id,value) VALUES({$type->getEntityTypeId()},{$attr->getId()},{$store_id},{$defaultProductId}, 4);
");

$this->endSetup();