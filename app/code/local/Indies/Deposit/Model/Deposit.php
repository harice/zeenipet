<?php

class Indies_Deposit_Model_Deposit extends Varien_Object {
	const XML_PATH_DISPLAY_CART_PRICE       = 'tax/cart_display/price';
    const XML_PATH_DISPLAY_CART_SUBTOTAL    = 'tax/cart_display/subtotal';
    const XML_PATH_DISPLAY_CART_SHIPPING    = 'tax/cart_display/shipping';
    const XML_PATH_DISPLAY_CART_DISCOUNT    = 'tax/cart_display/discount';
    const XML_PATH_DISPLAY_CART_GRANDTOTAL  = 'tax/cart_display/grandtotal';

	const DISPLAY_TYPE_EXCLUDING_TAX = 1;
    const DISPLAY_TYPE_INCLUDING_TAX = 2;
    const DISPLAY_TYPE_BOTH = 3;

	public static $grandTotal = 0;
	public static $flag;

	public static function getGrandTotalDeposit(Mage_Sales_Model_Quote_Address $address)
	{
		if(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL) == self::DISPLAY_TYPE_EXCLUDING_TAX && Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX)
		{
           $tax = $address->getTaxAmount();
		}
		else {
			$tax = $address->getSubtotalInclTax() - $address->getSubtotal();
		}

		if ($address->getShippingAmount() > 0) {
			self::$flag = true;
		}

		if(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_GRANDTOTAL) == 0) {
			if($tax > 0) {
				$grandTotal = $address->getSubtotal() + $address->getDiscountAmount() + $tax + $address->getShippingAmount();
			}
			else {
				$grandTotal = $address->getSubtotal() + $address->getDiscountAmount() + $address->getShippingAmount();
			}
		}
		else{
			if($tax > 0) {
				$grandTotal = $address->getSubtotal() + $address->getDiscountAmount() + $tax + $address->getShippingAmount();
			}
			else {
				$grandTotal = $address->getSubtotal() + $address->getDiscountAmount() + $address->getShippingAmount();
			}
		}
		self::$grandTotal = $grandTotal;
		return $grandTotal;
	}
}