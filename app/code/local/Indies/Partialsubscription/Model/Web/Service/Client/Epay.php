<?php
ini_set('memory_limit', '128M');

class Indies_Partialsubscription_Model_Web_Service_Client_Epay extends Indies_Partialsubscription_Model_Web_Service_Client
{

    const WSDL_SUBSCRIPTION_PATH = 'https://ssl.ditonlinebetalingssystem.dk/remote/subscription.asmx?WSDL';
    const WSDL_PAYMENT_PATH = 'https://ssl.ditonlinebetalingssystem.dk/remote/payment.asmx?WSDL';

    const LANG_EN = 2;
    const LANG_DK = 1;


    public function ololo()
    {
        $subscription = Mage::getModel('partialsubscription/subscription')->load(8);
        /*foreach($subscription as $s){

          }
          */


        $subscription->payForDate(Mage::app()->getLocale()->date('2009-11-06', 'Y-MM-dd'));

    }

    /**
     * Authorizes subscription. If false throws exception why
     * @throws Mage_Core_Exception
     * @return StdClass
     */
    public function authorizeSubscription()
    {
        $this->setWsdl(self::WSDL_SUBSCRIPTION_PATH);

        $this->getRequest()
                ->setMerchantnumber((int)$this->getMerchantNumber())
                ->setInstantcapture((int)$this->getIsInstantCapture())
                ->setFraud(0)
                ->setTransactionid(0)
                ->setPbsresponse(0)
                ->setEpayresponse(0);

        $result = $this->getService()->authorize($this->getRequest()->getData());
        $this->getResponse()->setData($result);

        if (!$result->authorizeResult) {
            $err_decription = $this->getEpayError($result->epayresponse);
            throw new Indies_Partialsubscription_Exception("ePay error [{$result->epayresponse}]", "{$err_decription->epayResponseString}");
        }


        return $result;
    }

    /**
     * Returns ePay error by code
     * @param signed $code
     * @return StdClass
     */
    public function getEpayError($code)
    {
        $request = array(
            'merchantnumber' => $this->getMerchantNumber(),
            'language' => self::LANG_EN,
            'epayresponse' => '',
            'epayresponsecode' => $code
        );
        return ($this->getService()->getEpayError($request));
    }


}
