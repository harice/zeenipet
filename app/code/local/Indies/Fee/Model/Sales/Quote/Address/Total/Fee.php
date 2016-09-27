<?php
class Indies_Fee_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	protected $_code = 'fee';

	const XML_PATH_DISPLAY_CART_PRICE       = 'tax/cart_display/price';
    const XML_PATH_DISPLAY_CART_SUBTOTAL    = 'tax/cart_display/subtotal';
    const XML_PATH_DISPLAY_CART_SHIPPING    = 'tax/cart_display/shipping';
    const XML_PATH_DISPLAY_CART_DISCOUNT    = 'tax/cart_display/discount';
    const XML_PATH_DISPLAY_CART_GRANDTOTAL  = 'tax/cart_display/grandtotal';

	const DISPLAY_TYPE_EXCLUDING_TAX = 1;
    const DISPLAY_TYPE_INCLUDING_TAX = 2;
    const DISPLAY_TYPE_BOTH = 3;

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$items = $this->_getAddressItems($address);

		if (!count($items)) {
			return $this;
		}
		
		// Partial Payment Disabled or Guest Checkout Disabled.
		$status = $partialpaymentHelper->getPartialPaymentStatus();
		$validCustomer = $partialpaymentHelper->isValidCustomer();
		
		$quote = $calculationHelper->getQuote();
				
		if($status == 0)
		{
			$tax = $address->getSubtotalInclTax() - $address->getSubtotal();
			if($tax > 0){
				$this->_addAmount($tax);
				$this->_addBaseAmount($tax);
			}
			$address->setFeeAmount(0);
			//$address->setBaseFeeAmount(0);
		}
		
		if(!$validCustomer && $status == 1)
		{
			if(!($quote->getCustomerId() && $quote->getCustomerGroupId()))
			{
				$tax = $address->getSubtotalInclTax() - $address->getSubtotal();
				if($tax > 0){
					$this->_addAmount($tax);
					$this->_addBaseAmount($tax);
				}	
				$address->setFeeAmount(0);
				//$address->setBaseFeeAmount(0);
			}
		}
			
		if($calculationHelper->canApply()){
		
			$fee = Indies_Fee_Model_Fee::getFee();
					
			/* Tax Caluclation Start */
			$tax = $address->getSubtotalInclTax() - $address->getSubtotal();
			
			if($tax > 0){
				$this->_addAmount($tax);
				$this->_addBaseAmount($tax);
			}
			$taxFinal = 0;
			
			if($tax > 0) {
				$taxFinal = $calculationHelper->shippingTaxCalculation($tax);
			}
			
			
			/* Tax Caluclation End */
			
			/* Shipping Caluclation Start */
			$shipping = (float) $address->getShippingAmount();
			$shippingFinal = 0;
			if($address->getShippingAmount()>0)
			$shippingFinal = $calculationHelper->shippingTaxCalculation($shipping);

			if($fee > 0 && !$partialpaymentHelper->isPartialPaymentOptional() && Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_SHIPPING) == self::DISPLAY_TYPE_EXCLUDING_TAX)
			{
				$shippingTax = $address->getShippingInclTax() - $address->getShippingAmount();
				$dividedShippingTax = $shippingTax / 2;
				$fee = $fee + $shippingFinal + $taxFinal + $dividedShippingTax;
			}
			elseif($fee > 0) {
				$fee = $fee + $shippingFinal + $taxFinal;
			}
			/* Shipping Caluclation End */	


			if(abs($address->getDiscountAmount())>0)
			{
				$discount = $calculationHelper->discountCalculation(abs($address->getDiscountAmount()));
				if($fee>0) {
					$fee = $fee - $discount;
				}
				$address->setFeeAmount($fee);
				//$address->setBaseFeeAmount($calculationHelper->convertCurrencyAmount($fee));
			} else {
				$address->setFeeAmount($fee);
				//$address->setBaseFeeAmount($calculationHelper->convertCurrencyAmount($fee));
			}

			if($partialpaymentHelper->isEnabledWithSurcharge()){
				if(!$partialpaymentHelper->isApplyToWholeCart()){
				$outOfStockDiscount = $calculationHelper->getTotalOutOfStockDiscount($quote);
				$address->setFeeAmount($fee - $outOfStockDiscount);
				//$address->setBaseFeeAmount($calculationHelper->convertCurrencyAmount($fee - $outOfStockDiscount));
				}
			}

			$address->setSubtotal($address->getSubtotal() + Indies_Fee_Model_Fee::$surchargeAmt);
			//$address->setBaseSubtotal($calculationHelper->convertCurrencyAmount($address->getBaseSubtotal() + Indies_Fee_Model_Fee::$surchargeAmt));

			//$address->setSubtotalInclTax($address->getSubtotal() + Indies_Fee_Model_Fee::$surchargeAmt);
			$address->setSubtotalInclTax($address->getSubtotalInclTax() + Indies_Fee_Model_Fee::$surchargeAmt);
			//$address->setBaseSubtotalInclTax($calculationHelper->convertCurrencyAmount($address->getBaseSubtotalInclTax() + Indies_Fee_Model_Fee::$surchargeAmt));

			$address->setGrandTotal($address->getGrandTotal() + Indies_Fee_Model_Fee::$surchargeAmt);
			//$address->setBaseGrandTotal($calculationHelper->convertCurrencyAmount($address->getBaseGrandTotal() + Indies_Fee_Model_Fee::$surchargeAmt));
	  }
	}


	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$partial_payment_amount = $address->getFeeAmount();

		if ($partial_payment_amount) {		
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
			$calculationHelper = Mage::helper('partialpayment/calculation');

			if ($partialpaymentHelper->isEnabledWithSurcharge() && Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL) == self::DISPLAY_TYPE_INCLUDING_TAX && $address->getSubtotal() == $address->getSubtotalInclTax()) {
				$partial_payment_amount = $partial_payment_amount - (Indies_Fee_Model_Fee::$subTotal - $address->getSubtotal());
			}

			if($partialpaymentHelper->isApplyToWholeCart()) {
				$condition = (($partial_payment_amount > 0) && ($address->getSubtotalInclTax() >= $partialpaymentHelper->getMinimumOrderAmount()) && ($address->getGrandTotal() > $partial_payment_amount));
			}
			else {
				$condition = (($partial_payment_amount > 0) && ($address->getGrandTotal() > $partial_payment_amount) );
			}

			if ($condition) {
				$address->addTotal(array(
						'code'=>$this->getCode(),
						'title'=>Mage::helper('fee')->formatFee($partial_payment_amount),
						'value'=> $partial_payment_amount,
						'area' => 'footer'
				), 'grand_total');
			}
			else {
				$address->setFeeAmount('0');
				//$address->setBaseFeeAmount('0');

				$partial_payment_amount = $address->getFeeAmount();
			}
		}
		return $this;
	}
}