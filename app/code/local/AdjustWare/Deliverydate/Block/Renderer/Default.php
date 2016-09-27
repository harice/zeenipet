<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      1.1.7
 * @license:     CGRl40OoIpwl63Yy9HmSwXtQ6ZlFDRlIXEc7HbfxdJ
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Deliverydate_Block_Renderer_Default extends Mage_Core_Block_Template 
implements Varien_Data_Form_Element_Renderer_Interface
{    
    public function render(Varien_Data_Form_Element_Abstract $element){
        return $element->getLabelHtml() . '<br />' . $element->getElementHtml();
    }
}