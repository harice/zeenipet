<?php
class Indies_Outstockdiscount_Model_Observer{

	public function invoiceSaveAfter(Varien_Event_Observer $observer)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$invoice = $observer->getEvent()->getInvoice();
		if ($invoice->getBaseOutstockDiscountAmount()) {
			$order = $invoice->getOrder();
			$order->setOutstockDiscountAmountInvoiced($order->getOutstockDiscountAmountInvoiced() + $invoice->getOutstockDiscountAmount());
			//$order->setBaseOutstockDiscountAmountInvoiced($calculationHelper->convertCurrencyAmount($order->getBaseOutstockDiscountAmountInvoiced() + $invoice->getBaseOutstockDiscountAmount()));
		}
		return $this;
	}
	public function creditmemoSaveAfter(Varien_Event_Observer $observer)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		/* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
		$creditmemo = $observer->getEvent()->getCreditmemo();
		if ($creditmemo->getOutstockDiscountAmount()) {
			$order = $creditmemo->getOrder();
			$order->setOutstockDiscountAmountRefunded($order->getOutstockDiscountAmountRefunded() + $creditmemo->getOutstockDiscountAmount());
			//$order->setBaseOutstockDiscountAmountRefunded($calculationHelper->convertCurrencyAmount($order->getBaseOutstockDiscountAmountRefunded() + $creditmemo->getBaseOutstockDiscountAmount()));
		}
		return $this;
	}


	public function notifyForOutOfStockRecovery()
	{
		 $partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		 if($partialpaymentHelper->canSendStockAvailabilityEmail())
		 {
			 $storeId = Mage::app()->getStore()->getId();
		 
		 $collection = Mage::getModel('partialpayment/product')->getCollection()
			->addFieldToFilter('is_out_of_stock',1)
			->addFieldToFilter('notified_for_stock_recovery',0)
			->addFieldToFilter('total_installment',array('neq' => '0' ));
		foreach ($collection as $collectData)
		{
		  if($collectData->getTotalInstallment() != $collectData->getPaidInstallment())
		  {
			try
			{ 
				$collectData->setNotifiedDate(date('Y-m-d'));
				$collectData->setIsOutOfStock(0);
				$collectData->setNotifiedForStockRecovery(1);
				$collectData->save();
				
				$due_date = '' ;
				$getDueDate =	Mage::getModel('partialpayment/installment')->getCollection()
								->addFieldToFilter('partial_payment_id',$collectData->getPartialPaymentId())
								->addFieldToFilter('installment_status','Remaining')
								->getData() ;				
				$due_date = $getDueDate[0]['installment_due_date'] ;
				$installment = Mage::getModel('partialpayment/partialpayment')->getCollection()
							->addFieldToFilter('order_id',array('eq'=>$collectData->getOrderId()))
							->getData();
				
				$partialpaymentId = $collectData->getPartialPaymentId() ;
				$orderId = $collectData->getOrderId();
				$customerName = $installment[0]['customer_first_name'] .' '. $installment[0]['customer_last_name'] ;
				$customerEmail = $installment[0]['customer_email'] ;
				$paid_amount = $installment[0]['paid_amount'] ;
				$remaining_amount = $installment[0]['remaining_amount'];
				$total_amount = $installment[0]['total_amount'];
				$paid_installment = $installment[0]['paid_installment'];
				$remaining_installment = $installment[0]['remaining_installment'];
				$total_installment = $installment[0]['total_installment'] ;
				
				// Mail Data
				$data = array();
				$data['frontend_label'] = $partialpaymentHelper->getFrontendLabel();
				$data['customer_name'] = $customerName;
				$data['product_name'] = $collectData['product_name'] ;
				$data['due_date'] = $due_date ;
				$data['order_id'] = $orderId;
				$data['paid_amount'] = $paid_amount;
				$data['remaining_amount'] = $remaining_amount;
				$data['total_amount'] = $total_amount ;
				$data['paid_installment'] = $paid_installment ;
				$data['remaining_installment'] = $remaining_installment ;
				$data['total_installment'] = $total_installment ;
				$data['login_url'] = Mage::getUrl('partialpayment/index/installments',array('order_id'=> $orderId, 'partial_payment_id'=>$collectData->getPartialPaymentId()));
				
				$translate = Mage::getSingleton('core/translate');
				$translate->setTranslateInline(false);
				$mailTemplate = Mage::getModel('core/email_template');
				
				$emailInfo = Mage::getModel('core/email_info');
				$emailInfo->addTo($customerEmail, $customerName);
				
				$sender =  $partialpaymentHelper->getStockAvailabilitySender();
				$template = $partialpaymentHelper->getStockAvailabilityEmailTemplate();
														   
				$mailTemplate->setDesignConfig(array('area' => 'frontend'))
				->sendTransactional(
					$template,
					$sender,
					$emailInfo->getToEmails(),
					$emailInfo->getToNames(),
					$data,
					$storeId
				 );
				
				$translate->setTranslateInline(true);
				if (!$mailTemplate->getSentSuccess()) {
					throw new Exception();
				}
				$translate->setTranslateInline(true);
			}
			catch(Exception $e)
			{
				Mage::log("Exception" . $e);
				return;
			}		
		}
	 }		
   }
  }
}