<?php
/**
 * @category   Indies Services
 * @package    Indies_Support
 * @version    1.0.0
 * @copyright  Copyright (c) 2012-2013 Indies Services (http://www.indieswebs.com)
 */

class AW_All_Block_System_Config_Form_Fieldset_Awall_Store extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<div id="' . $element->getId() . '"></div>';
        return $html;		
    }
}
?>
