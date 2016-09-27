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

require_once dirname(__FILE__) . DS . 'Container.php';
require_once dirname(__FILE__) . DS . '..' . DS . 'Model' . DS . 'Template.php';

class PdfTemplatesEditObserver {

	/**
	 *
	 * @param Varien_Object $ev
	 */
	public function adminPdfTemplateEditAction(&$ev) {
		/** @var $controller Itoris_Sfg_Admin_IndexController */
		$controller = $ev->getController();
		$controller->loadLayout();
		if (version_compare(Mage::getVersion(), '1.4.0.0', '>=')) {
			/** @var $headBlock Mage_Adminhtml_Block_Page_Head */
			$headBlock = $controller->getLayout()->getBlock('head');
			$headBlock->addJs('mage/adminhtml/wysiwyg/tiny_mce/setup.js');
			$headBlock->addJs('mage/adminhtml/variables.js');
			$headBlock->addItem('skin_js', 'itoris_sfg/jsf/plugins/pdf_templates/variables.js');
			$headBlock->addItem('js_css', 'prototype/windows/themes/magento.css');
			$headBlock->addItem('js_css', 'prototype/windows/themes/default.css');
			$headBlock->setCanLoadTinyMce(true);
		}
		try {
			if (version_compare(Mage::getVersion(), '1.4.0.0', '>=')) {
				/** @var $form PdfTemplates_Edit_Container */
				$form = $controller->getLayout()->createBlock('PdfTemplates_Edit_Container');
				$controller->getLayout()->getBlock('content')->append($form);
			} else {
				$form = new PdfTemplates_Edit_Container();
				$form->setLayout($controller->getLayout());
				$controller->getLayout()->setBlock('pdf_form_container', $form);
				$controller->getLayout()->getBlock('content')->append($form, 'pdf_form_container');
			}
		} catch (Exception $e) {
			$ev->getSession()->addError($this->getSfgHelper()->__('Cannot load data'));
		}
		$controller->renderLayout();
		$ev->setDispatched(true);
	}

	public function adminPdfTemplateSaveAction(&$ev) {
		/** @var $controller Itoris_Sfg_Admin_IndexController */
		$controller = $ev->getController();
		$formId = (int)$ev->getRequest()->getParam('form_id');
		if ($formId) {
			$pdfTemplate = $ev->getRequest()->getParam('pdf');
			$template = '';
			if (is_array($pdfTemplate)) {
				$template = serialize($pdfTemplate);
			}
			if (!empty($template)) {
				try {
					$dbModel = new PdfTemplate_Model_Template();
					$dbModel->load($formId);
					$dbModel->setTemplate($template);
					$dbModel->save();
					$ev->getSession()->addSuccess($this->getSfgHelper()->__('Template configuration has been saved'));
				} catch (Exception $e) {
					$ev->getSession()->addError($this->getSfgHelper()->__('Cannot save template configuration'));
				}
			}
		}
		$this->adminPdfTemplateEditAction($ev);
	}

	/**
	 *
	 * @param Varien_Object $ev
	 */
	public function formListFormActions(&$ev) {
		$actions = $ev->getActions();
		if (!is_array($actions)) {
			$actions = array();
		}
		$url = Mage::helper('adminhtml')->getUrl('itoris_sfg/admin_index/handler', array('action' => 'pdf_template_edit', 'form_id' => $ev->getForm()->getId()));
		$actions[] = '<a href="javascript: void(0);" onclick="setLocation(\''. $url .'\')">'.$this->getSfgHelper()->__('PDF Template').'</a>';
		$ev->setActions($actions);
	}

	/**
	 * @return Itoris_Sfg_Helper_Data
	 */
	public function getSfgHelper() {
		return Mage::helper('sfg');
	}
}
?>