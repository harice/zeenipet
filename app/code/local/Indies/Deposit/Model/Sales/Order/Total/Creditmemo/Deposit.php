<?php
class Indies_Deposit_Model_Sales_Order_Total_Creditmemo_Deposit extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$order = $creditmemo->getOrder();
		$depositAmountLeft = $order->getDepositAmountInvoiced() - $order->getDepositAmountRefunded();
		$baseDepositAmountLeft = $order->getBaseDepositAmountInvoiced() - $order->getBaseDepositAmountRefunded();
		if ($baseDepositAmountLeft > 0) {
			//$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $depositAmountLeft);
			//$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseDepositAmountLeft);
			$creditmemo->setDepositAmount($depositAmountLeft);
			//$creditmemo->setBaseDepositAmount($calculationHelper->convertCurrencyAmount($baseDepositAmountLeft));
		}
		return $this;
	}
}
