<?php
/**
 * Humsayaa AbsolutePricing
 *
 * customers defined options
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      AbsolutePricing Team <enquiries@absolutepricing.com>
**/

class Humsayaa_AbsolutePricing_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Option extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option
{
    public function getPriceValue($value, $type)
    {
        if ($type == 'fixed' || $type == 'percent' || $type == 'absolute') {
            return number_format($value, 2, null, '');
        }
    }
}
