<?php
class Indies_Deposit_Model_Sales_Quote_Address_Total_Deposit extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	protected $_code = 'deposit';
	// variable used for sagepay 1st payment start
	public static $deposit = 0;
 	// variable used for sagepay 1st payment end

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);

		$calculationHelper = Mage::helper('partialpayment/calculation');
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$items = $this->_getAddressItems($address);

		if (!count($items)) {
			return $this;
		}

		if($calculationHelper->canApply())
		 {
			$fee = Indies_Fee_Model_Fee::getFee();
			$grandTotal = Indies_Deposit_Model_Deposit::getGrandTotalDeposit($address) + Indies_Fee_Model_Fee::$surchargeAmt;

			$deposit_amount = 0;

			if(($fee) && ($grandTotal) && ($grandTotal > $fee)) {
				$deposit_amount = $grandTotal - $fee;
			}

			/* Tax Calculation Start */
			$tax = $address->getSubtotalInclTax() - $address->getSubtotal();
			$taxFinal = 0;

			if($tax>0) {
				$taxFinal = $calculationHelper->shippingTaxCalculation($tax);
			}

			$deposit_amount = $deposit_amount - $taxFinal;
			/* Tax Calculation End */		

			/* Shipping Calculation Start */				
			$shipping = (float) $address->getShippingAmount();
			$shippingFinal = 0;
			if($address->getShippingAmount()>0)
			$shippingFinal = $calculationHelper->shippingTaxCalculation($shipping);

			$deposit_amount = $deposit_amount - $shippingFinal;
			/* Shipping Calculation End */	

			if(abs($address->getDiscountAmount())>0)
			{
				/* Discount Calculation Start */
				$discount = $calculationHelper->discountCalculation(abs($address->getDiscountAmount()));
				$deposit_amount = $deposit_amount + $discount;
				/* Discount Calculation End */
				$address->setDepositAmount($deposit_amount);
				//$address->setBaseDepositAmount($calculationHelper->convertCurrencyAmount($deposit_amount));
			} else {
				$address->setDepositAmount($deposit_amount);
				//$address->setBaseDepositAmount($calculationHelper->convertCurrencyAmount($deposit_amount));
			}
			// variable used for sagepay 1st payment start
		   self::$deposit = $deposit_amount;
		   // variable used for sagepay 1st payment end
		}
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		$partial_payment_amount = $address->getFeeAmount();

		if (!Indies_Deposit_Model_Deposit::$flag) {
			$deposit_amount = $address->getDepositAmount() + $address->getShippingAmount();
		}
		else {
			$deposit_amount = $address->getDepositAmount();
		}

		if (!$partialpaymentHelper->isEnabledWithSurcharge()) {
			$address->setDepositAmount($address->getGrandTotal() - $partial_payment_amount);
			$deposit_amount = $address->getDepositAmount();
		}

		if ($partial_payment_amount) {
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

			if($partialpaymentHelper->isApplyToWholeCart()) {
				$condition = (($partial_payment_amount > 0) && ($deposit_amount > 0) && ($address->getSubtotalInclTax() >= $partialpaymentHelper->getMinimumOrderAmount()) && ($address->getGrandTotal() > $partial_payment_amount));
			}
			else {
				$condition = (($partial_payment_amount > 0) && ($deposit_amount > 0) && ($address->getGrandTotal() > $partial_payment_amount));
			}

			if($condition)	
			{
				$address->addTotal(array(
						'code'=>$this->getCode(),
						'strong' => true,
						'title'=>Mage::helper('deposit')->formatDeposit($deposit_amount),
						'value'=> $deposit_amount,
						'area' => 'footer'
				), 'grand_total');
			}
			else
			{
				$address->setDepositAmount('0');
				//$address->setBaseDepositAmount('0');
				$deposit_amount = $address->getDepositAmount();
			}
		}
		return $this;
	}
}