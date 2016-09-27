<?php
/**
 * Humsayaa AbsolutePricing
 *
 * Price types mode source
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      AbsolutePricing Team <enquiries@absolutepricing.com>
**/

class Humsayaa_AbsolutePricing_Model_Adminhtml_System_Config_Source_Product_Options_Price extends Mage_Adminhtml_Model_System_Config_Source_Product_Options_Price
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'fixed', 	 'label' => Mage::helper('adminhtml')->__('Fixed')),
            array('value' => 'percent',  'label' => Mage::helper('adminhtml')->__('Percent')),
            array('value' => 'absolute', 'label' => Mage::helper('adminhtml')->__('Absolute'))
        );
    }
}
