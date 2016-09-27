<?php
class Indies_Fee_Model_Observer{

	public function invoiceSaveAfter(Varien_Event_Observer $observer)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$invoice = $observer->getEvent()->getInvoice();
		if ($invoice->getBaseFeeAmount()) {
			$order = $invoice->getOrder();
			$order->setFeeAmountInvoiced($order->getFeeAmountInvoiced() + $invoice->getFeeAmount());
			//$order->setBaseFeeAmountInvoiced($calculationHelper->convertCurrencyAmount($order->getBaseFeeAmountInvoiced() + $invoice->getBaseFeeAmount()));
		}
		return $this;
	}
	public function creditmemoSaveAfter(Varien_Event_Observer $observer)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		/* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
		$creditmemo = $observer->getEvent()->getCreditmemo();
		if ($creditmemo->getFeeAmount()) {
			$order = $creditmemo->getOrder();
			$order->setFeeAmountRefunded($order->getFeeAmountRefunded() + $creditmemo->getFeeAmount());
			//$order->setBaseFeeAmountRefunded($calculationHelper->convertCurrencyAmount($order->getBaseFeeAmountRefunded() + $creditmemo->getBaseFeeAmount()));
		}
		return $this;
	}
	public function updatePaypalTotal($evt){
		$cart = $evt->getPaypalCart();
		$cart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL,$cart->getSalesEntity()->getFeeAmount());
	}
}