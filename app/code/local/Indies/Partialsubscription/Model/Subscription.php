<?php

class Indies_Partialsubscription_Model_Subscription extends Mage_Core_Model_Abstract
{
	const INTERNAL_DATE_FORMAT = 'yyyy-MM-dd'; // DON'T use Y(uppercase here)
    const DB_DATE_FORMAT = 'yyyy-MM-dd'; // DON'T use Y(uppercase here)
    const DB_DATETIME_FORMAT = 'yyyy-MM-dd H:m:s'; // DON'T use Y(uppercase here)

    const ITERATE_STATUS_REGISTRY_NAME = 'IND_SARP_PAYMENT_STATUS';
    const ITERATE_STATUS_RUNNING = 2;
    const ITERATE_STATUS_FINISHED = 12;

	public function processPayment($Order)
    {
        try {
			$storeId = $Order->getStoreId();
			$this->setOrder($Order);
            $pm_code = $Order->getPayment()->getMethod();
            $PaymentInstance = $this->_getMethodInstance($pm_code);			
			$subscription = new Varien_Object();
			$subscription->setStoreId($storeId);
			$service = $PaymentInstance->getWebService();
			$service->setSubscription($subscription);
            $PaymentInstance
                    ->processOrder($this->getOrder(), $Order);
        } catch (Exception $e) {
            throw new Mage_Core_Exception("Payment message: #{$this->getId()}: {$e->getMessage()}");
        }     
	    return $this;
    }
	
	public function getMethodInstance($method)
    {
        return $this->_getMethodInstance($method);
    }
	
	protected function _getMethodInstance($method = null)
    {		
        if (!$method && $this->getOrder()) {
            try {
                $method = $this->getOrder()->getPayment()->getMethod();
            } catch (Exception $e) {
                Mage::log("Can't find payment for subscription #{$this->getId()}. Order missing?");
            }
        }
        if ($model = Mage::getModel('partialsubscription/payment_method_' . $method)) {
            return $model->setSubscription($this);
        } else {
            throw new Mage_Core_Exception(Mage::helper('partialsubscription')->__("Can't find implementation of payment method $method"));
        }
    }
	public static function isIterating()
    {
        return Mage::registry(self::ITERATE_STATUS_REGISTRY_NAME) == self::ITERATE_STATUS_RUNNING;
    }
}