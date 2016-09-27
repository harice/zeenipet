<?php
/**
 * Humsayaa AbsolutePricing
 *
 * Product type price model
 *
 * 1702, 1701, 1700				- all use $confItemOption  & ->setConfigurationItemOption($confItemOption) -- returns float
 * 1620, 1610, 1510, 1501	    - all use $confItemOption  & ->setConfigurationItemOption($confItemOption) -- returns double
 * 1420, 1411, 1410, 1330		- all use $quoteItemOption & ->setQuoteItemOption($quoteItemOption) -- returns double
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      AbsolutePricing Team <enquiries@absolutepricing.com>
**/

class Humsayaa_AbsolutePricing_Model_Catalog_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Apply options price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param float $finalPrice
     * @return float
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
		//$MageVersion = Mage::getVersion();  // returns current magento version

        $fixedfinalprice = 0; $percentfinalprice = 0; $absolutefinalprice = 0;

        if ($optionIds = $product->getCustomOption('option_ids')) {
            $basePrice = $finalPrice;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {

                    $confItemOption = $product->getCustomOption('option_'.$option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItemOption($confItemOption);

					$getType = $option->getType();
					//$getValue = $option->getId(); get products id - ie. 29 - may use at a later date
					$finalPrice = $group->getOptionPrice($confItemOption->getValue(), 0); // gets base price

						if ($getType == "file" || $getType == "field" || $getType == "area" || $getType == "date" || $getType == "date_time" || $getType == "time" ) {
							$OptionGetPriceType = $option->getPriceType();
                            if ($OptionGetPriceType == 'fixed')     { $fixedfinalprice    += $group->getOptionPrice($confItemOption->getValue(), 0); }
                            if ($OptionGetPriceType == 'percent')     { $percentfinalprice  += $group->getOptionPrice($confItemOption->getValue(), $basePrice); }
                            if ($OptionGetPriceType == 'absolute')     { $absolutefinalprice += $group->getOptionPrice($confItemOption->getValue(), 0); }
						} else {
                            $ids = explode(',', $confItemOption->getValue());
                            foreach($ids as $option_value_id) {
                                $OptionGetPriceType = $option->getValueById($option_value_id)->getPriceType();
                                if ($OptionGetPriceType == 'fixed')     { $fixedfinalprice    += $group->getOptionPrice($confItemOption->getValue(), 0); }
                                if ($OptionGetPriceType == 'percent')     { $percentfinalprice  += $group->getOptionPrice($confItemOption->getValue(), $basePrice); }
                                if ($OptionGetPriceType == 'absolute')     { $absolutefinalprice += $group->getOptionPrice($confItemOption->getValue(), 0); }
                            }
						}


                }
            }
        }
		
		/** Changes by Ishtiaq */
		/** Calculates true final price */
		if ($qty <= 0){ $qty = 1; }
		if ($basePrice <= 0){ $basePrice = $finalPrice; }
		
		$finalPrice = ($basePrice + $fixedfinalprice + $percentfinalprice) + ($absolutefinalprice / $qty);
		/** [end] Changes by Ishtiaq */
		return $finalPrice;
    }

}
