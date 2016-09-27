<?php

class Indies_Partialpayment_Model_Standard extends Mage_Paypal_Model_Standard
{
	protected $_config = null;
	private function _getAggregatedCartSummary()
    {
        if ($this->_config->lineItemsSummary) {
            return $this->_config->lineItemsSummary;
        }
        return Mage::app()->getStore($this->getStore())->getFrontendName();
    }
	public function getStandardCheckoutFormFields()
    {
		
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $api = Mage::getModel('paypal/api_standard')->setConfigObject($this->getConfig());
        $api->setOrderId($orderIncrementId)
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
            ->setReturnUrl(Mage::getUrl('paypal/standard/success'))
            ->setCancelUrl(Mage::getUrl('paypal/standard/cancel'));

        // export address
        $isOrderVirtual = $order->getIsVirtual();
        $address = $isOrderVirtual ? $order->getBillingAddress() : $order->getShippingAddress();
        if ($isOrderVirtual) {
            $api->setNoShipping(true);
		}elseif ($address->getEmail()) {
		    $api->setAddress($address);
        }
		
		
		$api->setPaypalCart(Mage::getModel('paypal/cart', array($order)))
            ->setIsLineItemsEnabled($this->_config->lineItemsEnabled);
		
		if(!$this->_config->lineItemsEnabled){
			$api->setCartSummary($this->_getAggregatedCartSummary());
		}
		
		$result = $api->getStandardCheckoutRequest();
		
		
		// Statrt: Update Total when Order was placed with Partial Payment on Date: - 29/08/2012
		$incrementId = $order->getIncrementId();
		$partialpaymentOrder = Mage::getModel('partialpayment/partialpayment')->getCollection()
			->addFieldToFilter('order_id',$incrementId)
			->load();
		
		foreach($partialpaymentOrder as $partialpaymentModel)
		{
			
			if($order->getDepositAmount())
			{
				
				$amount = $order->getDepositAmount();
				
				if(Mage::getStoreConfig("payment/paypal_standard/line_items_enabled")==1){		
				
				$items = $order->getAllItems();
				$ii=0;
				for($i=1;$i<=Mage::getSingleton('core/session')->getPcart()+1;$i++)
				{
					if($result['item_name_'.$i] =='Shipping')
					{
						unset($result['item_number_'.$i]);
					    unset($result['item_name_'.$i]);
					    unset($result['quantity_'.$i]);
    					unset($result['amount_'.$i]);
						
					}
				}
				for($i=1;$i<=Mage::getSingleton('core/session')->getPcart();$i++)
				{
					if($i==1)
					{
						$result['item_number_'.$i] = '';
						$result['item_name_'.$i] = $order->getIncrementId();
						$result['quantity_'.$i] = '';
						$result['amount_'.$i] = number_format($amount, 2);
						$result['tax_cart']= 0 ;
						$result['tax']= 0 ;
						
					}
					else{
							$result['item_number_'.$i] = '';
							$result['item_name_'.$i] = '';
							$result['quantity_'.$i] = '';
							$result['amount_'.$i] = '';
					}
				  }
				}
				if(Mage::getStoreConfig("payment/paypal_standard/line_items_enabled") == 0) {
					if($result['cmd']=="_ext-enter")
					{
						$result['tax'] = 0;
						$result['amount'] = number_format($amount, 2);
    					$result['shipping'] = number_format($order->getShippingAmount(), 2);
					}
				}
				
			}
		}
		if($result['cmd']=="_ext-enter")
		{
				$result['tax'] = 0;
				$result['shipping'] = number_format ($order->getShippingAmount(),2);
		}
		
		return $result;
		
    }
}