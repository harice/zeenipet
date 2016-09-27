<?php
class Indies_Partialsubscription_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function savePaymentInfoInSession($quote)
    {
        try
        {
            if (!Indies_Partialsubscription_Model_Subscription::isIterating()) {
                $quote = $observer->getEvent()->getQuote();
                if (!$quote->getPaymentsCollection()->count())
                    return;
                $Payment = $quote->getPayment();
                if ($Payment && $Payment->getMethod()) {
                    if ($Payment->getMethodInstance() instanceof Mage_Payment_Model_Method_Cc) {
                        // Credit Card number
                        if ($Payment->getMethodInstance()->getInfoInstance() && ($ccNumber = $Payment->getMethodInstance()->getInfoInstance()->getCcNumber())) {
                            $ccCid = $Payment->getMethodInstance()->getInfoInstance()->getCcCid();
                            $ccType = $Payment->getMethodInstance()->getInfoInstance()->getCcType();
                            $ccExpMonth = $Payment->getMethodInstance()->getInfoInstance()->getCcExpMonth();
                            $ccExpYear = $Payment->getMethodInstance()->getInfoInstance()->getCcExpYear();
                            Mage::getSingleton('customer/session')->setSarpCcNumber($ccNumber);
                            Mage::getSingleton('customer/session')->setSarpCcCid($ccCid);
                        }
                    }
                }
            }
        } catch (Exception $e)
        {
            //throw($e);
        }
    }
	public static function isSubscriptionType($typeId)
    {
        if ($typeId instanceof Mage_Catalog_Model_Product) {
            $typeId = $typeId->getTypeId();
        } elseif (($typeId instanceof Mage_Sales_Model_Order_Item) || ($typeId instanceof Mage_Sales_Model_Quote_Item)) {
            $typeId = $typeId->getProductType();
        }
        //return strpos($typeId, "subscription") !== false;
		return true;
    }

}