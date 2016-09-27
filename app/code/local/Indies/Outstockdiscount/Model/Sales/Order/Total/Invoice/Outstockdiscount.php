<?php

class Indies_Outstockdiscount_Model_Sales_Order_Total_Invoice_Outstockdiscount extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{	
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$order = $invoice->getOrder();
		$outstockdiscountAmountLeft = $order->getOutstockDiscountAmount() - $order->getOutstockDiscountAmountInvoiced();
		$baseOutstockDiscountAmountLeft = $order->getBaseOutstockDiscountAmount() - $order->getBaseOutstockDiscountAmountInvoiced();

		$invoice->setOutstockDiscountAmount($outstockdiscountAmountLeft);
		//$invoice->setBaseOutstockDiscountAmount($calculationHelper->convertCurrencyAmount($baseOutstockDiscountAmountLeft));

		return $this;
	}
}
