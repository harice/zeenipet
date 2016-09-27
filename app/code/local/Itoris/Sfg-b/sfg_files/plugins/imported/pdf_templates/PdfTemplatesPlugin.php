<?php 
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_SFG_PLUGIN_PDF_TEMPLATES
 * @copyright  Copyright (c) 2012 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

class PdfTemplatesPlugin {

	private $tableSfgPluginPdfTemplates = 'sfg_plugin_pdf_templates';

	public function __construct() {
		$this->tableSfgPluginPdfTemplates = Mage::getSingleton('core/resource')->getTableName($this->tableSfgPluginPdfTemplates);
	}

	/**
	 * Create table for plugin
	 *
	 * @param Varien_Object $ev
	 */
	public function installPluginAfterPdfTemplates($ev) {
		$db = $this->getDbAdapter();

		$sql = "CREATE TABLE IF NOT EXISTS {$this->tableSfgPluginPdfTemplates} (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`form_id` INT UNSIGNED NOT NULL,
				`template` TEXT NOT NULL DEFAULT '',
				UNIQUE(`form_id`)
		) ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;";
		$db->raw_query($sql);
	}

	/**
	 * @return Varien_Db_Adapter_Pdo_Mysql
	 */
	protected function getDbAdapter() {
		return Mage::getSingleton('core/resource')->getConnection('core_write');
	}
}
?>