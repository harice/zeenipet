<?php

abstract class Indies_Partialsubscription_Model_Payment_Method_Abstract extends Varien_Object implements Indies_Partialsubscription_Model_Payment_Method_Interface
{

    /**
     * Returns order id for transfered order
     * @param mixed $Order
     * @return
     */
    public function getOrderId($Order)
    {
        if (is_int($Order)) {
            return $Order;
        } else {
            return $Order->getId();
        }
    }

    /**
     * Returns order object for transfered order
     * @param mixed $Order
     * @return Mage_Sales_Model_Order
     */
    public function getOrder($Order)
    {
        if (is_int($Order)) {
            return Mage::getModel('sales/order')->load($Order);
        } else {
            return $Order;
        }
    }

    /**
     * This function is run when subscription is created and new order creates
     * @param Indies_Partialsubscription_Model_Subscription $Subscription
     * @param Mage_Sales_Model_Order     $Order
     * @param Mage_Sales_Model_Quote     $Quote
     * @return Indies_Partialsubscription_Model_Payment_Method_Abstract
     */
    public function onSubscriptionCreate(Indies_Partialsubscription_Model_Subscription $Subscription, Mage_Sales_Model_Order $Order, Mage_Sales_Model_Quote $Quote)
    {
        return $this;
    }

    /**
     * This function is run when subscription is created and new order creates
     * @param Indies_Partialsubscription_Model_Subscription $Subscription
     * @param Mage_Sales_Model_Order     $Order
     * @param Mage_Sales_Model_Quote     $Quote
     * @return Indies_Partialsubscription_Model_Payment_Method_Abstract
     */
    public function onSubscriptionCancel(Indies_Partialsubscription_Model_Subscription $Subscription)
    {
        return $this;
    }


    /**
     * Cancels subscription at paypal
     * @param Indies_Partialsubscription_Model_Subscription $Subscription
     * @return Indies_Partialsubscription_Model_Payment_Method_Abstract
     */
    public function onSubscriptionSuspend(Indies_Partialsubscription_Model_Subscription $Subscription)
    {
        return $this;
    }

    /**
     * Cancels subscription at paypal
     * @param Indies_Partialsubscription_Model_Subscription $Subscription
     * @return Indies_Partialsubscription_Model_Payment_Method_Abstract
     */
    public function onSubscriptionReactivate(Indies_Partialsubscription_Model_Subscription $Subscription)
    {
        return $this;
    }


    /**
     * Checks if payment method can perform transaction now
     * @return bool
     */
    public function isValidForTransaction(Indies_Partialsubscription_Model_Sequence $Sequence)
    {
        return true;
    }

}