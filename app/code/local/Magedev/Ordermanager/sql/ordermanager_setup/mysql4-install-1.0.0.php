<?php

$installer = $this; 
$installer->startSetup();
$resource = Mage::getResourceModel('sales/order_collection');
$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

if(!method_exists($resource, 'getEntity')){
 
    try {
$tableOrder = $this->getTable('sales_flat_order');
$tableGrid = $this->getTable('sales_flat_order_grid');

//===================================add field 'is_archieved' in (sales flat order + grid) table=====================================================
	    
		$query1 = 'ALTER TABLE `' . $tableOrder . '` ADD COLUMN `is_archieved` tinyint(1) NOT NULL DEFAULT 0' ; 
        $connection->query($query1);
	
	    $query1A = 'ALTER TABLE `' . $tableGrid . '` ADD COLUMN `is_archieved` tinyint(1) NOT NULL DEFAULT 0' ; 
		$connection->query($query1A);


//===================================add field 'is_edit' in (sales flat order + grid) table=====================================================
	    
		$query2 = 'ALTER TABLE `' . $tableOrder . '` ADD COLUMN `is_edit` tinyint(1) NOT NULL DEFAULT 0' ; 
        $connection->query($query2);

	    $query2A = 'ALTER TABLE `' . $tableGrid . '` ADD COLUMN `is_edit` tinyint(1) NOT NULL DEFAULT 0' ; 
		$connection->query($query2A);
 
//===================================add field 'edit_comments' in (sales flat order + grid) table=====================================================

	    $query3 = 'ALTER TABLE `' . $tableOrder . '` ADD COLUMN `edit_comments` TEXT CHARACTER SET utf8 DEFAULT NULL' ; 
        $connection->query($query3);

	    $query3A = 'ALTER TABLE `' . $tableGrid . '` ADD COLUMN `edit_comments` TEXT CHARACTER SET utf8 DEFAULT NULL' ; 
		$connection->query($query3A);
  
			
    } catch (Exception $e) {
 		Mage::log($e.'error in create table field');
    }

}

$installer->endSetup();
