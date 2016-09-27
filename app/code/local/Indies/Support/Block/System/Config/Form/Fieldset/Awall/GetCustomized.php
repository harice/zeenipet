<?php
/**
 * @category   Indies Services
 * @package    Indies_Support
 * @version    1.0.0
 * @copyright  Copyright (c) 2012-2013 Indies Services (http://www.indieswebs.com)
 */

class Indies_Support_Block_System_Config_Form_Fieldset_Awall_GetCustomized extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		$html .='<div><p>Looking for some customization in any of our extension? Then <a target="_blank" href="http://www.indieswebs.com/contact-indies-webs.html?utm_source=supportab&utm_content=customized">Contact us</a> to get the extension customized specially for you as you wanted.</p></div>';
		$html .= $this->_getFooterHtml($element);
		return $html;
	}
}