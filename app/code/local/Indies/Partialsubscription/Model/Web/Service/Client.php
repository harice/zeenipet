<?php

abstract class Indies_Partialsubscription_Model_Web_Service_Client extends Varien_Object
{

    /**
     * Initializes request model for web client.
     * @return Indies_Partialsubscription_Model_Web_Service_Client_Request_Simple
     */
    public function getRequest()
    {
		
        if (!$this->getData('request')) {
            $this->setRequest(Mage::getModel('partialsubscription/web_service_client_request_simple'));
        }
		
        return $this->getData('request');
    }

    /**
     * Initializes response model for web client.
     * @return Indies_Partialsubscription_Model_Web_Service_Client_Response_Simple
     */
    public function getResponse()
    {
        if (!$this->getData('response')) {
            $this->setResponse(Mage::getModel('partialsubscription/web_service_client_response_simple'));
        }
        return $this->getData('response');
    }


    /**
     * Initializes SOAP service or returns existing
     * @return Zend_Soap_Client
     */
    public function getService($function = null)
    {
        if (!$this->getData('service')) {
            $this->setService(
                new Zend_Soap_Client($this->getWsdl(), $this->getServiceOptions())
            );
        }
		$uri = $this->getUri($function);		
        if ($uri = $this->getUri($function)) {
            $this->getData('service')->setLocation($uri);
        }
        return $this->getData('service');
    }

    /**
     * Re-initializes service
     * @return Zend_Soap_Client
     */
    public function resetService()
    {
        $this->setService(null);
        return $this->getService();
    }

    /**
     * Returns current SOAP service options or default options if not set
     * @return array
     */
    public function getServiceOptions()
    {
        if (!$this->getData('service_options')) {
            return $this->getDefaultServiceOptions();
        } else {
            return array_merge($this->getDefaultServiceOptions(), $this->getData('service_options'));
        }
    }

    /**
     * Sets one or more service options
     * @param string $arg
     * @param mixed $value [optional]
     * @return
     */
    public function setServiceOptions($arg, $value = null)
    {
        if (is_array($arg)) {
            $this->_data['service_options'] = array_merge($this->_data['service_options'], $arg);
        } else {
            $this->_data['service_options'][$arg] = $value;
        }
        return $this;
    }

    /**
     * Returns default SOAP service options
     * @return array
     */
    public function getDefaultServiceOptions()
    {
        return array(
            'compression' => SOAP_COMPRESSION_ACCEPT,
            'soap_version' => SOAP_1_2
        );
    }

    /**
     * Retrieve and return current customer
     * @return Varien_Object
     */
    public function getCustomer()
    {
        if (!$this->getData('customer')) {

            $Customer = new Varien_Object(array(
                                               'id' => $this->getQuote()->getCustomerId(),
                                               'name' => $this->getQuote()->getCustomerName(),
                                               'email' => $this->getQuote()->getCustomerEmail()
                                          ));
            $this->setData('customer', $Customer);
        }
        return $this->getData('customer');
    }

    /**
     * Retrieve current quote
     * @return <type>
     */
    public function getQuote()
    {
        if (!$this->getData('quote')) {
            $Quote = $this->getPayment()->getQuote();
            $this->setData('quote', $Quote);
        }
        return $this->getData('quote');
    }


}