<?php


class Indies_Partialsubscription_Model_Payment_Method_Purchaseorder extends Indies_Partialsubscription_Model_Payment_Method_Abstract
{

    /**
     * Processes payment for specified order
     * @param Mage_Sales_Model_Order $Order
     * @return
     */
    public function processOrder(Mage_Sales_Model_Order $PrimaryOrder, Mage_Sales_Model_Order $Order = null)
    {
        // Set order as pending
        $Order->addStatusToHistory('pending', '', false)->save();
        // Throw exception to suspend subscription
    }

    /**
     * Returns service subscription service id for specified quote
     * @param mixed $quoteId
     * @return int
     */
    public function getSubscriptionId($OrderItem)
    {
        return 1;
    }
}