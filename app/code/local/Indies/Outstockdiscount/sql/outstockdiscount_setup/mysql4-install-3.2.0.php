<?php

$installer = $this;

$installer->startSetup();

$installer->run("
		
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `outstock_discount_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_outstock_discount_amount` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `outstock_discount_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `base_outstock_discount_amount` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `outstock_discount_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_outstock_discount_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `outstock_discount_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `base_outstock_discount_amount` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `outstock_discount_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_outstock_discount_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
		
	ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `outstock_discount_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `base_outstock_discount_amount` DECIMAL( 10, 2 ) NOT NULL;

	");

$installer->endSetup(); 