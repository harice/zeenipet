<?php
/**
 * @category   Indies Services
 * @package    Indies_Support
 * @version    1.0.0
 * @copyright  Copyright (c) 2012-2013 Indies Services (http://www.indieswebs.com)
 */

class Indies_Support_Block_System_Config_Form_Fieldset_Awall_ShareYourFeedback extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		$html .='<div><p>If you want this extensions to get even better, <strong>we need your testimonials and feedback!</strong> Email your Feedback to <a target="_blank" href="mailto:service@indieswebs.com">services@indieswebs.com</a> or use the <a target="_blank" href="http://www.indieswebs.com/contact-indies-webs.html?utm_source=supportab&utm_content=feedback">Contact Us</a>. Your ideas, suggestions and enthusiasm will help us serve you more!</p></div>';
		$html .= $this->_getFooterHtml($element);
		return $html;
	}

}