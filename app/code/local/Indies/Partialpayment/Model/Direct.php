<?php
class Indies_Partialpayment_Model_Direct extends Mage_Paypal_Model_Direct
{ 
	protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $fee_amount = Indies_Fee_Model_Fee::getFee();
		$amt = $amount  ;

		if ($fee_amount > 0) {
			$deposit = Indies_Deposit_Model_Sales_Quote_Address_Total_Deposit::$deposit;
			$deposit_amount = round($deposit, 2);
			$amt = number_format($deposit_amount, 2, '.', '');	
		}

		$order = $payment->getOrder();
		if($_REQUEST['installment_id'] == '')
		{
			$api = $this->_pro->getApi()
				->setPaymentAction($this->_pro->getConfig()->paymentAction)
				->setIpAddress(Mage::app()->getRequest()->getClientIp(false))
				->setAmount($amt)
				->setCurrencyCode($order->getBaseCurrencyCode())
				->setInvNum($order->getIncrementId().'-'.'1'.'-'.date("dmyHis"))    // //$order->getIncrementId()
				->setEmail($order->getCustomerEmail())
				->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
				->setCreditCardType($payment->getCcType())
				->setCreditCardNumber($payment->getCcNumber())
				->setCreditCardExpirationDate(
					$this->_getFormattedCcExpirationDate($payment->getCcExpMonth(), $payment->getCcExpYear())
				)
				->setCreditCardCvv2($payment->getCcCid())
				->setMaestroSoloIssueNumber($payment->getCcSsIssue())
			;
		}else     //when pay for remainning installments
		{
			$partial_payment = Mage::getModel('partialpayment/partialpayment')->getCollection();
			$partial_payment->addFieldToFilter('partial_payment_id', $_REQUEST['partial_payment_id']);
			$partial_payment->getData();
			
				foreach ($partial_payment as $partial)
				{
					$newOrderId =$partial->getPaidInstallment() + 1;
				}
				
				 $api = $this->_pro->getApi()
				->setPaymentAction($this->_pro->getConfig()->paymentAction)
				->setIpAddress(Mage::app()->getRequest()->getClientIp(false))
				->setAmount($amt)
				->setCurrencyCode($order->getBaseCurrencyCode())
				->setInvNum($order->getIncrementId().'-'.$newOrderId.'-' .date("dmyHis"))    // //$order->getIncrementId()
				->setEmail($order->getCustomerEmail())
				->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
				->setCreditCardType($payment->getCcType())
				->setCreditCardNumber($payment->getCcNumber())
				->setCreditCardExpirationDate(
					$this->_getFormattedCcExpirationDate($payment->getCcExpMonth(), $payment->getCcExpYear())
				)
				->setCreditCardCvv2($payment->getCcCid())
				->setMaestroSoloIssueNumber($payment->getCcSsIssue())
			;
		
		}

        if ($payment->getCcSsStartMonth() && $payment->getCcSsStartYear()) {
            $year = sprintf('%02d', substr($payment->getCcSsStartYear(), -2, 2));
            $api->setMaestroSoloIssueDate(
                $this->_getFormattedCcExpirationDate($payment->getCcSsStartMonth(), $year)
            );
        }
        if ($this->getIsCentinelValidationEnabled()) {
            $this->getCentinelValidator()->exportCmpiData($api);
        }

        // add shipping and billing addresses
        if ($order->getIsVirtual()) {
            $api->setAddress($order->getBillingAddress())->setSuppressShipping(true);
        } else {
            $api->setAddress($order->getShippingAddress());
            $api->setBillingAddress($order->getBillingAddress());
        }

        // add line items
        $api->setPaypalCart(Mage::getModel('paypal/cart', array($order)))
            ->setIsLineItemsEnabled($this->_pro->getConfig()->lineItemsEnabled)
        ;

        // call api and import transaction and other payment information
        $api->callDoDirectPayment();
        $this->_importResultToPayment($api, $payment);

        try {
            $api->callGetTransactionDetails();
        } catch (Mage_Core_Exception $e) {
            // if we recieve errors, but DoDirectPayment response is Success, then set Pending status for transaction
            $payment->setIsTransactionPending(true);
        }
        $this->_importResultToPayment($api, $payment);
        return $this;
   } 
}