<?php

class Indies_Deposit_Model_Observer{

	public function invoiceSaveAfter(Varien_Event_Observer $observer)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$invoice = $observer->getEvent()->getInvoice();
		if ($invoice->getBaseDepositAmount()) {
			$order = $invoice->getOrder();
			$order->setDepositAmountInvoiced($order->getDepositAmountInvoiced() + $invoice->getDepositAmount());
			//$order->setBaseDepositAmountInvoiced($calculationHelper->convertCurrencyAmount($order->getBaseDepositAmountInvoiced() + $invoice->getBaseDepositAmount()));
		}
		return $this;
	}


	public function creditmemoSaveAfter(Varien_Event_Observer $observer)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		/* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
		$creditmemo = $observer->getEvent()->getCreditmemo();
		if ($creditmemo->getDepositAmount()){
			$order = $creditmemo->getOrder();
			$order->setDepositAmountRefunded($order->getDepositAmountRefunded() + $creditmemo->getDepositAmount());
			//$order->setBaseDepositAmountRefunded($calculationHelper->convertCurrencyAmount($order->getBaseDepositAmountRefunded() + $creditmemo->getBaseDepositAmount()));
		}
		return $this;
	}


	public function updatePaypalTotal($evt){
		$cart = $evt->getPaypalCart();
		$cart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL,$cart->getSalesEntity()->getDepositAmount());
	}


	public function setCorrectTax ($observer)
    {
		$quote = $observer->getQuote();
         foreach ($quote->getAllAddresses() as $address) {
            $grandTotal = $address->getSubtotalInclTax() + $address->getDiscountAmount() +  $address->getShippingInclTax();
			$address->setGrandTotal($grandTotal);
			$address->setBaseGrandTotal($grandTotal);
		}
    }
}