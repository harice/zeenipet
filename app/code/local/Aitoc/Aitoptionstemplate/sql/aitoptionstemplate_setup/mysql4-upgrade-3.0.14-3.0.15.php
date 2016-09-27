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
 * @copyright  Copyright (c) 2011 AITOC, Inc. 
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

// FIXED http://mantis.it/view.php?id=27449 #2
$productId = intval(Mage::getConfig()->getNode('general/aitoptionstemplate/default_product_id', 'default'));
if ($productId)
{
    $installer->run('
        UPDATE
            ' . $this->getTable('catalog_product_entity_varchar') . '
        SET
            store_id  = ' . Mage_Core_Model_App::ADMIN_STORE_ID . '
        WHERE
            entity_id = ' . $productId . '
        LIMIT 1
    ');
}

// FIXED http://mantis.it/view.php?id=27449 #1
Mage::app()->getCache()->clean();

$installer->endSetup();