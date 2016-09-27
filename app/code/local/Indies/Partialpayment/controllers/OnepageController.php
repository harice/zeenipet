<?php
/* file created for successfull order place & update by Indies Services on 31-12-2012*/
require_once 'Mage/Checkout/controllers/OnepageController.php';

class Indies_Partialpayment_OnepageController extends Mage_Checkout_OnepageController
{
	  public function successAction()
    {
		
        $session = $this->getOnepage()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }
		$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$partial_payment = Mage::getModel('partialpayment/partialpayment')->getCollection();
		$partial_payment->addFieldToFilter('order_id', $orderId );
		$size = $partial_payment->getSize();
		
		$id=0;
		foreach ($partial_payment as $item)
		  {
			  
				$id = $item->getPartialPaymentId();
		  }
		 
		if($size)
		{		
			
			$partial_payment_save= Mage::getModel('partialpayment/partialpayment')
			->setPartialPaymentStatus('Processing')
			->setPartialPaymentId($id)
			->save();
		}
		
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
		$payment = Mage::getModel('sales/order_payment_transaction')->getCollection()
							->addFieldToFilter('order_id', $order->getEntityId());
		$txnId = "";
		
		foreach ($payment as $item)
		{
			$txnId = $item->getTxnId();
			$installment = Mage::getModel('partialpayment/installment')->getCollection()	
							->addFieldToFilter('installment_status', 'Paid')
							->addFieldToFilter('partial_payment_id',$id);
							
			foreach ($installment as $item)
			{
					$installmentload = Mage::getModel('partialpayment/installment')->load($item->getInstallmentId());
					$installmentload->setTxnId($txnId);
					$installmentload->save();
			}
		}
		
        $session->clear();
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        $this->renderLayout();
    }
}