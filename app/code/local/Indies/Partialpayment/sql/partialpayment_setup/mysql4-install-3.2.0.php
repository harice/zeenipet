<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$installer->run("
	-- DROP TABLE IF EXISTS {$this->getTable('partial_payment')};
	CREATE TABLE {$this->getTable('partial_payment')}(
		`partial_payment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`order_id` varchar(30) NOT NULL,
		`customer_id` int(11) unsigned NOT NULL,
		`customer_first_name` varchar(255) NOT NULL,
		`customer_last_name` varchar(255) NOT NULL,
		`customer_email` varchar(255) NOT NULL,
		`total_amount` decimal(12,4) NOT NULL,
		`paid_amount` decimal(12,4) NOT NULL,
		`remaining_amount` decimal(12,4) NOT NULL,
		`total_installment` int(2) unsigned NOT NULL,
		`paid_installment` int(2) unsigned NOT NULL,
		`remaining_installment` int(2) unsigned NOT NULL,
		`partial_payment_status` varchar(20) NOT NULL DEFAULT 'Processing',
		`created_date` date NOT NULL,
		`updated_date` date NOT NULL,
		`enabled_with_surcharge` int(1) unsigned NOT NULL,
		PRIMARY KEY (`partial_payment_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
	-- DROP TABLE IF EXISTS {$this->getTable('partial_payment_installment')};
	CREATE TABLE {$this->getTable('partial_payment_installment')}(
		`installment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`partial_payment_id` int(11) unsigned NOT NULL,
		`installment_amount` decimal(12,4) NOT NULL,
		`installment_due_date` date DEFAULT NULL,
		`installment_paid_date` date DEFAULT NULL,
		`installment_status` varchar(20) NOT NULL DEFAULT 'Remaining',
		`payment_method` text DEFAULT NULL,
		`txn_id` varchar(100) DEFAULT NULL,
		PRIMARY KEY (`installment_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
	-- DROP TABLE IF EXISTS {$this->getTable('partial_payment_product')};
	CREATE TABLE {$this->getTable('partial_payment_product')}(
		`partial_payment_product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`partial_payment_id` int(11) unsigned NOT NULL,
		`order_id` int(11) unsigned NOT NULL,
		`sales_flat_order_item_id` int(11) unsigned NOT NULL,
		`product_id` int(11) unsigned NOT NULL,
		`product_name` varchar(255) NOT NULL,
		`product_type` varchar(255) NOT NULL,
		`total_installment` int(2) unsigned NOT NULL,
		`paid_installment` int(2) unsigned NOT NULL,
		`remaining_installment` int(2) unsigned NOT NULL,
		`total_amount` DECIMAL(12,4) NOT NULL,
		`paid_amount` DECIMAL(12,4) NOT NULL,
		`remaining_amount` DECIMAL(12,4) NOT NULL,
		`out_of_stock_discount_value` DECIMAL(12,4) NOT NULL,
		`out_of_stock_discount_calculation_type` varchar(20) NOT NULL,
		`surcharge_value` DECIMAL(12,4) NOT NULL,
		`surcharge_calculation_type` varchar(20) NOT NULL,
		`is_out_of_stock` int(1) NOT NULL,
		`notified_for_stock_recovery` int(1) NOT NULL ,
		`notified_date` DATE DEFAULT NULL,
		PRIMARY KEY (`partial_payment_product_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
	CREATE TABLE `{$this->getTable('partial_payment_capture')}` (
	  `entity_id` int(10) unsigned NOT NULL auto_increment,
	  `parent_id` int(10) unsigned NOT NULL,
	  `base_shipping_captured` decimal(12,4) default NULL,
	  `shipping_captured` decimal(12,4) default NULL,
	  `amount_refunded` decimal(12,4) default NULL,
	  `base_amount_paid` decimal(12,4) default NULL,
	  `amount_canceled` decimal(12,4) default NULL,
	  `base_amount_authorized` decimal(12,4) default NULL,
	  `base_amount_paid_online` decimal(12,4) default NULL,
	  `base_amount_refunded_online` decimal(12,4) default NULL,
	  `base_shipping_amount` decimal(12,4) default NULL,
	  `shipping_amount` decimal(12,4) default NULL,
	  `amount_paid` decimal(12,4) default NULL,
	  `amount_authorized` decimal(12,4) default NULL,
	  `base_amount_ordered` decimal(12,4) default NULL,
	  `base_shipping_refunded` decimal(12,4) default NULL,
	  `shipping_refunded` decimal(12,4) default NULL,
	  `base_amount_refunded` decimal(12,4) default NULL,
	  `amount_ordered` decimal(12,4) default NULL,
	  `base_amount_canceled` decimal(12,4) default NULL,
	  `ideal_transaction_checked` tinyint(1) unsigned default NULL,
	  `quote_payment_id` int(10) default NULL,
	  `additional_data` text,
	  `cc_exp_month` varchar(255) default NULL,
	  `cc_ss_start_year` varchar(255) default NULL,
	  `echeck_bank_name` varchar(255) default NULL,
	  `method` varchar(255) default NULL,
	  `cc_debug_request_body` varchar(255) default NULL,
	  `cc_secure_verify` varchar(255) default NULL,
	  `cybersource_token` varchar(255) default NULL,
	  `ideal_issuer_title` varchar(255) default NULL,
	  `protection_eligibility` varchar(255) default NULL,
	  `cc_approval` varchar(255) default NULL,
	  `cc_last4` varchar(255) default NULL,
	  `cc_status_description` varchar(255) default NULL,
	  `echeck_type` varchar(255) default NULL,
	  `paybox_question_number` varchar(255) default NULL,
	  `cc_debug_response_serialized` varchar(255) default NULL,
	  `cc_ss_start_month` varchar(255) default NULL,
	  `echeck_account_type` varchar(255) default NULL,
	  `last_trans_id` varchar(255) default NULL,
	  `cc_cid_status` varchar(255) default NULL,
	  `cc_owner` varchar(255) default NULL,
	  `cc_type` varchar(255) default NULL,
	  `ideal_issuer_id` varchar(255) default NULL,
	  `po_number` varchar(255) default NULL,
	  `cc_exp_year` varchar(255) default NULL,
	  `cc_status` varchar(255) default NULL,
	  `echeck_routing_number` varchar(255) default NULL,
	  `account_status` varchar(255) default NULL,
	  `anet_trans_method` varchar(255) default NULL,
	  `cc_debug_response_body` varchar(255) default NULL,
	  `cc_ss_issue` varchar(255) default NULL,
	  `echeck_account_name` varchar(255) default NULL,
	  `cc_avs_status` varchar(255) default NULL,
	  `cc_number_enc` varchar(255) default NULL,
	  `cc_trans_id` varchar(255) default NULL,
	  `flo2cash_account_id` varchar(255) default NULL,
	  `paybox_request_number` varchar(255) default NULL,
	  `address_status` varchar(255) default NULL,
	  `additional_information` text,
	  PRIMARY KEY  (`entity_id`),
	  KEY `IDX_PARENT_ID` (`parent_id`),
	  CONSTRAINT `FK_ORDER_CAPTURE_PAYMENT_PARENT` FOREIGN KEY (`parent_id`) REFERENCES `{$this->getTable('sales/order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
	CREATE TABLE `{$this->getTable('partial_payment_capture_transaction')}` (
	  `transaction_id` int(10) unsigned NOT NULL auto_increment,
	  `parent_id` int(10) unsigned default NULL,
	  `order_id` int(10) unsigned NOT NULL default '0',
	  `payment_id` int(10) unsigned NOT NULL default '0',
	  `txn_id` varchar(100) NOT NULL default '',
	  `parent_txn_id` varchar(100) default NULL,
	  `txn_type` varchar(15) NOT NULL default '',
	  `is_closed` tinyint(1) unsigned NOT NULL default '1',
	  `additional_information` blob,
	  `created_at` datetime default NULL,
	  PRIMARY KEY  (`transaction_id`),
	  UNIQUE KEY `UNQ_ORDER_CAPTURE_PAYMENT_TXN` (`order_id`,`payment_id`,`txn_id`),
	  KEY `IDX_ORDER_ID` (`order_id`),
	  KEY `IDX_PARENT_ID` (`parent_id`),
	  KEY `IDX_PAYMENT_ID` (`payment_id`),
	  CONSTRAINT `FK_SALES_CAPTURE_PAYMENT_TRANSACTION_ORDER` FOREIGN KEY (`order_id`) REFERENCES `{$this->getTable('sales/order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	  CONSTRAINT `FK_SALES_CAPTURE_PAYMENT_TRANSACTION_PARENT` FOREIGN KEY (`parent_id`) REFERENCES `{$this->getTable('partial_payment_capture_transaction')}` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	  CONSTRAINT `FK_SALES_CAPTURE PAYMENT_TRANSACTION_PAYMENT` FOREIGN KEY (`payment_id`) REFERENCES `{$this->getTable('partial_payment_capture')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


/* Adding Different Attributes */

// Adding Attribute Group
$installer->addAttributeGroup('catalog_product', 'Default', 'Prices', 1000);

// Started Creating Partial Payment Attribute
$installer->addAttribute('catalog_product', 'partial_payment', array(
		'group'         	=> 'Prices',
		'label'				=> 'Allow Partial Payment',
		'type'				=> 'int',
		'input'            	=> 'boolean',
		'source'           	=> 'eav/entity_attribute_source_boolean',
		'global'           	=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible'          	=> 1,
		'required'         	=> 1,
		'user_defined'     	=> 1,
		'searchable'       	=> 0,
		'filterable'       	=> 0,
		'comparable'       	=> 0,
		'visible_on_front' 	=> 0,
		'visible_in_advanced_search'	=> 0,
		'unique'            => 0,
		'default'			=> 0
));
$setup->updateAttribute('catalog_product', 'partial_payment', 'is_used_for_promo_rules',1);
$setup->updateAttribute('catalog_product', 'partial_payment', 'is_used_for_price_rules',1);

$installer->endSetup(); 