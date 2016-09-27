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

class PdfTemplates_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

	private $wysiwygConfig = null;

	protected function _prepareForm() {
		$formId = $this->getRequest()->getParam('form_id');
		$variables = '';

		$greaterThan13version = version_compare(Mage::getVersion(), '1.4.0.0', '>=');
		$platformInfo = array();
		Mage::helper('itoris_installer')->addPlatformInfo($platformInfo);
		$beforeEE1700 = false;
		if ($platformInfo['magento_edition'] == Itoris_Installer_Helper_Data::$MAGENTO_EDITION_EE) {
			$beforeEE1700 = version_compare(Mage::getVersion(), '1.7.0.0', '<');
		}

		if ($greaterThan13version && !$beforeEE1700) {
			require_once dirname(__FILE__) . DS . '..' . DS . 'Variables' . DS . 'Config.php';
		} else {
			require_once dirname(__FILE__) . DS . '..' . DS . 'Model' . DS . 'Variables.php';
			$variablesModel = new PdfTemplates_Model_Variables();
			$variables = $variablesModel->load($formId);
			$variables = $variables[0]['value'];
			$variableStr = '<div';
			if (!$beforeEE1700) {
				$variableStr .= ' style="margin-left: 150px;"';
			}
			$variableStr .= '>';
			foreach ($variables as $variable) {
				$variableStr .= $variable['value'] . '<br/>';
			}
			$variables = $variableStr . '</div>';
		}

		$form = new Varien_Data_Form();

		$fieldSet = $form->addFieldset('template_configuration', array('legend'=> $this->getSfgHelper()->__('Template Configuration')));

		$selectedValues = array(
			'1' => $this->getSfgHelper()->__('Yes'),
			'0' => $this->getSfgHelper()->__('No'),
		);

		$fieldSet->addField('attach_file_name', 'text', array(
			'name'    => 'pdf[attach_file_name]',
			'label'   => $this->getSfgHelper()->__("PDF Filename"),
			'note'    => $this->getSfgHelper()->__("if empty, filename will be 'pdf_template'"),
		));

		$fieldSet->addField('attach_admin_email', 'select', array(
			'name'    => 'pdf[attach_admin_email]',
			'values'   => $selectedValues,
			'label'   => $this->getSfgHelper()->__("Attach PDF to Admin's email"),
		));

		$fieldSet->addField('attach_user_email', 'select', array(
			'name'    => 'pdf[attach_user_email]',
			'values'   => $selectedValues,
			'label'   => $this->getSfgHelper()->__("Attach PDF to User's email"),
		));

		$fieldSet->addField('page_size', 'select', array(
			'name'   => 'pdf[page_size]',
			'values' => array('A4' => 'A4 (210x297 mm.)', 'LETTER' => 'Letter (8.5x11 in.)', 'LEGAL' => 'Legal (8.4x14 in.)'),
			'label'  => $this->getSfgHelper()->__('Page Size'),
		));

		$fieldSet->addField('header_content', 'editor', array(
			'name'    => 'pdf[header_content]',
			'label'   => $this->getSfgHelper()->__('Header Content'),
			'wysiwyg' => true,
			'config'    => ($greaterThan13version && !$beforeEE1700) ? $this->getWysiwygConfig($formId) : '',
			'style'   => 'width: 400px; height: 200px;',
		));

		if (!$greaterThan13version || $beforeEE1700) {
			$fieldSet->addField('header_content_note', 'note', array(
				'label'     => $this->getSfgHelper()->__('Variables'),
				'text'      => $variables,
			));
		}

		$fieldSet->addField('header_height', 'text', array(
			'name'   => 'pdf[header_height]',
			'label'  => $this->getSfgHelper()->__('Top Margin'),
			'note'   => $this->getSfgHelper()->__('apply if the header content is not empty'),
			'class'  => 'validate-digits',
		));

		$fieldSet->addField('show_line_separator_header_body', 'select', array(
			'name'    => 'pdf[show_line_separator_header_body]',
			'label'   => $this->getSfgHelper()->__('Line Separator<br/>Between header and body'),
			'values'   => $selectedValues,
		));

		$fieldSet->addField('body_content', 'editor', array(
			'name'  => 'pdf[body_content]',
			'label' => $this->getSfgHelper()->__('Body Content'),
			'wysiwyg' => true,
			'config'    => ($greaterThan13version && !$beforeEE1700) ? $this->getWysiwygConfig($formId) : '',
			'style'   => 'width: 400px; height: 200px;',
		));

		if (!$greaterThan13version || $beforeEE1700) {
			$fieldSet->addField('body_content_note', 'note', array(
				'label'     => $this->getSfgHelper()->__('Variables'),
				'text'      => $variables,
			));
		}

		$fieldSet->addField('body_styles', 'textarea', array(
			'name'  => 'pdf[body_styles]',
			'label' => $this->getSfgHelper()->__('Styles'),
			'style'   => 'width: 400px; height: 200px;',
		));

		$fieldSet->addField('show_line_separator_body_footer', 'select', array(
			'name'    => 'pdf[show_line_separator_body_footer]',
			'label'   => $this->getSfgHelper()->__('Line Separator<br/>Between footer and body'),
			'values'   => $selectedValues,
		));

		$fieldSet->addField('show_page_numbers', 'select', array(
			'name'    => 'pdf[show_page_numbers]',
			'label'   => $this->getSfgHelper()->__('Show Page Numbers<br/>in format (page x of y)'),
			'values'   => $selectedValues,
		));

		$fieldSet->addField('page_numbers_align', 'select', array(
			'name'     => 'pdf[page_numbers_align]',
			'label'    => $this->getSfgHelper()->__('Page Numbers Align'),
			'values'   => array(
								'left'   => $this->getSfgHelper()->__('left'),
							    'center' => $this->getSfgHelper()->__('center'),
							    'right'  => $this->getSfgHelper()->__('right'),
						  ),
		));

		$fieldSet->addField('footer_content', 'editor', array(
			'name'    => 'pdf[footer_content]',
			'label'   => $this->getSfgHelper()->__('Footer Content'),
			'wysiwyg' => true,
			'config'    => ($greaterThan13version && !$beforeEE1700) ? $this->getWysiwygConfig($formId) : '',
			'style'   => 'width: 400px; height: 200px;',
		));

		if (!$greaterThan13version || $beforeEE1700) {
			$fieldSet->addField('footer_content_note', 'note', array(
				'label'     => $this->getSfgHelper()->__('Variables'),
				'text'      => $variables,
			));
		}

		$fieldSet->addField('footer_height', 'text', array(
			'name'  => 'pdf[footer_height]',
			'label' => $this->getSfgHelper()->__('Bottom Margin'),
			'note'  => $this->getSfgHelper()->__('apply if the footer content is not empty'),
			'class' => 'validate-digits',
		));

	    $form->setValues($this->getFormValues($formId));
		$form->setAction(Mage::helper('adminhtml')->getUrl('itoris_sfg/admin_index/handler', array('action' => 'pdf_template_save', 'form_id' => $formId)));
		$form->setMethod('post');
		$form->setUseContainer(true);
		$form->setId('edit_form');

		$this->setForm($form);

		return parent::_prepareForm();
	}

	private function getFormValues($formId) {
		$values = $this->getDefaultValues();
		$formValues = $this->loadFormValues($formId);
		if (!empty($formValues)) {
			foreach ($formValues as $key => $value) {
				$values[$key] = $value;
			}
		}
		return $values;
	}

	protected function getDefaultValues() {
		$values = array(
				'header_content' => '',
				'header_height' => '15',
				'footer_height' => '15',
				'footer_content' => '',
				'body_content' => '',
				'page_size' => 'A4',
				'show_page_numbers' => false,
				'page_numbers_align' => 'left',
				'show_line_separator_body_footer' => false,
				'show_line_separator_header_body' => false,
				'body_styles' =>'
h1,h2,h3,
h4,h5,h6      {  color:#CD150D; }
h1            { font-size:20px; font-weight:normal; }
h2            { font-size:18px; font-weight:normal; }
h3            { font-size:16px; font-weight:bold; }
h4            { font-size:14px; font-weight:bold; }
h5            { font-size:12px; font-weight:bold; }
h6            { font-size:11px; font-weight:bold; }

p {
   text-indent:25px;
}

a {color:green; text-decoration:underline;}

table {
  	background-color:#AAA;
	font-size:8pt;
	border-top:1px solid black;
	border-left:1px solid black;
}

table td {
	border-right:1px solid black;
	border-bottom:1px solid black;
}
				',
				'attach_admin_email' => 1,
				'attach_user_email' => 1,
				'attach_file_name'  => 'pdf_template',
		);

		return $values;
	}

	public function loadFormValues($formId) {
		$dbModel = new PdfTemplate_Model_Template();
		$dbModel->load($formId);
		$template = $dbModel->getTemplate();

		$result = array();

		$values = @unserialize($template);


		if (is_array($values)) {
			$result = $values;
		} elseif (is_string($values)) {
			$result['body_content'] = $values;
		}

		return $result;
	}

	private function getWysiwygConfig($formId) {
		if (!$this->wysiwygConfig) {
			$config = new Varien_Object();
			$config->setData(array(
				  'add_variables'                 => true,
				  'translator'                    => Mage::helper('cms'),
				  'encode_directives'             => true,
				  'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg/directive'),
				  'popup_css'                     =>
					  Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/dialog.css',
				  'content_css'                   =>
					  Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/content.css',
				  'width'                         => '100%',
				  'plugins'                       => array(),
				  'add_images'               => false,
			));

			$variablesPlugin = new PdfTemplates_Variables_Config($formId);
			$config->addData($variablesPlugin->getWysiwygPluginSettings($config));

			$this->wysiwygConfig = $config;
		}

		return $this->wysiwygConfig;
	}

	public function getNameInLayout() {
		if (version_compare(Mage::getVersion(), '1.4.0.0', '>=')) {
			return parent::getNameInLayout();
		} else {
			return 'pdf_form';
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