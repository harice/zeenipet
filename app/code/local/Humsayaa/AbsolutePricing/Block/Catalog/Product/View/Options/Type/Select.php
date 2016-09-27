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

class Humsayaa_AbsolutePricing_Block_Catalog_Product_View_Options_Type_Select extends Mage_Catalog_Block_Product_View_Options_Type_Select
{
    /**
     * Return html for control element
     *
     * @return string
     */
    public function getValuesHtml() // mage 1700, 1701, 1702
    {
        $_option = $this->getOption();
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());
        $store = $this->getProduct()->getStore();

        /** Changes by Ishtiaq */
        $onclickReloadPrice = "onclick=\"opConfig.reloadPrice()\"";
        $onchangeReloadPrice = "onchange=\"opConfig.reloadPrice()\"";
        $product_custom_option="product-custom-option";
        $getChargetxt = "";
        /** [end] Changes by Ishtiaq */

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN 
		    || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
            $require = ($_option->getIsRequire()) ? ' required-entry' : '';
            $extraParams = '';

            /** Changes by Ishtiaq */
            foreach ($_option->getValues() as $_value) {
                $priceStr = $this->_formatPrice(array(
                    'is_percent'    => ($_value->getPriceType() == 'percent'),
                    'pricing_value' => $_value->getPrice(($_value->getPriceType() == 'percent'))
                ), false);
				
				if ($_value->getPriceType() == 'absolute') 	{ $product_custom_option = "product-custom-option-absolute"; $onchangeReloadPrice = ""; $onclickReloadPrice = ""; }
            }
            /** [end] Changes by Ishtiaq */

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setData(array(
                    'id' => 'select_'.$_option->getId(),
                    /** Changes by Ishtiaq */
                    'class' => $require.' '.$product_custom_option.''
                    /** [end] Changes by Ishtiaq */
                ));

            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN) {
                $select->setName('options['.$_option->getid().']')
                    ->addOption('', $this->__('-- Please Select --'));
            } else {
                $select->setName('options['.$_option->getid().'][]');
                /** Changes by Ishtiaq */
                $select->setClass('multiselect'.$require.' '.$product_custom_option.'');
                /** [end] Changes by Ishtiaq */
            }
            foreach ($_option->getValues() as $_value) {
                $priceStr = $this->_formatPrice(array(
                    'is_percent'    => ($_value->getPriceType() == 'percent'),
                    'pricing_value' => $_value->getPrice(($_value->getPriceType() == 'percent'))
                ), false);
				
				/** Changes by Ishtiaq */
				if ($_value->getPriceType() == 'absolute') 	{ $product_custom_option = "product-custom-option-absolute"; $onchangeReloadPrice = ""; $onclickReloadPrice = ""; }
				if (!$priceStr == "" && $_value->getPriceType() == 'absolute') { $getChargetxt = ""; $onclickReloadPrice = ""; }
				if ($_value->getPrice() != 0 && $_value->getPriceType() == 'absolute') { $getChargetxt = ' ' . Mage::getStoreConfig('humsayaa/general/default_description'); }
				if ($_value->getPrice() == 0 && $_value->getPriceType() == 'absolute') { $getChargetxt = ""; }
				/** [end] Changes by Ishtiaq */
				
                $select->addOption(
                    $_value->getOptionTypeId(),
                    /** Changes by Ishtiaq */
                    $_value->getTitle() . ' ' . $priceStr . ' ' . $getChargetxt . '',
                    /** [end] Changes by Ishtiaq */
                    array('price' => $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false))
                );
            }
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                $extraParams = ' multiple="multiple"';
            }
            if (!$this->getSkipJsReloadPrice()) {
                /** Changes by Ishtiaq */
                $extraParams .= " $onchangeReloadPrice";
                /** [end] Changes by Ishtiaq */
            }
            $select->setExtraParams($extraParams);

            if ($configValue) {
                $select->setValue($configValue);
            }

            return $select->getHtml();
        }

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO 
		    || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX) {
            $selectHtml = '<ul id="options-'.$_option->getId().'-list" class="options-list">';
            $require = ($_option->getIsRequire()) ? ' validate-one-required-by-name' : '';
            $arraySign = '';
		
            switch ($_option->getType()) {
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
                    $type = 'radio';
                    $class = 'radio';
                    if (!$_option->getIsRequire()) {
                        /** Changes by Ishtiaq */
                        $selectHtml .= '<li><input type="radio" id="options_' . $_option->getId() . '" class="'
							. $class .' '.$product_custom_option.'" name="options[' . $_option->getId() . ']" ' 
							. ($this->getSkipJsReloadPrice() ? '' : $onclickReloadPrice) 
							. ' value="" checked="checked" /><span class="label"><label for="options_'
							. $_option->getId() . '">' . $this->__('None') . '</label></span></li>';
                        /** [end] Changes by Ishtiaq */
                    }
                    break;
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
                    $type = 'checkbox';
                    $class = 'checkbox';
                    $arraySign = '[]';
                    break;
            }
            $count = 1;
            foreach ($_option->getValues() as $_value) {
                $count++;

                $priceStr = $this->_formatPrice(array(
                    'is_percent'    => ($_value->getPriceType() == 'percent'),
                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent')
                ));

				/** Changes by Ishtiaq */
				if ($_value->getPriceType() == 'absolute') 	{ $product_custom_option = "product-custom-option-absolute"; $onclickReloadPrice = ""; }
				if (!$priceStr == "" && $_value->getPriceType() == 'absolute') { $getChargetxt = ""; $onclickReloadPrice = ""; }
				if ($_value->getPrice() != 0 && $_value->getPriceType() == 'absolute') { $getChargetxt = ' ' . Mage::getStoreConfig('humsayaa/general/default_description'); }
				if ($_value->getPrice() == 0 && $_value->getPriceType() == 'absolute') { $getChargetxt = ""; }
				/** [end] Changes by Ishtiaq */
				
                $htmlValue = $_value->getOptionTypeId();
                if ($arraySign) {
                    $checked = (is_array($configValue) && in_array($htmlValue, $configValue)) ? 'checked' : '';
                } else {
                    $checked = $configValue == $htmlValue ? 'checked' : '';
                }

                $selectHtml .= '<li>'
                    /** Changes by Ishtiaq */
					. '<input type="' . $type . '" class="' . $class . ' ' . $require
					. ' '.$product_custom_option.'" '
					. ($this->getSkipJsReloadPrice() ? '' : $onclickReloadPrice) 
					. ' name="options[' . $_option->getId() . ']' . $arraySign . '" id="options_' . $_option->getId()
					. '_' . $count . '" value="' . $htmlValue . '" ' . $checked . ' price="' 
					. $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />' 
					. '<span class="label"><label for="options_' . $_option->getId() . '_' . $count . '">'
					. $_value->getTitle() . ' ' . $priceStr . '</label>' . $getChargetxt . '</span>';
                    /** [end] Changes by Ishtiaq */
                if ($_option->getIsRequire()) {
                    $selectHtml .= '<script type="text/javascript">' . '$(\'options_' . $_option->getId() . '_'
					. $count . '\').advaiceContainer = \'options-' . $_option->getId() . '-container\';' 
					. '$(\'options_' . $_option->getId() . '_' . $count 
					. '\').callbackFunction = \'validateOptionsCallback\';' . '</script>';
                }
                $selectHtml .= '</li>';
            }
            $selectHtml .= '</ul>';

            return $selectHtml;
        }
    }

}
