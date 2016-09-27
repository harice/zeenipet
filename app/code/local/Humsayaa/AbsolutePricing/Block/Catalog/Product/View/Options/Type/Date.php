<?php
/**
 * Humsayaa AbsolutePricing
 *
 * Product options text type block
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      AbsolutePricing Team <enquiries@absolutepricing.com>
**/

class Humsayaa_AbsolutePricing_Block_Catalog_Product_View_Options_Type_Date extends Mage_Catalog_Block_Product_View_Options_Type_Date
{
    /**
     * JS Calendar html
     *
     * @return string Formatted Html
     */
    public function getCalendarDateHtml()
    {
        $option = $this->getOption();
        $value = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId() . '/date');

        //$require = $this->getOption()->getIsRequire() ? ' required-entry' : '';
        $require = '';

        $yearStart = Mage::getSingleton('catalog/product_option_type_date')->getYearStart();
        $yearEnd = Mage::getSingleton('catalog/product_option_type_date')->getYearEnd();

        /** Changes by Ishtiaq */
        $onchangeReloadPrice = "onchange=\"opConfig.reloadPrice()\"";
        $product_custom_option="product-custom-option";

        if ($option->getPriceType() == 'absolute')	{ $product_custom_option = "product-custom-option-absolute";	$onchangeReloadPrice = ""; }
        /** [end] Changes by Ishtiaq */

        $calendar = $this->getLayout()
            ->createBlock('core/html_date')
            ->setId('options_'.$this->getOption()->getId().'_date')
            ->setName('options['.$this->getOption()->getId().'][date]')
            /** Changes by Ishtiaq */
            ->setClass($product_custom_option . ' datetime-picker input-text' . $require)
            /** [end] Changes by Ishtiaq */
            ->setImage($this->getSkinUrl('images/calendar.gif'))
            ->setFormat(Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT))
            ->setValue($value)
            ->setYearsRange('[' . $yearStart . ', ' . $yearEnd . ']');
        if (!$this->getSkipJsReloadPrice()) {
            $calendar->setExtraParams("$onchangeReloadPrice");
        }

        return $calendar->getHtml();
    }

    /**
     * HTML select element
     *
     * @param string $name Id/name of html select element
     * @return Mage_Core_Block_Html_Select
     */
    protected function _getHtmlSelect($name, $value = null)
    {
        $option = $this->getOption();

        /** Changes by Ishtiaq */
        $onchangeReloadPrice = "onchange=\"opConfig.reloadPrice()\"";
        $product_custom_option="product-custom-option";

        if ($option->getPriceType() == 'absolute')	{ $product_custom_option = "product-custom-option-absolute";	$onchangeReloadPrice = ""; }
        /** [end] Changes by Ishtiaq */

        // $require = $this->getOption()->getIsRequire() ? ' required-entry' : '';
        $require = '';
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setId('options_' . $this->getOption()->getId() . '_' . $name)
            /** Changes by Ishtiaq */
            ->setClass($product_custom_option . ' datetime-picker' . $require)
            /** [end] Changes by Ishtiaq */
            ->setExtraParams()
            ->setName('options[' . $option->getId() . '][' . $name . ']');

        $extraParams = 'style="width:auto"';
        if (!$this->getSkipJsReloadPrice()) {
            $extraParams .= " $onchangeReloadPrice";
        }
        $select->setExtraParams($extraParams);

        if (is_null($value)) {
            $value = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId() . '/' . $name);
        }
        if (!is_null($value)) {
            $select->setValue($value);
        }

        return $select;
    }

}
