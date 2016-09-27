<?php
class Indies_Outstockdiscount_Model_Sales_Quote_Address_Total_Outstockdiscount extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	protected $_code = 'outofstockdiscount';

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$items = $this->_getAddressItems($address);

		if (!count($items)) {
			return $this;
		}

		if($calculationHelper->canApply()) {
			$quote = $address->getQuote();
			$outstock_discount_amount = $calculationHelper->getTotalOutOfStockDiscount($quote);

			$address->setOutstockDiscountAmount($outstock_discount_amount);
			//$address->setBaseOutstockDiscountAmount($calculationHelper->convertCurrencyAmount($outstock_discount_amount));

			if(!$partialpaymentHelper->isEnabledWithSurcharge()){
				$address->setDepositAmount($address->getDepositAmount() - $address->getOutstockDiscountAmount());
				//$address->setBaseDepositAmount($calculationHelper->convertCurrencyAmount($address->getBaseDepositAmount() - $address->getBaseOutstockDiscountAmount()));
			}

			if($partialpaymentHelper->isEnabledWithSurcharge()){
				if($partialpaymentHelper->isApplyToWholeCart()){
				$address->setDepositAmount($address->getDepositAmount() - $address->getOutstockDiscountAmount());
				//$address->setBaseDepositAmount($calculationHelper->convertCurrencyAmount($address->getBaseDepositAmount() - $address->getBaseOutstockDiscountAmount()));
				}
			}
			
			
			if(($address->getOutstockDiscountAmount() > 0) && ($address->getSubtotal() > $address->getOutstockDiscountAmount()) && (($address->getFeeAmount() > 0)))
			{
				$address->setGrandTotal($address->getGrandTotal() - $address->getOutstockDiscountAmount());
				//$address->setBaseGrandTotal($calculationHelper->convertCurrencyAmount($address->getGrandTotal() - $address->getOutstockDiscountAmount()));
			}
			else
			{
				$address->setOutstockDiscountAmount(0);
				//$address->setBaseOutstockDiscountAmount(0);
			}
		}
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$partial_payment_amount = $address->getFeeAmount();
		
		$outstock_discount_amount = $address->getOutstockDiscountAmount();
		
		if (($partial_payment_amount) && ($outstock_discount_amount)) 
		{
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
			if(($outstock_discount_amount > 0) && ($partial_payment_amount > 0) && ($address->getSubtotal() >= $partialpaymentHelper->getMinimumOrderAmount()) && ($address->getGrandTotal() > $partial_payment_amount))
			{
				$address->addTotal(array(
					'code'=>$this->getCode(),
					'title'=>Mage::helper('outstockdiscount')->formatOutstockDiscount($outstock_discount_amount),
					'value'=> -$outstock_discount_amount,
				));
			}
			else
			{
				$address->setOutofstockDiscountAmount(0);
				//$address->setBaseOutofstockDiscountAmount(0);
				$outstock_discount_amount = $address->getOutofstockDiscountAmount();
			}
		}
		
		return $this;
	}
}