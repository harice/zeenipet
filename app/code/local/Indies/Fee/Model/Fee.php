<?php

class Indies_Fee_Model_Fee extends Varien_Object {	
	public static $wholeCartFlag = 0;
	public static $subTotal = 0;
	public static $surchargeAmt = 0;
	public static $transactionId = "";

	public static function getFee()
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$calculationHelper = Mage::helper('partialpayment/calculation');

		$quote = $calculationHelper->getQuote();
		self::$subTotal = $calculationHelper->getSubtotal($quote);

		$fee = 0;

		if($partialpaymentHelper->isEnabledWithSurcharge()) {
			$fee = $calculationHelper->getSurchargeCalculation($quote, self::$subTotal);
		}
		else {
			$fee = $calculationHelper->getCalculationWithoutSurcharge($quote, self::$subTotal);
		}
		return $fee;
	}
}