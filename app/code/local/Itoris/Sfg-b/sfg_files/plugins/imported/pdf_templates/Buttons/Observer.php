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

class PdfTemplatesButtonsObserver {

	/**
	 * @param $ev Varien_Object
	 */
	public function blockShowRecordTopButtonsShow(&$ev) {
		/** @var $buttonsList Mage_Core_Block_Text_List */
		$buttonsList = $ev->getButtonsList();
		/** @var $form SFG_Engine */
		$form = $ev->getForm();
		$formId = $form->form_id;
		$recordId = $ev->getRecordId();
		/** @var $button Mage_Adminhtml_Block_Widget_Button */
		$button = $buttonsList->getLayout()->createBlock('adminhtml/widget_button', 'top.button.export_pdf', array(
				'on_click' 	=> sprintf("setLocation('%s')", Mage::helper('adminhtml')->getUrl('*/admin_index/handler/',array('action' => 'export_pdf','form_id' => $formId, 'record_id' => $recordId))),
				'class'		=> '',
				'label'		=> $this->getSfgHelper()->__('Export to PDF'),
				'type'		=> 'button',
		));

		$buttonsList->insert($button, 'top.button.back', true);
	}

	public function blockDataListTopButtonsShow(&$ev) {
		/** @var $buttonsList Mage_Core_Block_Text_List */
		$buttonsList = $ev->getButtonsList();
		/** @var $button Mage_Adminhtml_Block_Widget_Button */
		$button = $buttonsList->getLayout()->createBlock('adminhtml/widget_button', 'top.button.export_pdf', array(
				'on_click' 	=> "masstask('exportData', true, {export_type:'pdf'});",
				'class'		=> '',
				'label'		=> $this->getSfgHelper()->__('Export to PDF'),
				'type'		=> 'button',
		));
		$buttonsList->insert($button, 'top.button.cancel', true);

		$button = $buttonsList->getLayout()->createBlock('adminhtml/widget_button', 'top.button.export_all_pdf', array(
				'on_click' 	=> "masstask('exportAll', false, {export_type:'pdf'});",
				'class'		=> 'save',
				'label'		=> $this->getSfgHelper()->__('Export All to PDF'),
				'type'		=> 'button',
		));
		$buttonsList->insert($button, 'top.button.export_pdf');
	}

	/**
	 * @return Itoris_Sfg_Helper_Data
	 */
	public function getSfgHelper() {
		return Mage::helper('sfg');
	}
}

?>