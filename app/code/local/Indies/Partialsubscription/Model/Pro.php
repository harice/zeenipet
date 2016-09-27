<?php
class Indies_Partialsubscription_Model_Pro extends Mage_Paypal_Model_Pro
{
    public function capture(Varien_Object $payment, $amount,$inst='')
    {
		if ($inst == '')
		{
			 $authTransactionId = $this->_getParentTransactionId($payment);

      		  if (!$authTransactionId)
			  {
          		  return false;
       		  }
		}
		else
		{
			$authTransactionId  = $payment->getAuthorizationId();	
			
       		 $api = $this->getApi()
            ->setAuthorizationId($authTransactionId)
            ->setIsCaptureComplete($payment->getShouldCloseParentTransaction())
            ->setAmount($amount)
            ->setCurrencyCode($payment->getOrder()->getBaseCurrencyCode())
            ->setInvNum($payment->getOrder()->getIncrementId().'-'.$inst);
            // TODO: pass 'NOTE' to API

			$api->callDoCapture();
						
			$this->_importCaptureResultToPayment($api, $payment);
			$newTranId = $payment['transaction_id'];
		
			$this->successInstallment($inst,$newTranId,$amount);
		}
	}
	 public function getApi()
     {
        if (null === $this->_api) {	
            $this->_api = Mage::getModel($this->_apiType);
        }
	
		parent::setMethod('paypal_direct');
        $this->_api->setConfigObject($this->_config);
   
	   return $this->_api;
    }
	public function successInstallment($id,$tranId,$amount)
	{
		if(isset($tranId))
		{
			$installment = Mage::getModel('partialpayment/installment')->getCollection();
			$installment->addFieldToFilter('installment_id',$id);
			$installment->getData();
			$partial_id = '' ;		
				
			foreach ($installment as $m)
			{
					$m->setInstallmentPaidDate(date('Y-m-d'));
					$m->setInstallmentStatus('Paid');
					$m->setPaymentMethod('paypal_direct');
					$m->setTxnId($tranId);
					$m->save();
					$partial_id = $m->getPartialPaymentId();	
			}
		
		$parialModel =  Mage::getModel('partialpayment/partialpayment')->getCollection();
		$parialModel->addFieldToFilter('partial_payment_id', $partial_id);
		$parialModel->getData();
			
			foreach($parialModel as $part)
			{
				$ins = $part->getPaidInstallment();
				$remaning = $part->getRemainningInstallment();
				
				
				$part->setPaidAmount($part->getPaidAmount() + $amount );
				$part->setRemainingAmount($part->getRemainingAmount() - $amount);
						
				$part->setUpdatedDate(date('Y-m-d'));
				$part->setPaidInstallment($part->getPaidInstallment() + 1);
				$part->setRemainingInstallment($part->getRemainingInstallment() - 1);
				$part->save();
						
						//edited by indies on 2-1-2013 start
						if($part->getTotalInstallment() == $part->getPaidInstallment())
						{
							
							$part->setRemainingAmount(0);
							$part->setPartialPaymentStatus('Complete');
							$part->setPaidAmount($part->getTotalAmount());
							$part->save();
						}
			}	
			return;				
		}
	}
    
}
 ?>