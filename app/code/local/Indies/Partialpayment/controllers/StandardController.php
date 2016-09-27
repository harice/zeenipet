<?php
require_once Mage::getModuleDir('controllers', 'Mage_Paypal').DS.'StandardController.php';

class Indies_Partialpayment_StandardController extends Mage_Paypal_StandardController
{   
    public function cancelAction()
    {
		$installment_id = Mage::app()->getRequest()->getParam('installment_id');
		$partial_payment_id =  Mage::app()->getRequest()->getParam('partial_payment_id');
		if ($installment_id == '')
	    {
			$session = Mage::getSingleton('checkout/session');
			$session->setQuoteId($session->getPaypalStandardQuoteId(true));
			if ($session->getLastRealOrderId()) {
				$order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
				if ($order->getId()) {
					$order->cancel()->save();
				}
				
				$partial_payment = Mage::getModel('partialpayment/partialpayment')->getCollection();
				$partial_payment->addFieldToFilter('order_id',$session->getLastRealOrderId());
				$size = $partial_payment->getSize();
							
				foreach ($partial_payment as $item)
				  {
					 $remainingAmount = $item->getTotalAmount();
					 $remaningInstallment = $item->getTotalInstallment();
					  
					  $partial_payment_save = Mage::getModel('partialpayment/partialpayment')
											->setPartialPaymentStatus('Canceled')
											->setPaidAmount(0)
											->setPaidInstallment(0)
											->setRemainingAmount($remainingAmount)
											->setRemainingInstallment($remaningInstallment)										
											->setPartialPaymentId($item->getPartialPaymentId())
											->save();
				 }
			}
			$this->_redirect('checkout/cart');
	  }
	  else
	  {
		$partial_payment = Mage::getModel('partialpayment/partialpayment')->getCollection()
							->addFieldToFilter('partial_payment_id',$partial_payment_id) ; 

		$arrInstallment=array();
			if(sizeof($partial_payment)) 
			{
				foreach ($partial_payment as $order) 
				{
					$orderid = $order->getOrderId();
				}
			}

		 $this->_redirect('partialpayment/index/installments',array('order_id'=>$orderid,'partial_payment_id'=>$partial_payment_id),array('_secure'=>true));
	  }
    }

	public function successAction()
    {
		$installment_id = Mage::app()->getRequest()->getParam('installment_id');
		$partial_payment_id =  Mage::app()->getRequest()->getParam('partial_payment_id');
	   if ($installment_id == '')
	   {
		$session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPaypalStandardQuoteId(true));	
				
		$partial_payment = Mage::getModel('partialpayment/partialpayment')->getCollection();
		$partial_payment->addFieldToFilter('order_id',$session->getLastRealOrderId());
		
		foreach ($partial_payment as $item)
		{
			$totalAmount = $item->getTotalAmount();
			$totalInstallment = $item->getTotalInstallment();
			
			$installmentData = Mage::getModel('partialpayment/installment')->getCollection()->addFieldToFilter('partial_payment_id', $item->getPartialPaymentId())->addFieldToFilter('installment_status', 'Paid')->getData();
			
			$amount = 0;
			
			if (isset($installmentData[0]['installment_id'])) {
			$installmentModel = Mage::getModel('partialpayment/installment')->load($installmentData[0]['installment_id']);
			$amount = $installmentModel->getInstallmentAmount();
			
			$partial_payment_save = Mage::getModel('partialpayment/partialpayment')
									->setPartialPaymentStatus('Processing')
									->setPaidAmount($amount)
									->setPaidInstallment(1)
									->setRemainingAmount($totalAmount - $amount)
									->setRemainingInstallment($totalInstallment - 1)										
									->setPartialPaymentId($item->getPartialPaymentId())
									->save();
			
			}
		}			    
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }
	else     // for all next installment
	{
		$instalmentamt = $_REQUEST['mc_gross'];
		$order_id_str = $_REQUEST['invoice'];
		$order_id_pos = strpos($order_id_str, '-');
		$order_id = substr($order_id_str, 0, $order_id_pos);
		$paidInstallment = 1;

		$partial_payment = Mage::getModel('partialpayment/partialpayment')->getCollection();
		$partial_payment->addFieldToFilter('order_id', $order_id);
		$partial_payment->getData();		

 		foreach ($partial_payment as $partial)
		{
			if(($partial->getTotalAmount() > $partial->getPaidAmount()) && ($partial->getRemainingAmount() > 0) && (round($partial->getRemainingAmount(),2) >= round($instalmentamt,2)))
			{
				$partial->setPaidAmount($partial->getPaidAmount() + $instalmentamt );
				$partial->setRemainingAmount($partial->getRemainingAmount() - $instalmentamt);
						
				$partial->setUpdatedDate(date('Y-m-d'));
				$partial->setPaidInstallment($partial->getPaidInstallment() + 1);
				$partial->setRemainingInstallment($partial->getRemainingInstallment() - 1);
				$partial->save();

				//edited by indies on 2-1-2013 start
				if($partial->getTotalAmount() == $partial->getPaidAmount())
				{
					$partial->setRemainingAmount(0);
					$partial->setPartialPaymentStatus('Complete');
					$partial->setPaidAmount($partial->getTotalAmount());
					$partial->save();
				}
			}
			$installment = Mage::getModel('partialpayment/installment')->getCollection();
			$installment->addFieldToFilter('installment_id',$installment_id);
			$installment->getData();
			$paidInstallment = $partial->getPaidInstallment();				
			foreach ($installment as $m)
			{
				$m->setInstallmentPaidDate(date('Y-m-d'));
				$m->setInstallmentStatus('Paid');
				$m->setPaymentMethod('paypal_standard');
				$m->save();
			}
		}

		Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
        $this->_redirect('partialpayment/index/installments',array('order_id'=>$order_id,'partial_payment_id'=>$partial_payment_id),array('_secure'=>true));

		if($paidInstallment == 1)
		{
			Mage::getSingleton('core/session')->addSuccess($paidInstallment.'st installment of order # ' . $order_id. ' has been done successfully.');
		}
		elseif($paidInstallment == 2)
		{
			Mage::getSingleton('core/session')->addSuccess($paidInstallment.'nd installment of order # ' . $order_id. ' has been done successfully.');
		}
		elseif($paidInstallment == 3)
		{
			Mage::getSingleton('core/session')->addSuccess($paidInstallment.'rd installment of order # ' . $order_id . ' has been done successfully.');
		}
		else
		{
			Mage::getSingleton('core/session')->addSuccess($paidInstallment.'th installment of order # ' . $order_id . ' has been done successfully.');
		}
	}
  }
}
