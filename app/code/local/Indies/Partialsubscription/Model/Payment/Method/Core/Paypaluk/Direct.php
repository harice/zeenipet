<?php

class Indies_Partialsubscription_Model_Payment_Method_Core_Paypaluk_Direct extends Mage_PaypalUk_Model_Direct
{
    /** This event is dispatched after authorize on checkout */
    const EVENT_NAME_AUTH_AFTER = "sarp_paypaluk_checkout_authorize_after";
    /** This event is dispatched after capture on checkout */
    const EVENT_NAME_CAPTURE_AFTER = "sarp_paypaluk_checkout_capture_after";

    /**
     * Validate payment method information object
     *
     * @param   Mage_Payment_Model_Info $info
     * @return  Mage_Payment_Model_Abstract
     */
    public function validate()
    {
        if (Indies_Partialsubscription_Model_Subscription::isIterating()) {
            return $this;
        } else {
            $result = parent::validate();
            return $result;
        }
    }

    public function capture(Varien_Object $payment, $amount)
    {
        if (Indies_Partialsubscription_Model_Subscription::isIterating()) {
            $Subscription = Indies_Partialsubscription_Model_Subscription::getInstance()->processPayment($payment->getOrder());
            return $this;
        }
        $result = parent::capture($payment, $amount);

        $verify_result = Mage::getModel('sarp/payment_method_paypaluk_direct')->getPnref($payment, $amount, $payment->getOrder()->getBaseCurrencyCode());
        Mage::dispatchEvent(self::EVENT_NAME_CAPTURE_AFTER, array('verification' => $verify_result));

        return $result;
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        if (Indies_Partialsubscription_Model_Subscription::isIterating()) {
            $Subscription = Indies_Partialsubscription_Model_Subscription::getInstance()->processPayment($payment->getOrder());
            return $this;
        }
        $result = parent::authorize($payment, $amount);

        $verify_result = Mage::getModel('sarp/payment_method_paypaluk_direct')->getPnref($payment, $amount, $payment->getOrder()->getBaseCurrencyCode());
        Mage::dispatchEvent(self::EVENT_NAME_AUTH_AFTER, array('verification' => $verify_result));

        return $result;
    }

}