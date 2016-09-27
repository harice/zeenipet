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

class PdfTemplatesVariablesObserver {

	/**
	 *
	 * @param Varien_Object $ev
	 */
	public function adminLoadVariablesAction(&$ev) {
		require_once dirname(__FILE__) . DS . '..' . DS . 'Model' . DS . 'Variables.php';
		/** @var $controller Itoris_Sfg_Admin_IndexController */
		$controller = $ev->getController();
		$ev->getResponse()->setHeader('Content-type', 'application/json');
		try {
			$variablesModel = new PdfTemplates_Model_Variables();
			$formId = $controller->getRequest()->getParam('form_id');
			$variables = $variablesModel->load($formId);
			$ev->getResponse()->setBody(Zend_Json::encode($variables));
		} catch (Exception $e) {
			$error = array(
				array(
					'label' => $this->getSfgHelper()->__('Cannot load variables'),
					'value' => array(),
				)
			);
			$ev->getResponse()->setBody(Zend_Json::encode($error));
		}
		$ev->setDispatched(true);
	}
}

?>