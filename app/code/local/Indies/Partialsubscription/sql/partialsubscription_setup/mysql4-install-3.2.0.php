<?php
$installer = $this;

$installer->startSetup();

$installer->run("
		ALTER TABLE  `".$installer->getTable('sales/order')."` ADD  `cim_real_id` VARCHAR(20);
		ALTER TABLE  `".$installer->getTable('sales/order')."` ADD  `cim_real_payment_id` VARCHAR(20);
		");

$installer->endSetup();