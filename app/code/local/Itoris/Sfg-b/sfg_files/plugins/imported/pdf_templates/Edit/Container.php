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

require_once dirname(__FILE__) . DS . 'Form.php';

class PdfTemplates_Edit_Container extends Mage_Adminhtml_Block_Widget_Form_Container{
	public function __construct() {
		parent::__construct();
		$this->_mode = false;
		$this->_headerText = $this->getSfgHelper()->__('PDF Template Configuration');
		$this->_updateButton('save', 'label', $this->getSfgHelper()->__('Save Template'));
	}

	protected function _prepareLayout() {
		if (version_compare(Mage::getVersion(), '1.4.0.0', '>=')) {
			$this->setChild('form', $this->getLayout()->createBlock('PdfTemplates_Edit_Form'));
		} else {
			$form = new PdfTemplates_Edit_Form();
			$this->getLayout()->setBlock('form', $form);
			$this->setChild('form', $form);
		}
		return parent::_prepareLayout();
	}

	public function getBackUrl() {
		return $this->getUrl('*/admin_form/index');
	}

	public function getNameInLayout() {
		if (version_compare(Mage::getVersion(), '1.4.0.0', '>=')) {
			return parent::getNameInLayout();
		} else {
			return 'pdf_form_container';
		}
	}

	/**
	 * @return Itoris_Sfg_Helper_Data
	 */
	public function getSfgHelper() {
		return Mage::helper('sfg');
	}

}
?>