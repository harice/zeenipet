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

class PdfTemplatesFormObserver {

	/**
	 * @param $ev Varien_Object
	 */
	public function doAfterSubmitActionSaveDateAfter(&$ev) {
		require_once dirname(__FILE__) . DS . '..' . DS . 'Helper' . DS . 'Pdf.php';

		/** @var $form SFG_Engine */
		$form = $ev->getForm();
		$pdfHelper = new PdfTemplates_Helper_Pdf();
		$pdfHelper->prepareFormPDF($form);
	}

	/**
	 * @param $ev Varien_Object
	 *  $ev->getForm()
	 *  $ev->getElement()
	 *  $ev->getName()
	 *  $ev->getType()
	 */
	public function renderFormRenderElementBefore(&$ev) {
		$type = $ev->getType();
		if ($type == 'input' || $type == 'button') {
			$element = $ev->getElement();
			$events = isset($element['events']) ? $element['events'] : array();
			if (!empty($events)) {
				foreach ($events as $key => $event) {
					if ($event['value'] == '{download_pdf_document}') {
						$events[$key]['value'] = "setLocation('" . Mage::getUrl('sfg/index/handler', array('action' => 'download_pdf', 'form_id' => $ev->getForm()->form_id, 'db_id' => $ev->getForm()->db_id)) ."')";
					}
				}
			}
			$element['events'] = $events;
			$ev->setElement($element);
		}
	}
}

?>