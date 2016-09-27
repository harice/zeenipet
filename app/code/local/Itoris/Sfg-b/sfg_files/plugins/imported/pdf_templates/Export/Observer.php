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

class PdfTemplatesExportObserver {

	/** @var PdfTemplates_Helper_Records */
	private $recordsHelper = null;

	/**
	 * @param $ev Varien_Object
	 * @return null
	 */
	public function adminExportPdfAction(&$ev) {
		/** @var $controller Itoris_Sfg_Admin_IndexController */
		$controller = $ev->getController();
		$formId = (int)$ev->getRequest()->getParam('form_id');
		$recordId = (int)$ev->getRequest()->getParam('record_id');
		if ($formId && $recordId) {
			$form = new SFG_Engine();
			$form->form_id = $formId;
			$form->loadForm();
			$form->parseXML();

			$dbTableName = isset($form->formXMLStruct['database']['name']) ? $form->formXMLStruct['database']['name'] : '';
			if ($dbTableName=='') {
				$ev->getSession()->addError($this->getSfgHelper()->__('The form does not have associated BD Table'));
				return null;
			}

			require_once dirname(__FILE__) . DS . '..' . DS . 'Model' . DS . 'Records.php';

			$recordsModel = new PdfTemplate_Model_Records($dbTableName, $recordId);
			$row = $recordsModel->load();
			if (!is_array($row)) {
				$ev->getSession()->addError($this->getSfgHelper()->__('Record does not exist'));
				return null;
			}

			require_once dirname(__FILE__) . DS . '..' . DS . 'Helper' . DS . 'Pdf.php';
			require_once dirname(__FILE__) . DS . '..' . DS . 'Helper' . DS . 'Records.php';

			$pdfHelper = new PdfTemplates_Helper_Pdf();
			$pdfFileContent = $pdfHelper->prepareRecordPdf($row, $form->formXMLStruct['form_name'], $this->getRecordsHelper()->getSfgAliases($form->formXMLStruct['elements'], $form->formXMLStruct['database']['mapping']));
			$this->downloadPdf($pdfFileContent);
		}
		$ev->setDispatched(true);
	}

	/**
	 * @param $ev Varien_Object
	 * 	$ev->getRows()
	 *  $ev->getForm()
	 *  $ev->getTask()
	 *  $ev->getExported()
	 */
	public function adminDataListControllerExport(&$ev) {
		if (Mage::app()->getRequest()->getParam('export_type') == 'pdf') {

			require_once dirname(__FILE__) . DS . '..' . DS . 'Helper' . DS . 'Pdf.php';
			require_once dirname(__FILE__) . DS . '..' . DS . 'Helper' . DS . 'Records.php';

			$formName = $ev->getForm()->formXMLStruct['form_name'];
			$rows = $ev->getRows();
			$columns = explode('|', Mage::app()->getRequest()->getParam('columns'));
			$filters = Mage::app()->getRequest()->getParam('filter');
			$pdfHelper = new PdfTemplates_Helper_Pdf();
			$records = array();
			if (!$columns[0]) {
				$records = $rows;
			} else {
				if ($filters) {
					$rows = $this->getRecordsHelper()->filterRows($rows, $filters);
				}
				for ($i = 0; $i < count($rows); $i++) {
					$record = array();
					foreach ($rows[$i] as $key => $value) {
						if (in_array($key, $columns)) {
							$record[$key] = $value;
						}
					}
					$records[] = $record;
				}
				$sort = Mage::app()->getRequest()->getParam('srt');
				$sortDirection = (int)Mage::app()->getRequest()->getParam('ord');
				$records = $this->getRecordsHelper()->sortRecords($records, $sort, $sortDirection);
			}
			$pdfFileContent = $pdfHelper->prepareAllRecordsPdf($records, $formName, $this->getRecordsHelper()->getSfgAliases($ev->getForm()->formXMLStruct['elements'], $ev->getForm()->formXMLStruct['database']['mapping']));
			$this->downloadPdf($pdfFileContent);
			$ev->setExported(true);
		}
	}

	public function frontendDownloadPdfAction(&$ev) {
		$formId = $ev->getRequest()->getParam('form_id');
		if (isset($_SESSION['sfg'][$formId]['files']['pdf_document'])) {
			$path = SFG_ENGINE . DS . 'files' . DS . $ev->getRequest()->getParam('db_id') . '-' . $_SESSION['sfg'][$formId]['files']['pdf_document']['name'];
			if (!file_exists($path)) {
				$path = $_SESSION['sfg'][$formId]['files']['pdf_document']['tmp_name'];
			}
			$this->readPdfFile($path, $_SESSION['sfg'][$formId]['files']['pdf_document']['name']);
		}
		$ev->setDispatched(true);
	}

	public function readPdfFile($file, $name = 'document.pdf') {
		@ob_end_clean();
		$name = str_replace(' ', '_', $name);
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // some day in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-type: application/pdf");
		header("Content-Length: " . filesize($file));
		header("Content-Disposition: attachment; filename={$name}");
		readfile($file);
		exit;
	}


	public function downloadPdf($fileContent){
		@ob_end_clean();
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // some day in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Content-type: application/pdf");
		header("Content-Length: " . strlen($fileContent));
		header("Content-Disposition: attachment; filename=document.pdf");
		echo $fileContent;
		exit;
	}

	/**
	 * @return Itoris_Sfg_Helper_Data
	 */
	public function getSfgHelper() {
		return Mage::helper('sfg');
	}

	public function getRecordsHelper() {
		if (!$this->recordsHelper) {
			$this->recordsHelper = new PdfTemplates_Helper_Records();
		}
		return $this->recordsHelper;
	}
}

?>