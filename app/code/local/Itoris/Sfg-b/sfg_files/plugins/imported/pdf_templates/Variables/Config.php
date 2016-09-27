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

class PdfTemplates_Variables_Config extends Mage_Core_Model_Variable_Config {

	private $formId = 0;

	public function __construct($formId) {
		$this->formId = $formId;
	}
	/**
 	* Return url of action to get variables
 	*
 	* @return string
 	*/
	public function getVariablesWysiwygActionUrl() {
    	return Mage::helper('adminhtml')->getUrl('itoris_sfg/admin_index/handler', array('action' => 'load_variables', 'form_id' => $this->formId));
	}

}
?>