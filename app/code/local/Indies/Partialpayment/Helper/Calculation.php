<?php

class Indies_Partialpayment_Helper_Calculation extends Mage_Core_Helper_Abstract
{
	const XML_PATH_DISPLAY_CART_PRICE       = 'tax/cart_display/price';
    const XML_PATH_DISPLAY_CART_SUBTOTAL    = 'tax/cart_display/subtotal';
    const XML_PATH_DISPLAY_CART_SHIPPING    = 'tax/cart_display/shipping';
    const XML_PATH_DISPLAY_CART_DISCOUNT    = 'tax/cart_display/discount';
    const XML_PATH_DISPLAY_CART_GRANDTOTAL  = 'tax/cart_display/grandtotal';

	const DISPLAY_TYPE_EXCLUDING_TAX = 1;
    const DISPLAY_TYPE_INCLUDING_TAX = 2;
    const DISPLAY_TYPE_BOTH = 3;

	public function getQuote()
	{ 
		
	// Check Indies_Ppadmin Module whether is Active or not.
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;
 
		if(isset($modulesArray['Indies_Partialpaymentadmin'])) {
			$session = Mage::getSingleton('admin/session');
			if ($session->isLoggedIn()) {
				$quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
			}else{
				$quote = Mage::getSingleton('checkout/cart')->getQuote();
			}
		}else{
			$quote = Mage::getSingleton('checkout/cart')->getQuote();
		}
	// Modified Started by Indies on 19_11_2012 for Virtual & Downloadable Product
		return $quote;
	}


	public function getSubtotal($quote)
	{
		$billing_address_id = $quote->getBillingAddress()->getSubtotalInclTax();
		$shipping_address_id = $quote->getShippingAddress()->getSubtotalInclTax();

		if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
			if($billing_address_id > 0) {
				$subTotal = $quote->getBillingAddress()->getSubtotal();
			}
			else {
				$subTotal = $quote->getShippingAddress()->getSubtotal();
			}
		} elseif (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL) == self::DISPLAY_TYPE_INCLUDING_TAX) {
			if($billing_address_id > 0) {
				$subTotal = $quote->getBillingAddress()->getSubtotal();
			}
			else {
				$subTotal = $quote->getShippingAddress()->getSubtotal();
			}
		} else {
			if($billing_address_id > 0) {
				$subTotal = $quote->getBillingAddress()->getSubtotalInclTax();
			}
			else {
				$subTotal = $quote->getShippingAddress()->getSubtotalInclTax();
			}
		}
		return $subTotal;
	}


	public function getBaseSubtotal($quote)
	{
		$billing_address_id = $quote->getBillingAddress()->getBaseSubtotalInclTax();
		$shipping_address_id = $quote->getShippingAddress()->getBaseSubtotalInclTax();

		if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
			if($billing_address_id > 0) {
				$subTotal = $quote->getBillingAddress()->getBaseSubtotal();
			}
			else {
				$subTotal = $quote->getShippingAddress()->getBaseSubtotal();
			}
		} elseif (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL) == self::DISPLAY_TYPE_INCLUDING_TAX) {
			if($billing_address_id > 0) {
				$subTotal = $quote->getBillingAddress()->getBaseSubtotalInclTax();
			}
			else {
				$subTotal = $quote->getShippingAddress()->getBaseSubtotalInclTax();
			}
		} else {
			if($billing_address_id > 0) {
				$subTotal = $quote->getBillingAddress()->getBaseSubtotalInclTax();
			}
			else {
				$subTotal = $quote->getShippingAddress()->getBaseSubtotalInclTax();
			}
		}
		return $subTotal;
	}


	public function getSurchargeCalculation($quote,$subTotal)
	{
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

			if ($partialpaymentHelper->isApplyToWholeCart()){
				$outOfStockDiscount = $this->getTotalOutOfStockDiscount($quote);
				Indies_Fee_Model_Fee::$wholeCartFlag = Mage::getSingleton('core/session')->getPP();

				if(Indies_Fee_Model_Fee::$wholeCartFlag)
				{
					$surcharge_amount = 0;
					if($partialpaymentHelper->isSurchargeOptionsSingleSurcharge())
					{
						if ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments() && Indies_Fee_Model_Fee::$wholeCartFlag) {
							if($partialpaymentHelper->isInstallmentCalculationTypeFixed())
							{
								$surchargeValue = $partialpaymentHelper->getSingleSurchargeValue();
								Indies_Fee_Model_Fee::$surchargeAmt = $surchargeValue;
								$surchargeAmountCalculate = (($subTotal + $surchargeValue) / Indies_Fee_Model_Fee::$wholeCartFlag);
								return ($subTotal + $surchargeValue) - $surchargeAmountCalculate;
							}
							else
							{
								$subTotal = $subTotal - $outOfStockDiscount;
								$surchargeValue = $partialpaymentHelper->getSingleSurchargeValue();
								$surcharge_amount = ($subTotal * $surchargeValue) / 100;
								Indies_Fee_Model_Fee::$surchargeAmt = $surcharge_amount;
								$surchargeAmountCalculate = ($subTotal + $surcharge_amount) / Indies_Fee_Model_Fee::$wholeCartFlag;

								if($surcharge_amount > 0) {
									$surcharge_amount = ($subTotal + $surcharge_amount) - $surchargeAmountCalculate;
								}	
								return $surcharge_amount;	
							}
						}
						else {
							if($partialpaymentHelper->isInstallmentCalculationTypeFixed())
							{
								$surchargeValue = $partialpaymentHelper->getSingleSurchargeValue();
								Indies_Fee_Model_Fee::$surchargeAmt = $surchargeValue;
								return (($subTotal + $surchargeValue) - $partialpaymentHelper->getFirstInstallmentAmount());
							}
							else
							{
								$subTotal = $subTotal - $outOfStockDiscount;
								$surchargeValue = $partialpaymentHelper->getSingleSurchargeValue();
								$surcharge_amount = ($subTotal * $surchargeValue) / 100;
								Indies_Fee_Model_Fee::$surchargeAmt = $surcharge_amount ;
								$downPayment = $partialpaymentHelper->getFirstInstallmentAmount();
	
								if(Indies_Fee_Model_Fee::$wholeCartFlag != 0) {
									$surchargeAmountCalculate = (($subTotal + $surcharge_amount) * $downPayment) / 100;
								}
	
								if($surcharge_amount > 0) {
									$surcharge_amount = ($subTotal + $surcharge_amount) - $surchargeAmountCalculate;
								}	
								return $surcharge_amount;	
							}
						}
					}
					elseif($partialpaymentHelper->isSurchargeOptionsMultipleSurcharge())
					{
						$surchargeValues = $partialpaymentHelper->getMultipleSurchargeValues();
						$surchargeValues = explode(",", $surchargeValues);
						$surchargeValues = array_filter($surchargeValues, 'strlen');
						$surchargeValues = array_combine(range(1, count($surchargeValues)), array_values($surchargeValues));

						if ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments() && Indies_Fee_Model_Fee::$wholeCartFlag) {
							if($partialpaymentHelper->isInstallmentCalculationTypeFixed())
							{
								$surchargeValue = $surchargeValues[Indies_Fee_Model_Fee::$wholeCartFlag];
								Indies_Fee_Model_Fee::$surchargeAmt = $surchargeValue;
								$surchargeAmountCalculate = (($subTotal + $surchargeValue) / Indies_Fee_Model_Fee::$wholeCartFlag);
								return (($subTotal + $surchargeValue) - $surchargeAmountCalculate);
							}
							else
							{
								$surchargeValue = $surchargeValues[Indies_Fee_Model_Fee::$wholeCartFlag];
								$subTotal = $subTotal - $outOfStockDiscount;
								$surcharge_amount = ($subTotal * $surchargeValue) / 100;
								Indies_Fee_Model_Fee::$surchargeAmt = $surcharge_amount ;
								$surchargeAmountCalculate = ($subTotal + $surcharge_amount) / Indies_Fee_Model_Fee::$wholeCartFlag;

								if($surcharge_amount > 0) {
									$surcharge_amount = ($subTotal + $surcharge_amount) - $surchargeAmountCalculate;
								}

								return $surcharge_amount;	
							}
						}
						else {
							if($partialpaymentHelper->isInstallmentCalculationTypeFixed())
							{
								if ($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
									$totalInstallment = $partialpaymentHelper->getTotalNoOfInstallment(); 
									$surchargeValue = $surchargeValues[$totalInstallment];
								}
								else {
									$surchargeValue = $surchargeValues[Indies_Fee_Model_Fee::$wholeCartFlag];
								}
								Indies_Fee_Model_Fee::$surchargeAmt = $surchargeValue;
								return (($subTotal + $surchargeValue) - $partialpaymentHelper->getFirstInstallmentAmount());
							}
							else
							{
								if ($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
									$totalInstallment = $partialpaymentHelper->getTotalNoOfInstallment(); 
									$surchargeValue = $surchargeValues[$totalInstallment];
								}
								else {
									$surchargeValue = $surchargeValues[Indies_Fee_Model_Fee::$wholeCartFlag];
								}
	
								$subTotal = $subTotal - $outOfStockDiscount;
								$surcharge_amount = ($subTotal * $surchargeValue) / 100;
								Indies_Fee_Model_Fee::$surchargeAmt = $surcharge_amount ;
								$downPayment = $partialpaymentHelper->getFirstInstallmentAmount();
	
								if(Indies_Fee_Model_Fee::$wholeCartFlag != 0) {
									$surchargeAmountCalculate = (($subTotal + $surcharge_amount) * $downPayment) / 100;
								}
	
								if($surcharge_amount > 0) {
									$surcharge_amount = ($subTotal + $surcharge_amount) - $surchargeAmountCalculate;
								}
	
								return $surcharge_amount;	
							}
						}
					}
				}
			}
			else {
				Indies_Fee_Model_Fee::$surchargeAmt = 0;
				$items = $quote->getAllVisibleItems();

				$fee = 0;

				foreach($items as $item){
					$fee += $this->getSurchargeByItem($item);
				}

				if($fee > 0){
					$fee = ($subTotal + Indies_Fee_Model_Fee::$surchargeAmt) - $this->convertToCurrentCurrencyAmount($fee);
				}
				return $fee;
			}
	}


	public function discountCalculation($discountValue)
	{
			$discount = 0;
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
				if(!$partialpaymentHelper->isPartialPaymentOptional() || ($partialpaymentHelper->isPartialPaymentOptional() && $partialpaymentHelper->isApplyToWholeCart()))
				{
					if($partialpaymentHelper->isPartialPaymentOption2Installments())
					{
						if($partialpaymentHelper->isApplyToAllProducts())
						{
							if($partialpaymentHelper->isAllproducts2InstallmentsDiscountCalculationOptionsDistributeEquallyInInstallment())
							$discount = abs($discountValue)/2;
						}elseif($partialpaymentHelper->isApplyToWholeCart()){
							if($partialpaymentHelper->isWholecartDiscountCalculationOptionsDistributeEquallyInInstallment())
							$discount = abs($discountValue)/2;
						}
						
					}elseif($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()){
						if($partialpaymentHelper->isApplyToAllProducts())
						{
							if($partialpaymentHelper->isAllproductsFixedInstallmentsDiscountCalculationOptionsDistributeEquallyInInstallment()){
								$totalInstallment = $partialpaymentHelper->getTotalNoOfInstallment();
								$discount = abs($discountValue)/$totalInstallment;
								$discount = $discount * ($totalInstallment - 1);
							}
						}elseif($partialpaymentHelper->isApplyToWholeCart()){
								if($partialpaymentHelper->isWholecartDiscountCalculationOptionsDistributeEquallyInInstallment()){
									$totalInstallment = $partialpaymentHelper->getTotalNoOfInstallment();
									$discount = abs($discountValue)/$totalInstallment;
									$discount = $discount * ($totalInstallment - 1);
								}
							}
					}
				}
				if($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()){
					if($partialpaymentHelper->isApplyToWholeCart()){
							if($partialpaymentHelper->isWholecartDiscountCalculationOptionsDistributeEquallyInInstallment()){
								$totalInstallment = Mage::getSingleton('core/session')->getPP();
								$discount = abs($discountValue)/$totalInstallment;
								$discount = $discount * ($totalInstallment - 1);
							}
					}
				}
				return $discount;
	}
	
	public function shippingTaxCalculation($value)
	{
		$valueFinal = 0;
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
				if(!$partialpaymentHelper->isPartialPaymentOptional() || ($partialpaymentHelper->isPartialPaymentOptional() && $partialpaymentHelper->isApplyToWholeCart()))
				{
					if($partialpaymentHelper->isPartialPaymentOption2Installments())
					{
						if($partialpaymentHelper->isApplyToAllProducts())
						{
							if($partialpaymentHelper->isAllproducts2InstallmentShippingTaxCalculationOptionsDistributeEquallyInInstallment())
							$valueFinal = $value/2;
						}elseif($partialpaymentHelper->isApplyToWholeCart()){
							if($partialpaymentHelper->isWholecartShippingTaxCalculationOptionsDistributeEquallyInInstallment())
							$valueFinal = $value/2;
						}
						
					}elseif($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()){
						if($partialpaymentHelper->isApplyToAllProducts())
						{
							if($partialpaymentHelper->isAllproductsFixedInstallmentShippingTaxCalculationOptionsDistributeEquallyInInstallment()){
								
								$totalInstallment = $partialpaymentHelper->getTotalNoOfInstallment();
								$valueFinal = $value/$totalInstallment;
								$valueFinal = $valueFinal * ($totalInstallment - 1);
							}
						}elseif($partialpaymentHelper->isApplyToWholeCart()){
								if($partialpaymentHelper->isWholecartShippingTaxCalculationOptionsDistributeEquallyInInstallment()){
									$totalInstallment = $partialpaymentHelper->getTotalNoOfInstallment();
									$valueFinal =$value/$totalInstallment;
									$valueFinal = $valueFinal * ($totalInstallment - 1);
								}
							}
					}
				}
				if($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()){
					if($partialpaymentHelper->isApplyToWholeCart()){
							if($partialpaymentHelper->isWholecartShippingTaxCalculationOptionsDistributeEquallyInInstallment()){
								$totalInstallment = Mage::getSingleton('core/session')->getPP();
								$valueFinal = $value/$totalInstallment;
								$valueFinal = $valueFinal * ($totalInstallment - 1);
							}
					}
				}
			return $valueFinal;
	}


	public function wholecartCalculation($subTotal,$quote)
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		Indies_Fee_Model_Fee::$wholeCartFlag = Mage::getSingleton('core/session')->getPP();
		$outOfStockDiscount = $this->getTotalOutOfStockDiscount($quote);

		$subTotal = $subTotal - $outOfStockDiscount;

		if(Indies_Fee_Model_Fee::$wholeCartFlag)
		{
			if ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
				return ($subTotal - ($subTotal / Indies_Fee_Model_Fee::$wholeCartFlag));
			}
			else {
				if ($partialpaymentHelper->isInstallmentCalculationTypePercentage())
				{
					return ($subTotal - (($subTotal * $partialpaymentHelper->getFirstInstallmentAmount()) / 100));
				}
				else
				{
					return ($subTotal - $partialpaymentHelper->getFirstInstallmentAmount());
				}
			}
		}
	}


	public function getCalculationWithoutSurcharge($quote,$subTotal)
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		if ($partialpaymentHelper->isApplyToWholeCart()){
			return 	$this->wholecartCalculation($subTotal,$quote);			
		}

		$items = $quote->getAllVisibleItems();
		$fee = 0;

		foreach($items as $item) {
			$fee += $this->getFeeByItem($item);
		}
		return $fee;
	}


	public function getTotalOutOfStockDiscount($quote)
	{
		$items = $quote->getAllVisibleItems();
		$discount = 0;
		foreach($items as $item)
			$discount += $this->getOutstockDiscountByItem($item);
				
		return $discount;
	}


	public function getFeeByItem($item) {
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		$allow_partial_payment = $partialpaymentHelper->getAllowPartialPayment($item);
		$outOfStockDiscount = 0;

		if ( $allow_partial_payment && ( ($partialpaymentHelper->isApplyToAllProducts()) || ($partialpaymentHelper->isApplyToSpecificProductsOnly()) || ($partialpaymentHelper->isApplyToOutOfStockProducts() && $partialpaymentHelper->isOutOfStockProduct($item->getProductId())) ) ) {
			if ($partialpaymentHelper->isInstallmentCalculationTypeFixed())
			{
				$outOfStockDiscount = $this->getOutstockDiscountByItem($item);

				if($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
					// Item Price Excluding Tax
					if(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
						$partial_payment_amount = ( (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) - ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) / $allow_partial_payment) );
					}
					// Item Price Including Tax
					elseif(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
						$partial_payment_amount = ( (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) - ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) / $allow_partial_payment) );
					}
					// Item Price with Including & Excluding Tax
					else {
						$partial_payment_amount = ( (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) - ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) / $allow_partial_payment) );
					}
				}
				else {
					// Item Price Excluding Tax
					if(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
						$partial_payment_amount = ( (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) - ($partialpaymentHelper->getFirstInstallmentAmount() * $item->getQty()) );
					}
					// Item Price Including Tax
					elseif(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
						$partial_payment_amount = ( (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) - ($partialpaymentHelper->getFirstInstallmentAmount() * $item->getQty()) );
					}
					// Item Price with Including & Excluding Tax
					else {
						$partial_payment_amount = ( (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) - ($partialpaymentHelper->getFirstInstallmentAmount() * $item->getQty()) );
					}
				}
				return ($partial_payment_amount > 0 ? $partial_payment_amount : 0);
			}
			else
			{
				// Item Price Excluding Tax
				$outOfStockDiscount = $this->getOutstockDiscountByItem($item);

				if($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
					// Item Price Excluding Tax
					if(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
						$partial_payment_amount = ( (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) - ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) / $allow_partial_payment) );
					}
					// Item Price Including Tax
					elseif(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
						$partial_payment_amount = ( (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) - ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) / $allow_partial_payment) );
					}
					// Item Price with Including & Excluding Tax
					else {
						$partial_payment_amount = ( (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) - ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) / $allow_partial_payment) );
					}
				}
				else {
					if(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
						$partial_payment_amount = ( (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) - (((($item->getPrice() - $outOfStockDiscount) * $partialpaymentHelper->getFirstInstallmentAmount()) / 100) * $item->getQty()) );
					}
					// Item Price Including Tax
					elseif(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
						$partial_payment_amount = ( (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) - (((($item->getPriceInclTax() - $outOfStockDiscount) * $partialpaymentHelper->getFirstInstallmentAmount()) / 100) * $item->getQty()) );
					}
					// Item Price with Including & Excluding Tax
					else {
						$partial_payment_amount = ( (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) - (((($item->getPriceInclTax() - $outOfStockDiscount) * $partialpaymentHelper->getFirstInstallmentAmount()) / 100) * $item->getQty()) );
					}
				}
				return ($partial_payment_amount > 0 ? $partial_payment_amount : 0);
			}
		}
		return 0;	
	}


	public static function getOutstockDiscountByItem($item){
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		
		if($partialpaymentHelper->isOutOfStockProduct($item->getProductId()) &&$partialpaymentHelper->isApplyToWholeCart() )
					$allow_partial_payment = Mage::getSingleton('core/session')->getPP();
		else
					$allow_partial_payment = $partialpaymentHelper->getAllowPartialPayment($item);
		
		if ( $allow_partial_payment && ( ($partialpaymentHelper->isApplyToAllProducts()) || ($partialpaymentHelper->isApplyToSpecificProductsOnly()) || ($partialpaymentHelper->isApplyToOutOfStockProducts() && $partialpaymentHelper->isOutOfStockProduct($item->getProductId())) ) || $allow_partial_payment) {
			if($partialpaymentHelper->isOutOfStockProduct($item->getProductId()))
			{
				
				if ($partialpaymentHelper->isPreOrderDiscountCalculationTypeFixedAmount())
				{
					if($partialpaymentHelper->getMinimumOrderAmount() <= $item->getPrice())
								$outstock_discount_amount = $partialpaymentHelper->getPreOrderDiscount() * $item->getQty();		
					else 
						return 0;
					
					return ($outstock_discount_amount > 0?$outstock_discount_amount:0);
				}
				else{
					
						if(Mage::getStoreConfig(Indies_Deposit_Model_Deposit::XML_PATH_DISPLAY_CART_PRICE) == Indies_Deposit_Model_Deposit::DISPLAY_TYPE_EXCLUDING_TAX)
						{
							if($partialpaymentHelper->getMinimumOrderAmount()<=$item->getPrice())
										$outstock_discount_amount =(($item->getPrice() * $partialpaymentHelper->getPreOrderDiscount()) / 100) * $item->getQty();
							else
								return 0;						
						
						}
						elseif(Mage::getStoreConfig(Indies_Deposit_Model_Deposit::XML_PATH_DISPLAY_CART_PRICE) == Indies_Deposit_Model_Deposit::DISPLAY_TYPE_INCLUDING_TAX)
						{
							if($partialpaymentHelper->getMinimumOrderAmount()<=$item->getPrice())
										$outstock_discount_amount =(($item->getPriceInclTax() * $partialpaymentHelper->getPreOrderDiscount()) / 100) * $item->getQty();
							
							else
								return 0;
						}
						else
						{
							if($partialpaymentHelper->getMinimumOrderAmount()<=$item->getPrice())
										$outstock_discount_amount =(($item->getPriceInclTax() * $partialpaymentHelper->getPreOrderDiscount()) / 100) * $item->getQty();
							else
								return 0;
						}		
						
						return ($outstock_discount_amount > 0?$outstock_discount_amount:0);
				
				}
				
			}
		}
		return 0;	
	}


	public function getSingleSurchargeCalculation($item, $allow_partial_payment = '')
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$surchargeValue = $partialpaymentHelper->getSingleSurchargeValue();
		$downPayment = $partialpaymentHelper->getFirstInstallmentAmount();
		$outOfStockDiscount = 0;

		if ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments() && $allow_partial_payment) {
			if ($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
				if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surchargeValue);
					$surchargeFee = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surchargeValue) / $allow_partial_payment;
					return $surchargeFee;
				}
				elseif (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surchargeValue);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surchargeValue) / $allow_partial_payment;
					return $surchargeFee;
				}
				else {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surchargeValue);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surchargeValue) / $allow_partial_payment;
					return $surchargeFee;
				}
			}
			else {
				if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $allow_partial_payment;
					return $surchargeFee;
				}
				elseif (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $allow_partial_payment;
					return $surchargeFee;
				}
				else {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $allow_partial_payment;
					return $surchargeFee;
				}
			}
		}
		else {
			if ($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
				Indies_Fee_Model_Fee::$surchargeAmt += $surchargeValue;
				return $partialpaymentHelper->getFirstInstallmentAmount();
			}
			else {
				if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
					return $surchargeFee;
				}
				elseif (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
					return $surchargeFee;
				}
				else {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
					return $surchargeFee;
				}
			}
		}
	}


	public function getMultipleSurchargeCalculation($value, $item)
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$downPayment = $partialpaymentHelper->getFirstInstallmentAmount();
		$surchargeValues = $partialpaymentHelper->getMultipleSurchargeValues();
		$surchargeValues = explode(",", $surchargeValues);
		$surchargeValues = array_filter($surchargeValues, 'strlen');
		$surchargeValues = array_combine(range(1, count($surchargeValues)), array_values($surchargeValues));
		$outOfStockDiscount = 0;

		if ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments() && $value) {
			if ($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
				if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surchargeValues[$value]);
					$surchargeFee = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surchargeValues[$value]) / $value;
					return $surchargeFee;
				}
				elseif (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surchargeValues[$value]);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surchargeValues[$value]) / $value;
					return $surchargeFee;
				}
				else {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surchargeValues[$value]);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surchargeValues[$value]) / $value;
					return $surchargeFee;
				}
			}
			else {
				if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $value;
					return $surchargeFee;
				}
				elseif (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $value;
					return $surchargeFee;
				}
				else {
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $value;
					return $surchargeFee;
				}
			}
		}
		else {
			if($partialpaymentHelper->isInstallmentCalculationTypeFixed())
			{
				Indies_Fee_Model_Fee::$surchargeAmt += $surchargeValues[$value];
				return $partialpaymentHelper->getFirstInstallmentAmount();
			}
			else {
				if(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX){
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
					return $surchargeFee;
				}
				elseif(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX){
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
					return $surchargeFee;
				}
				else{
					$outOfStockDiscount = $this->getOutstockDiscountByItem($item);
					$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
					Indies_Fee_Model_Fee::$surchargeAmt += $this->convertToCurrentCurrencyAmount($surcharge_amount);
					$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
					return $surchargeFee;
				}
			}
		}
	}


	public function getSurchargeByItem($item)
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$allow_partial_payment = $partialpaymentHelper->getAllowPartialPayment($item);

		if($allow_partial_payment) {
			if($partialpaymentHelper->isPartialPaymentOption2Installments())
			{
				if($partialpaymentHelper->isSurchargeOptionsSingleSurcharge()) {
					return $this->getSingleSurchargeCalculation($item);
				}
				elseif($partialpaymentHelper->isSurchargeOptionsMultipleSurcharge()) {
					return $this->getMultipleSurchargeCalculation(2,$item);
				}
			}
			elseif($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
				if($partialpaymentHelper->isSurchargeOptionsSingleSurcharge()) {
					return $this->getSingleSurchargeCalculation($item);
				}
				elseif($partialpaymentHelper->isSurchargeOptionsMultipleSurcharge()) {
					$totalTotal = $partialpaymentHelper->getTotalNoOfInstallment();
					return $this->getMultipleSurchargeCalculation($totalTotal, $item);
				}
			}
			elseif ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
				if($partialpaymentHelper->isSurchargeOptionsSingleSurcharge()) {
					return $this->getSingleSurchargeCalculation($item, $allow_partial_payment);
				}
				elseif($partialpaymentHelper->isSurchargeOptionsMultipleSurcharge()) {
					return $this->getMultipleSurchargeCalculation($allow_partial_payment, $item);
				}
			}
		}
		else {
			if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
				return $item->getPrice() * $item->getQty();
			}
			elseif (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
				return $item->getPriceInclTax() * $item->getQty();
			}
			else {
				return $item->getPriceInclTax() * $item->getQty();
			}
		}
	}


	// This function converts base currency amount to current currency amount.
	public function convertToCurrentCurrencyAmount ($price)
	{
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();

		if ($baseCurrencyCode != $currentCurrencyCode) {
			$currencyRates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, $currentCurrencyCode);
			$currentCurrencyRate = $currencyRates[$currentCurrencyCode];
			$price = $price * $currentCurrencyRate;
		}

		return $price;
	}


	// This function converts current currency amount to base currency amount.
	public function convertCurrencyAmount ($price)
	{
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();

		if ($baseCurrencyCode != $currentCurrencyCode) {
			$currencyRates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, $currentCurrencyCode);
			$currentCurrencyRate = $currencyRates[$currentCurrencyCode];
			$price = $price / $currentCurrencyRate;
		}

		return $price;
	}


	public function getInclusiveSurchargeSubtotal ($_total) {
		if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL) == self::DISPLAY_TYPE_INCLUDING_TAX) {
			return $_total + Indies_Fee_Model_Fee::$surchargeAmt;
		}
		return $_total;
	}


	public function getBaseGrandTotal ($_totals)
	{
        $firstTotal = reset($_totals);
        if ($firstTotal) {
            $total = $this->convertCurrencyAmount($firstTotal->getAddress()->getGrandTotal());
            return Mage::app()->getStore()->getBaseCurrency()->format($total, array(), true);
        }
        return '-';
	}


	public function canApply(){
		$partialpaymentAuthentication = Mage::helper('partialpayment/data');
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$quote = $this->getQuote();

		if ($partialpaymentHelper->isEnabled() && !$partialpaymentAuthentication->canRun()) {
			Mage::getSingleton('checkout/session')->addNotice(Mage::helper('checkout')->__($partialpaymentAuthentication->getMessage()));
			return false;
		}
		elseif ($partialpaymentHelper->isEnabled()) {
			if ($partialpaymentHelper->isValidCustomer()) {
				if ($partialpaymentHelper->isCustomerCreditToRegistered()) {
					if (Mage::app()->getRequest()->getControllerName() == 'onepage') {
						if (!Mage::helper('customer')->isLoggedIn() && Mage::getSingleton('core/session')->getRemovePartialPayment()) {
							return false;
						}
					}
				}
				return true;
			}
		}
	}
}
