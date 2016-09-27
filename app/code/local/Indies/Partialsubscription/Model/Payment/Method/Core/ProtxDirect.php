<?php

class Indies_Partialsubscription_Model_Payment_Method_Core_ProtxDirect extends B4Before_ProtxDirect_Model_ProtxDirect
{
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
            return parent::validate();
        }
    }

    public function capture(Varien_Object $payment, $amount)
    {
        if (Indies_Partialsubscription_Model_Subscription::isIterating()) {
            $Subscription = Indies_Partialsubscription_Model_Subscription::getInstance()->processPayment($payment->getOrder());
            return $this;
        }
        return parent::capture($payment, $amount);
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        if (Indies_Partialsubscription_Model_Subscription::isIterating()) {
            $Subscription = Indies_Partialsubscription_Model_Subscription::getInstance()->processPayment($payment->getOrder());
            return $this;
        }
        return parent::authorize($payment, $amount);
    }

    protected function _postRequest(Varien_Object $request, $callback3D = false)
    {
        Indies_Partialsubscription_Model_Payment_Method_ProtxDirect::$VendorTxCode = $request->getVendorTxCode();

        $result = parent::_postRequest($request, $callback3D);

        Indies_Partialsubscription_Model_Payment_Method_ProtxDirect::$VPSTxID = $result->getVPSTxID();
        Indies_Partialsubscription_Model_Payment_Method_ProtxDirect::$TxAuthNo = $result->getTxAuthNo();
        Indies_Partialsubscription_Model_Payment_Method_ProtxDirect::$SecurityKey = $result->getSecurityKey();

        return $result;
    }
}