<?php

class Indies_Fee_Model_Sales_Order_Total_Invoice_Fee extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{	
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$order = $invoice->getOrder();
		$feeAmountLeft = $order->getFeeAmount() - $order->getFeeAmountInvoiced();
		$baseFeeAmountLeft = $order->getBaseFeeAmount() - $order->getBaseFeeAmountInvoiced();

		$invoice->setFeeAmount($feeAmountLeft);
		//$invoice->setBaseFeeAmount($calculationHelper->convertCurrencyAmount($baseFeeAmountLeft));

		return $this;
	}
}
