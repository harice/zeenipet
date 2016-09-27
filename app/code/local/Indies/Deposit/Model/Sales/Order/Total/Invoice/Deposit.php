<?php

class Indies_Deposit_Model_Sales_Order_Total_Invoice_Deposit extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{	
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$order = $invoice->getOrder();
		$depositAmountLeft = $order->getDepositAmount() - $order->getDepositAmountInvoiced();
		$baseDepositAmountLeft = $order->getBaseDepositAmount() - $order->getBaseDepositAmountInvoiced();

		$invoice->setDepositAmount($depositAmountLeft);
		//$invoice->setBaseDepositAmount($calculationHelper->convertCurrencyAmount($baseDepositAmountLeft));

		return $this;
	}
}
