<?php
$installer = $this;
$installer->startSetup();
$installer->run("

ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD `active_from` timestamp NULL DEFAULT NULL COMMENT 'Active From Time' AFTER `store_id`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD `active_to` timestamp NULL DEFAULT NULL COMMENT 'Active From Time' AFTER `active_from`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD `cat_id` varchar(255) NOT NULL COMMENT 'Slider Category' AFTER `sort_order`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD  `call_to_action_type` varchar(100) NOT NULL DEFAULT 'showlinkwithborowsebutton' COMMENT 'Call2Action Type' AFTER `cat_id`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD  `call_to_action` BINARY(1) NOT NULL DEFAULT '1' COMMENT 'Call2Action Button' AFTER `call_to_action_type`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD  `call_action_text` varchar(255) COMMENT 'Call2Action Button Color' AFTER `call_to_action`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD  `call_action_textcolor` varchar(255) COMMENT 'Call2Action Button Color' AFTER `call_action_text`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD  `call_action_text_shadow` varchar(255) COMMENT 'Call2Action Button Color' AFTER `call_action_textcolor`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD  `call_action_background_from` varchar(255) COMMENT 'Button Background Gradient Color From' AFTER `call_action_text_shadow`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD  `call_action_background_to` varchar(255) COMMENT 'Button Background Gradient Color To' AFTER `call_action_background_from`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD  `call_action_background_border` varchar(255) COMMENT 'Call2Action Button Background Color' AFTER `call_action_background_to`;

ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD  `name_color` varchar(255) COMMENT 'Name Color' AFTER `name`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} ADD  `content_color` varchar(255) COMMENT 'Content Color' AFTER `content`;
ALTER TABLE {$this->getTable('cybernetikz_cnslider/slider')} MODIFY `store_id` varchar(255) NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS {$this->getTable('cybernetikz_cnslider/cat')} (
`cat_id` int(11) unsigned NOT NULL AUTO_INCREMENT ,
`title` varchar(255) NOT NULL DEFAULT '',
`font_size` varchar(50) NOT NULL DEFAULT '30px',
`sort_order` tinyint(6) NOT NULL,
`settings` varchar(100) NOT NULL DEFAULT '',
`show_title` binary(1) NOT NULL DEFAULT '0',
`show_content` binary(1) NOT NULL DEFAULT '0',
`show_link` binary(1) NOT NULL DEFAULT '0',
`width` varchar(100) NULL,
`height` varchar(100) NULL,
`effect` varchar(100) NULL,
`delay` varchar(100) NULL,
`length` varchar(100) NOT NULL,
`playpause` binary(1) NOT NULL DEFAULT '0',
`pagination` binary(1) NOT NULL DEFAULT '0',
`nextprev` binary(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`cat_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

");
$installer->endSetup();