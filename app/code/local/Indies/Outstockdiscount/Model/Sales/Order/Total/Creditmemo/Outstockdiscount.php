<?php
class Indies_Outstockdiscount_Model_Sales_Order_Total_Creditmemo_Outstockdiscount extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$order = $creditmemo->getOrder();
		$outstockdiscountAmountLeft = $order->getOutstockDiscountAmountInvoiced() - $order->getOutstockDiscountAmountRefunded();
		$baseOutstockDiscountAmountLeft = $order->getBaseOutstockDiscountAmountInvoiced() - $order->getBaseOutstockDiscountAmountRefunded();
		if ($baseOutstockDiscountAmountLeft > 0) {
			//$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $outstockdiscountAmountLeft);
			//$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseOutstockDiscountAmountLeft);
			$creditmemo->setOutstockDiscountAmount($outstockdiscountAmountLeft);
			//$creditmemo->setBaseOutstockDiscountAmount($calculationHelper->convertCurrencyAmount($baseOutstockDiscountAmountLeft));
		}
		return $this;
	}
}
