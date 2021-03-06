<?php


abstract class Indies_Partialsubscription_Model_Web_Service_Client_Nvp_Abstract extends Indies_Partialsubscription_Model_Web_Service_Client
{

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Initializes NVP service
     * @return int
     */
    protected function _initService()
    {
        $this->setService(new Varien_Object);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $this->getData('service')->setHandler($ch);
    }

    /**
     * Initializes NVP service or returns existing
     * @return Varien_Object
     */
    public function getService($function = null)
    {
        if (!$this->getData('service')) {
            $this->_initService();
        }
        return $this->getData('service');
    }

    /**
     * Return URI of API
     * @return string
     */
    public function getURI()
    {
        return '';
    }

    /**
     * Run before requesting
     * @return Indies_Partialsubscription_Model_Web_Service_Client_Nvp_Abstract
     */
    protected function _beforeRunRequest()
    {
        return $this;
    }

    /**
     * Run after requesting
     * @return Varien_Object
     */
    protected function _afterRunRequest(Varien_Object $Response)
    {
        return $Response;
    }

    /**
     * Convert response string to object
     * @param string $response
     * @return Varien_Object
     */
    protected function _parseResponseString($httpResponse)
    {
        $httpResponseAr = @explode("&", $httpResponse);
        $httpParsedResponseAr = array();
        if (is_array($httpResponseAr)) {
            foreach ($httpResponseAr as $i => $value) {
                $tmpAr = explode("=", $value);
                if (sizeof($tmpAr) > 1) {
                    $httpParsedResponseAr[strtolower($tmpAr[0])] = urldecode($tmpAr[1]);
                } else {
                    $httpParsedResponseAr[strtolower($tmpAr[0])] = true;
                }
            }
        } else {
            return false;
        }
        return new Varien_Object($httpParsedResponseAr);
    }


    /**
     * Run request and return result
     * @return Varien_Object
     */
    public function runRequest()
    {

        $this->_beforeRunRequest();
        $requestString = $this->getRequest()->encodeRawPost();

        $ch = $this->getService()->getHandler();
        curl_setopt($ch, CURLOPT_URL, $this->getURI());
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);
        // Get response from server
        $httpResponse = curl_exec($ch);
        if (!$httpResponse) {
            throw new Indies_Partialsubscription_Exception("NVP Request failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')', print_r($this->getRequest()->getData(), 1));
        }
        $Response = $this->_parseResponseString($httpResponse);
        $Data = $this->_afterRunRequest($Response);
        return $Data;
    }

}