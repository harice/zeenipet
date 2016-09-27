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

class PdfTemplatesEmailObserver {

	/**
	 * @param $ev Varien_Object
	 *  $ev->getForm()
	 *  $ev->getTemplateId()
	 *  $ev->getUserEmail()
	 *  $ev->getFiles()
	 */
	public function doAfterSubmitActionUserEmailSendBefore(&$ev) {
		/** @var $form SFG_Engine */
		$form = $ev->getForm();
		if (isset($_SESSION['sfg'][$form->form_id]['attach_user_email']) && $_SESSION['sfg'][$form->form_id]['attach_user_email']) {
			$files = $ev->getFiles();
			$fileNames = $_SESSION['sfg'][$form->form_id]['files']['pdf_document'];
			$fileNames['new_name'] =  $form->db_id . '-' . $fileNames['name'];
			if (!file_exists(SFG_ENGINE . 'files' . DS . $fileNames['new_name'])) {
				$fileNames['new_name'] = substr($fileNames['tmp_name'], strrpos($fileNames['tmp_name'], DS));
			}
			unset($fileNames['tmp_name']);
			$files['pdf_document'] = $fileNames;
			$ev->setFiles($files);
		}
	}

	/**
	 * @param $ev Varien_Object
	 *  $ev->getForm()
	 *  $ev->getTemplateId()
	 *  $ev->getAdminEmail()
	 */
	public function doAfterSubmitActionAdminEmailSendBefore(&$ev) {
		/** @var $form SFG_Engine */
		$form = $ev->getForm();
		if (isset($_SESSION['sfg'][$form->form_id]['attach_admin_email']) && $_SESSION['sfg'][$form->form_id]['attach_admin_email']) {
			$fileNames = $_SESSION['sfg'][$form->form_id]['files']['pdf_document'];
			$fileNames['new_name'] =  $form->db_id . '-' . $fileNames['name'];
			if (!file_exists(SFG_ENGINE . 'files' . DS . $fileNames['new_name'])) {
				$fileNames['new_name'] = substr($fileNames['tmp_name'], strrpos($fileNames['tmp_name'], DS));
			}
			unset($fileNames['tmp_name']);
			$form->files['pdf_document'] = $fileNames;
		} else {
			if (isset($form->files['pdf_document'])) {
				unset($form->files['pdf_document']);
			}
		}
	}
}

?>