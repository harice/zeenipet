<?php
class Indies_Credit_Model_Sales_Order_Observer
{
    public function isCreditLimitExceeded($observer)
    {
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		$checkCreditLimit = (bool)$observer->getEvent()->getOrder()->getFeeAmount();   //if fee amt is pass or not 1-pass 
		$currentOrderFeeAmount = $observer->getEvent()->getOrder()->getFeeAmount();    //used to fetch fee amount
		$defaultCreditAmount = Mage::getStoreConfig('partialpayment/credit_group/default_credit_amount');  //defaultcredit

		if($currentOrderFeeAmount > 0)
		{
			$customer_id = $observer->getEvent()->getOrder()->getCustomerId();

			if ($customer_id)
			{
				// Customer
				$credit_limit= 0;
				$customer = Mage::getModel('customer/customer')->load($customer_id);
				$credit_amount = $customer->getCreditAmount();  //get particular customer's credit_amt

				if ($credit_amount >= 0  && $credit_amount != '')
				{
					 $credit_limit = $credit_amount;
				}
				elseif ($defaultCreditAmount >= 0 && $defaultCreditAmount != '')
				{
					$credit_limit = $defaultCreditAmount;
				}
				else {
					return true;
				}

				$collection = Mage::getModel('sales/order')->getCollection()
						  ->addFieldToFilter('customer_id',$customer->getId())
						  ->addFieldToFilter('status',array('nin' => array('canceled','closed')));

				$arrInstallment = array();

				if(sizeof($collection)) 
				{
					foreach ($collection as $order) 
					{
					 	$arrInstallment[] = $order->getIncrementId();
					}
				}

				if(sizeof($arrInstallment) == 0)
				{
					$arrInstallment[0] = 0;
				}

				$collection = Mage::getModel('partialpayment/partialpayment')->getCollection()
						->addFieldToFilter('customer_id', $customer_id)
						->setOrder('partial_payment_id','DESC')
						->addFieldToFilter('order_id',array('in' => $arrInstallment));			  

				$orders_total = 0;

				foreach($collection as $order)
				{
					$orders_total += $order->getRemainingAmount();
				}

				if($credit_limit > $orders_total)
				{
					return true ;
				}
				else
				{
					Mage::throwException($partialpaymentHelper->getCreditLimitExceededMessage());
				}
			}
			else
			{
				// Guest
				if($defaultCreditAmount != '')
				{ 
					if($currentOrderFeeAmount > $defaultCreditAmount)
					{
						Mage::throwException($partialpaymentHelper->getCreditLimitExceededMessage());
					}
				}
				else {
					return true;
				}
			}
		}
		else 
		{
			return true ;
		}
	}
}