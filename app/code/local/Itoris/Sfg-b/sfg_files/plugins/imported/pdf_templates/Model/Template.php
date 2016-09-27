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

class PdfTemplate_Model_Template extends Varien_Object {

	/** @var Mage_Core_Model_Resource */
	private $resource = null;
	/** @var Varien_Db_Adapter_Interface */
	private $dbAdapter = null;

	private $tableSfgPluginPdfTemplates = 'sfg_plugin_pdf_templates';

	public function __construct() {
		$this->resource = Mage::getSingleton('core/resource');
		$this->dbAdapter = $this->resource->getConnection('core_write');
		$this->tableSfgPluginPdfTemplates = $this->resource->getTableName($this->tableSfgPluginPdfTemplates);
	}

	public function load($formId) {
		$this->setFormId($formId);
		/** @var $sql Varien_Db_Select */
		$sql = $this->dbAdapter->select()
				->from($this->tableSfgPluginPdfTemplates, 'template')
				->where('form_id = ?', $formId);
		$template = $this->dbAdapter->fetchRow($sql);
		if (!empty($template)) {
			$this->setTemplate($template['template']);
			$this->setEntryExists(true);
		}
		return $template;
	}

	public function save() {
		$values = array(
			'form_id'  => $this->getFormId(),
			'template' => $this->getTemplate(),
		);
		if ($this->getEntryExists()) {
			$this->dbAdapter->query('update '.$this->tableSfgPluginPdfTemplates.' set `template`='.$this->dbAdapter->quote($this->getTemplate()).' where `form_id`='.intval($this->getFormId()));
			//$this->dbAdapter->update($this->tableSfgPluginPdfTemplates, $values, array('form_id' => $this->getFormId()));
		} else {
			$this->dbAdapter->insert($this->tableSfgPluginPdfTemplates, $values);
		}
	}

}

?>