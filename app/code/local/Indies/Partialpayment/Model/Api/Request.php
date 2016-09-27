<?php

class Indies_Partialpayment_Model_Api_Request extends Quadra_Atos_Model_Api_Request {

    public function doRequest($parameters = array()) {
	
		
       $command = $parameters['bin_request'];
	   $command .= ' pathfile=' . $this->getApiFiles()->getPathfileName($parameters['bank'], $parameters['merchant_id']);
	   
        $command .= ' language=' . $this->getLanguageCode();
        $command .= ' merchant_id=' . $parameters['merchant_id'];
        $command .= ' merchant_country=' . $this->getMerchantCountry();
		
		$val = $this->getGrandTotal();
		$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
		
		$fee_amount = $order->getDepositAmount();//Indies_Fee_Model_Fee::getFee();
		if ($fee_amount > 0) {
			$deposit_amount = round($fee_amount, 2);
			$val = number_format($deposit_amount, 2, '.', '');
			$val = $val * 100;
		}
        $command .= ' amount='. $val;
		// end
        $command .= ' currency_code=' . $this->getCurrencyCode();
        $command .= ' payment_means=' . $parameters['payment_means'];
        $command .= ' normal_return_url=' . $parameters['url']['normal'];
        $command .= ' cancel_return_url=' . $parameters['url']['cancel'];
        $command .= ' automatic_response_url=' . $parameters['url']['automatic'];
        $command .= ' customer_id=' . $this->getQuote()->getBillingAddress()->getId();

        if (!$customerEmail = $this->getQuote()->getBillingAddress()->getEmail()) {
            $customerEmail = $this->getQuote()->getData('customer_email');
        }

        $command .= ' customer_email=' . $customerEmail;
        $command .= ' customer_ip_address=' . $this->getIpAddress();
        $command .= ' order_id=' . $this->getOrderList();

        if (array_key_exists('templatefile', $parameters) && $parameters['templatefile'] != '') {
            $command .= ' templatefile=' . $parameters['templatefile'];
        }

        if (isset($parameters['command'])) {
            $command .= $parameters['command'];

            if (array_key_exists('capture', $parameters)) {
                if (array_key_exists('capture_mode', $parameters['capture']) && $parameters['capture']['capture_mode'] != '') {
                   $command .= ' capture_mode=' . $parameters['capture']['capture_mode'];
                    if (array_key_exists('capture_day', $parameters['capture']) && $parameters['capture']['capture_day'] != '') {
                        $command .= ' capture_day=' . $parameters['capture']['capture_day'];
                    }
                }
            }
        }
		
		$sips_result = shell_exec("$command &2>1");
		
        if (!empty($sips_result)) {
            $sips_values = explode('!', $sips_result);
			
            $sips = array(
                'code' => $sips_values[1],
                'error' => $sips_values[2],
                'message' => $sips_values[3],
                'command' => $command,
                'output' => $sips_result
            );

            if (!isset($sips['code'])) {
                Mage::throwException($sips_result);
            }

            if ($sips['code'] == '-1') {
                Mage::throwException($sips['error']);
            }

            return $sips;
        } else {
            if (file_exists($parameters['bin_request']) === false) {
                Mage::throwException(Mage::helper('atos')->__('Impossible to launch binary file - Path to binary file seem to be not correct (%s)<br />Command line : %s', $parameters['bin_request'], $command));
            }

            if (is_executable($parameters['bin_request']) === false) {
                $perms = substr(sprintf('%o', fileperms($parameters['bin_request'])), -4);
                Mage::throwException(Mage::helper('atos')->__('Impossible to execute binary file - Set correct chmod (current chmod %s)<br />Command line : %s', $perms, $command));
            }

            return false;
        }
    }

}
