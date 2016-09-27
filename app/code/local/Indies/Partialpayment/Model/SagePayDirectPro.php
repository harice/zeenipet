<?php

/**
 * DIRECT main model
 *
 * @category   Ebizmarts
 * @package    Ebizmarts_SagePaySuite
 * @author     Ebizmarts <info@ebizmarts.com>
 */

class Indies_Partialpayment_Model_SagePayDirectPro extends Ebizmarts_SagePaySuite_Model_SagePayDirectPro
{
    
	 public function requestPost($url, $data, $returnRaw = false) {
	 	
        $storeId = $this->getStoreId();
        $aux = $data;

        if (isset($aux['CardNumber'])) {
            $aux['CardNumber'] = substr_replace($aux['CardNumber'], "XXXXXXXXXXXXX", 0, strlen($aux['CardNumber']) - 3);
        }
        if (isset($aux['CV2'])) {
            $aux['CV2'] = "XXX";
        }
        $rd = '';
		
		/*  Task: Make partial payment module compatible with sage pay - Start - Date: 19/01/2013 - By: Indies Services  */
		$fee_amount = Indies_Fee_Model_Fee::getFee();
		
		if ($fee_amount > 0) {
			$deposit = Indies_Deposit_Model_Sales_Quote_Address_Total_Deposit::$deposit;
			$deposit_amount = round($deposit, 2);
			$data['Amount'] = number_format($deposit_amount, 2, '.', '');
				
		}
/*  Task: Make partial payment module compatible with sage pay - End - Date: 19/01/2013 - By: Indies Services  */

        foreach ($data as $_key => $_val) {
            if ($_key == 'billing_address1')
                $_key = 'BillingAddress1';
				
            $rd .= $_key . '=' . urlencode(mb_convert_encoding($_val, 'ISO-8859-1', 'UTF-8')) . '&';			
			
        }
		
        self::log($url, null, 'SagePaySuite_REQUEST.log');
        self::log(Mage::helper('core/http')->getHttpUserAgent(false), null, 'SagePaySuite_REQUEST.log');
        self::log($aux, null, 'SagePaySuite_REQUEST.log');

        $_timeout = (int)$this->getConfigData('connection_timeout');
        $timeout = ($_timeout > 0 ? $_timeout : 90);

        $output = array();

        $curlSession = curl_init();

		curl_setopt($curlSession, CURLOPT_USERAGENT, $this->_sageHelper()->getUserAgent());
        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_HEADER, 0);
        curl_setopt($curlSession, CURLOPT_POST, 1);
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $rd);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlSession, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

        $rawresponse = curl_exec($curlSession);

		if(true === $returnRaw){
			return $rawresponse;
		}

        self::log($rawresponse, null, 'SagePaySuite_RawResponse.log');

        //Split response into name=value pairs
        $response = explode(chr(10), $rawresponse);

        // Check that a connection was made
        if (curl_error($curlSession)) {

            self::log(curl_error($curlSession), Zend_Log::ALERT, 'SagePaySuite_REQUEST.log');
            self::log(curl_error($curlSession), Zend_Log::ALERT, 'Connection_Errors.log');

            $output['Status'] = 'FAIL';
            $output['StatusDetail'] = htmlentities(curl_error($curlSession)) . '. ' . $this->getConfigData('timeout_message');

            return $output;
        }

        curl_close($curlSession);

        // Tokenise the response
        for ($i = 0; $i < count($response); $i++) {
            // Find position of first "=" character
            $splitAt = strpos($response[$i], "=");
            // Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
            $arVal = (string) trim(substr($response[$i], ($splitAt + 1)));
            if (!empty($arVal)) {
                $output[trim(substr($response[$i], 0, $splitAt))] = $arVal;
            }
        }

        self::log($output, null, 'SagePaySuite_REQUEST.log');

        return $output;
    }

}

