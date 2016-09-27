<?php

class Indies_Partialsubscription_Model_Payment_Method_Paypaluk_Direct extends Indies_Partialsubscription_Model_Payment_Method_Paypal_Direct
{

    /** Web service model */
    const WEB_SERVICE_MODEL = 'sarp/web_service_client_paypaluk_nvp';

    /** PayPal UK setting to detect if authorize or capture */
    const XML_PATH_PPUK_PAYMENT_ACTION = 'payment/paypaluk_direct/fields/payment_action';

    /** PayPal UK setting to set order status */
    const XML_PATH_PPUK_ORDER_STATUS = 'payment/paypaluk_direct/order_status';

    /**
     * PNREF received from PayPal
     * @var string
     */
    protected static $_pnref;
    /**
     * AUTHCODE received from PayPal
     * @var string
     */
    protected static $_authcode;

    /**
     * Initializes web service instance
     * @return Indies_Partialsubscription_Model_Payment_Method_PaypalUk_Direct
     */
    protected function _initWebService()
    {
        $service = Mage::getModel(self::WEB_SERVICE_MODEL);
        $this->setWebService($service);
        return $this;
    }

    /**
     * This function is run when subscription is created and new order creates
     * @todo Add check for length of subscription period. If it is longer than 12 month, we have to fail subscription due to PayFlow reference transactions limitation
     * @param Indies_Partialsubscription_Model_Subscription $Subscription
     * @param Mage_Sales_Model_Order     $Order
     * @param Mage_Sales_Model_Quote     $Quote
     * @return Indies_Partialsubscription_Model_Payment_Method_PaypalUk_Direct
     */
    public function onSubscriptionCreate(Indies_Partialsubscription_Model_Subscription $Subscription, Mage_Sales_Model_Order $Order, Mage_Sales_Model_Quote $Quote)
    {
        // Get pnref and authcode and save that to payment
        if (!self::$_pnref) {
            throw new Indies_Partialsubscription_Exception("No PNRef set for subscription#%s", $Subscription->getId());
        }
        $Subscription
                ->setRealId(self::$_pnref)
                ->setRealPaymentId(self::$_authcode)
                ->save();
        return $this;
    }

    /**
     * Processes payment for specified order
     * @param Mage_Sales_Model_Order $Order
     * @return
     */
    public function processOrder(Mage_Sales_Model_Order $PrimaryOrder, Mage_Sales_Model_Order $Order = null)
    {
        $pnref = $this->getSubscription()->getRealId();
        $amt = $Order->getGrandTotal();

        $this->getWebService()
                ->getRequest()
                ->reset()
                ->setData(array(
                               'ORIGID' => $pnref,
                               'AMT' => floatval($amt)
                          ));

        if (strtolower(Mage::getStoreConfig(self::XML_PATH_PPUK_PAYMENT_ACTION) == 'sale')) {
            $result = $this->getWebService()->referenceCaptureAction();

        } else {
            $result = $this->getWebService()->referenceAuthAction();
        }

        if (($result->getResult() . '') == '0') {
            // Payment Succeded
        } else {
            throw new Mage_Core_Exception(Mage::helper('partialsubscription')->__("PayFlow " . Mage::getStoreConfig(self::XML_PATH_PPUK_PAYMENT_ACTION) . " failed:[%s] %s", $result->getResult(), $result->getRespmsg()));
        }

    }

    /**
     * On cancel
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
     * On suspend
     * @param Indies_Partialsubscription_Model_Subscription $Subscription
     * @return Indies_Partialsubscription_Model_Payment_Method_Abstract
     */
    public function onSubscriptionSuspend(Indies_Partialsubscription_Model_Subscription $Subscription)
    {
        return $this;
    }

    /**
     * On reactivatee
     * @param Indies_Partialsubscription_Model_Subscription $Subscription
     * @return Indies_Partialsubscription_Model_Payment_Method_Abstract
     */
    public function onSubscriptionReactivate(Indies_Partialsubscription_Model_Subscription $Subscription)
    {
        return $this;
    }


    /**
     * Return CC verification result
     * @param Varien_Object $Payment
     * @param int $AMT
     * @param string $CURR
     * @return Varien_Object
     */
    public function getPnref($Payment, $AMT, $CURR)
    {
        $ccNumber = Mage::getSingleton('customer/session')->getSarpCcNumber();

        $expirationDate = $this->_formatExpDate($Payment->getCcExpMonth(), $Payment->getCcExpYear());

        $ccCode = Mage::getSingleton('customer/session')->getSarpCcCid();

        $this->getWebService()
                ->getRequest()
                ->reset()
                ->setData(array(
                               'ACCT' => $ccNumber,
                               'AMT' => 0,
                               'CVV2' => $ccCode,
                               'EXPDATE' => $expirationDate,
                               'FIRSTNAME' => "",
                               'LASTNAME' => '',
                               'CURRENCY' => $CURR,
                               'COMMENT1' => Mage::helper('sarp')->__("Subscription_PNRef_obtaining"),
                               'COMMENT2' => Mage::helper('sarp')->__("Dont_capture_please")
                          ));
        $result = $this->getWebService()->verifyAccountAction();
        if (($result->getResult() . '') == '0') {
            // Verification succeeded
            return new Varien_Object(array(
                                          'pnref' => $result->getPnref(),
                                          'authcode' => $result->getAuthcode()
                                     ));
        } else {
            throw new Indies_Partialsubscription_Exception(Mage::helper('sarp')->__("PayFlow verification failed:[%s] %s", $result->getResult(), $result->getRespmsg()));
        }
    }

    /**
     * Format date
     * @param Zend_Date date
     * @return string
     */
    protected function _formatDate(Zend_Date $Date)
    {
        return date('mdY', $Date->getTimestamp());
    }

    /**
     * Returns how much occurences will be generated by paypal
     * @param Indies_Partialsubscription_Model_Subscription $Subscription
     * @return int
     */
    protected function _getTotalBillingCycles(Indies_Partialsubscription_Model_Subscription $Subscription)
    {
        if ($Subscription->isInfinite()) {
            return 0;
        } else {
            // Calculate how much subscription events are generated
            return parent::_getTotalBillingCycles($Subscription);
        }
    }

    /**
     * Format expire date to PayFlow format
     * @param int $month
     * @param int $year
     * @return string
     */
    protected function _formatExpDate($month, $year)
    {
        return sprintf("%02s", intval($month)) . substr($year, -2);
    }


    /**
     * Method to save data from PayPal to static variables
     * Is called as event listener
     * @param Varien_Object $event
     */
    public function saveVerificationData($event)
    {
        $V = $event->getVerification();
        self::$_authcode = $V->getAuthcode();
        self::$_pnref = $V->getPnref();
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
