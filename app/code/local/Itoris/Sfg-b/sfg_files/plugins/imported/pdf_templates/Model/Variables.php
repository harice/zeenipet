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

require_once Mage::getBaseDir('app') . DS . 'code' . DS . 'local' . DS . 'Itoris' . DS . 'Sfg' . DS . 'sfg_files' . DS . 'engine' . DS . 'engine.php';

class PdfTemplates_Model_Variables extends Varien_Object {

	public function load($formId) {
		$active_elements = array('input', 'select', 'textarea', 'button');
		$form = new SFG_Engine();
		$form->form_id = (int)$formId;
		$form->loadForm();
		$form->parseXML();
		$elements = $form->formXMLStruct['elements'];
		foreach ($elements as $key => $value) {
			if (!in_array($value["tag"], $active_elements)) {
				unset($elements[$key]);
			}
		}
		return $this->prepareVariables($elements);
	}

	private function prepareVariables($elements) {
		$variables = array();
		$elementNames = array();
		$variables[0] = array(
			'label' => $this->getSfgHelper()->__('Form variables'),
			'value' => array(),
		);
		foreach ($elements as $key => $value) {
		   foreach ($value['attributes'] as $row) {
			   if ($row['name']=='name'){
				   $elementName = $row['value'];
				   break;
			   }
		   }
		   if (!in_array($elementName, $elementNames) && !empty($elementName)) {
			   $elementNames[] = $elementName;
			   $label = ($value['sfgalias'] != "") ? $value['sfgalias'] : $elementName;
				$variables[0]['value'][] = array(
					'label' => $label,
					'value' => '{' . $elementName . '}',
				);
		   }
		}

		/*$variables[0]['value'][] = array(
			'label' => $this->getSfgHelper()->__('Signature'),
			'value' => '{signature}',
		);*/

		return $variables;
	}

	/**
	 * @return Itoris_Sfg_Helper_Data
	 */
	public function getSfgHelper() {
		return Mage::helper('sfg');
	}

}

?>