<?php	
class Indies_Partialsubscription_Model_Observer extends Varien_Object
{
	
	
    const HASH_SEPARATOR = ":::";
	private $classNumber = 0;


    /**
     * Saves payment information in session
     * This workaround is used for correct functionality at Magento 1.4+
     * @param object $observer
     */
    public function savePaymentInfoInSession($observer)
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

    public function salesOrderItemSaveAfter($observer)
    {
        $orderItem = $observer->getEvent()->getItem();
        $product = Mage::getModel('catalog/product')
                ->setStoreId($orderItem->getOrder()->getStoreId())
                ->load($orderItem->getProductId());

        if (method_exists($product->getTypeInstance(), 'prepareOrderItemSave'))
            $product->getTypeInstance()->prepareOrderItemSave($product, $orderItem);
    }

    /**
     * Returns current customer
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->getData('customer')) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->setCustomer($customer);
        }
        return $this->getData('customer');
    }

    /**
     * Return sales item as object
     * @param Mage_Sales_Model_Order_Item $item
     * @return Varien_Object
     */
    protected function _getOrderItemOptionValues(Mage_Sales_Model_Order_Item $item)
    {		
        $buyRequest = $item->getProductOptionByCode('info_buyRequest');
        $obj = new Varien_Object;
        $obj->setData($buyRequest);
        return $obj;
    }

    /**
     * Checks if guest checkout is available
     * @param object $e
     * @return
     */
	 
    public function checkGuestChecoutAvail($e)
    {
		if(!Mage::helper('partialpayment/partialpayment')->isEnabled()){
			return;
		}
        $result = $e->getResult();
        $quote = $e->getQuote();
        $currentCustomerId = Mage::getSingleton("customer/session")->getCustomer()->getId();
        $avail = true;
        if (!Mage::helper('partialpayment/partialpayment')->isValidCustomer()) {			
			$avail = false;
        }
        $result->setIsAllowed($avail);
    }
    public function paymentIsAvailable($observer)
    {
        $method = $observer->getMethodInstance();
        $quote = $observer->getQuote();
        if (is_null($quote)) {
           return;
        }
        if (!$quote instanceof Mage_Sales_Model_Quote) {
            $observer->getResult()->isAvailable = false;
            return;
        }
        $haveSarpItems = false;
        foreach ($quote->getAllItems() as $item)
        {
            $sarpSubscriptionType = $item->getProduct()->getCustomOption('allow_partial_payment');
            if (Mage::helper('partialsubscription')->isSubscriptionType($item) && !is_null($sarpSubscriptionType)) {
                $haveSarpItems = true;
                break;
            }
        }
        if ($haveSarpItems && !Mage::getModel('partialsubscription/subscription')->hasMethodInstance($method->getCode()))
            $observer->getResult()->isAvailable = false;
    }

    public function onepageCheckoutSaveOrderBefore($observer)
    {
		
        $quote = $observer->getQuote();
        $order = $observer->getOrder();
        $havePPItems = false;
		
        foreach ($quote->getAllItems() as $item)
        {			
            $infoBuyRequest = $item->getOptionByCode('info_buyRequest');
			$buyRequest = new Varien_Object(unserialize($infoBuyRequest->getValue()));
			$allow_partial_payment = $buyRequest->getAllowPartialPayment();
			if ($allow_partial_payment) {
                $havePPItems = true;
                break;
            }
        }		
		if($havePPItems){	
            switch($order->getPayment()->getMethod()) {				
                case Indies_Partialsubscription_Model_Payment_Method_Authorizenet::PAYMENT_METHOD_CODE:
                    $paymentModel = Mage::getModel('partialsubscription/payment_method_authorizenet'); //hardcode because unable get p.method without subscription
                    $service = $paymentModel->getWebService();
                    $service->setPayment($quote->getPayment());
                    $subscription = new Varien_Object();
                    $subscription->setStoreId($order->getStoreId());
                    $service->setSubscription($subscription);
                    try{
                        $data = $service->createCIMAccount();
						
						$order->setCimRealId($data->profile->customerProfileId);
						$order->setCimRealPaymentId($data->profile->paymentProfiles->CustomerPaymentProfileMaskedType->customerPaymentProfileId);
						$order->save();
                    }
                    catch(Exception $e){
                        throw new Mage_Core_Exception($e->getMessage());
                    }
                break;
                default:
            }
        }
    }


	/** Task add email reminder facility start on Date :- 16-03-2013 by indies*/
	public function sendReminderEmailTemplate ($email,$name,$orderid,$paidamt,$remainningamt,$totalamt,$paidinst,$remininst,$totalinst,$amount,$date) {
		try {
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

			$autoinstallment = Mage::getStoreConfig('partialpayment/functionalities_group/allow_auto_capturing', Mage::app()->getStore());

			if($autoinstallment  == 1) {
				 $msg = 'Your card will be charged with this amount on ' . $date;  //autoinstallment is true
			}
			else {
				 $msg = ' Please pay your installment before that ';
			}

			// Mail Data
			$storeId = Mage::app()->getStore()->getId();
			$data = array();
			$data['frontend_label'] = $partialpaymentHelper->getFrontendLabel();
			$data['customer_name'] = $name;
			$data['order_id'] = $orderid;
			$data['paid_amount'] = Mage::helper('checkout')->formatPrice($paidamt);
			$data['remainning_amount'] = Mage::helper('checkout')->formatPrice($remainningamt);
			$data['total_amount'] = Mage::helper('checkout')->formatPrice($totalamt);
			$data['paid_installment'] = $paidinst;
			$data['remainning_installment'] = $remininst;
			$data['total_installment'] = $totalinst;
			$data['amount'] = Mage::helper('checkout')->formatPrice($amount);
			$data['date'] = $date;
			$data['msg'] = $msg ; 

			// Email Template 
			$translate = Mage::getSingleton('core/translate');
			$translate->setTranslateInline(false);
			$mailTemplate = Mage::getModel('core/email_template');
			$copyTo = $partialpaymentHelper->getReminderEmailCCTo();
			$copyMethod = 'bcc';

			$emailInfo = Mage::getModel('core/email_info');
			$emailInfo->addTo($email,$name);
			if ($copyTo && $copyMethod == 'bcc') {
				// Add bcc to customer email
				$emailInfo->addBcc($copyTo);
			}

			$sender = $partialpaymentHelper->getReminderSender();
			$template =  $partialpaymentHelper->getInstallmentReminderEmailTemplate();
			$mailTemplate->addBcc($emailInfo->getBccEmails());
			$mailTemplate->setDesignConfig(array('area' => 'frontend'))
				->sendTransactional(
					$template,
					$sender,
					$emailInfo->getToEmails(),
					$emailInfo->getToNames(),
					$data,
					$storeId
				 );
			$translate->setTranslateInline(true);
			if (!$mailTemplate->getSentSuccess()) {
				throw new Exception();
			}
			$translate->setTranslateInline(true);
			$this->_redirect('*/*/');
			return;
		}
		catch(Exception $e){
			Mage::log("Exception" . $e);
			$this->_redirect('*/*/');
			return;
		}
	}


	public function sendReminderEmail() {
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$date = Mage::app()->getLocale()->date(new Zend_Date);

		$Days = $partialpaymentHelper->getEmailReminderBeforeDays();
		$date->addDay($Days);

		$salesOrderCollection = Mage::getModel('sales/order')->getCollection()
									->addAttributeToSelect('increment_id')
									->addFieldToFilter('status', array('nin' => array('canceled','close')));
		$salesOrderCollection = $salesOrderCollection->getData();
		$salesOrderIds = array();
		for ($i=0;$i<sizeof($salesOrderCollection);$i++) {
			array_push($salesOrderIds, $salesOrderCollection[$i]['increment_id']);
		}

		$partialpaymentCollection = Mage::getModel('partialpayment/partialpayment')->getCollection()
										->addFieldToFilter('order_id', array('in' => $salesOrderIds));
		$partialpaymentCollection = $partialpaymentCollection->getData();

		$partialpaymentIds = array();
		for ($i=0;$i<sizeof($partialpaymentCollection);$i++) {
			array_push($partialpaymentIds, $partialpaymentCollection[$i]['partial_payment_id']);
		}

		$installmentModel = Mage::getModel('partialpayment/installment')->getCollection()
							->addFieldToFilter('partial_payment_id', array('in' => $partialpaymentIds))
							->addFieldToFilter('installment_due_date', $date->toString('yyyy-MM-dd'))
							->addFieldToFilter('installment_status', array('in' => array('Remaining' , 'Canceled')))
							->load();

		foreach($installmentModel as $installment)
		{
			$partialpaymentId = $installment->getPartialPaymentId();		
			$partialpaymentModel = Mage::getModel('partialpayment/partialpayment')->load($partialpaymentId);
			$customerEmail = $partialpaymentModel->getCustomerEmail();
			$customerName =	$partialpaymentModel->getCustomerFirstName() . " " . $partialpaymentModel->getCustomerLastName();
			$orderId = $partialpaymentModel->getOrderId();
			$paidamount = $partialpaymentModel->getPaidAmount();
			$remainningamount = $partialpaymentModel->getRemainingAmount();
			$totalamount = $partialpaymentModel->getTotalAmount();
			$paidinstallment = $partialpaymentModel->getPaidInstallment();
			$remainninginstallment = $partialpaymentModel->getRemainingInstallment();
			$totalinstallment = $partialpaymentModel->getTotalInstallment();
			
			$installmentDate = $installment->getInstallmentDueDate();
			$installmentAmount = $installment->getInstallmentAmount();
			$this->sendReminderEmailTemplate($customerEmail, $customerName, $orderId,$paidamount,$remainningamount,$totalamount,$paidinstallment,$remainninginstallment,$totalinstallment,$installmentAmount, $installmentDate);
		}
	}
	/** Task add email reminder facility end on Date :- 16-03-2013 by indies*/


	public function processInstallmentPayment()
	{	
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		if ($partialpaymentHelper->canSendInstallmentsReminderEmail()) {
			$this->sendReminderEmail();
		}

		if ($partialpaymentHelper->isCaptureInstallmentsAutomatically()) {
			$date = Mage::app()->getLocale()->date(new Zend_Date);
			$installmentModel = Mage::getModel('partialpayment/installment')->getCollection()
				->addFieldToFilter('installment_due_date', $date->toString('yyyy-MM-dd'))
				->addFieldToFilter('installment_status', array('in' => array('Remaining' , 'Canceled')))
				->load();

			foreach($installmentModel as $installment)
			{
				$partialpaymentId = $installment->getPartialPaymentId();	
				$partialpaymentModel = Mage::getModel('partialpayment/partialpayment')->load($partialpaymentId);
				$incrementId = $partialpaymentModel->getOrderId();

				$value =	Mage::getModel('partialpayment/installment')->getCollection()
							->addFieldToFilter('partial_payment_id',$installment->getPartialPaymentId())
							->load('installment_id')
							->getData();

				$order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);

				if($value[0]['payment_method'] == 'sagepaydirectpro' || $value[0]['payment_method'] == 'sagepayserver') //  check first payment is done by sagepay direct pro or sagepay server
				{
					$response = $this->repeatWithSagePay($incrementId, $order->getBillingAddressId(), $order->getShippingAddressId(), $installment->getInstallmentId(), $installment->getInstallmentAmount(), $value[0]['payment_method'], $value[0]['txn_id']);

					if ($response['VPSTxId']) {
						$installment->setInstallmentPaidDate($date->toString('yyyy-MM-dd'))
						->setInstallmentStatus("Paid")
						->setPaymentMethod($value[0]['payment_method'])
						->setTxnId($response['VPSTxId'])
						->save();

						if ($partialpaymentModel->getRemainingInstallment() == '1') {
							$partialpaymentModel->setPartialPaymentStatus('Complete');
						}

						$partialpaymentModel->setPaidAmount($partialpaymentModel->getPaidAmount() + $installment->getInstallmentAmount());
						$partialpaymentModel->setRemainingAmount($partialpaymentModel->getRemainingAmount() - $installment->getInstallmentAmount());
						$partialpaymentModel->setPaidInstallment($partialpaymentModel->getPaidInstallment() + 1);
						$partialpaymentModel->setRemainingInstallment($partialpaymentModel->getRemainingInstallment() - 1);
						$partialpaymentModel->setUpdatedDate($date->toString('yyyy-MM-dd'));
						$partialpaymentModel->save();
						$this->sendInstallmentConfirmationEmail($partialpaymentModel, $installment);
					}
				}
				elseif($value[0]['payment_method'] == 'paypal_direct') //  check first payment is done by paypal pro
				{
					$tranId = $value[0]['txn_id'] ;  
						$amount = $installment->getInstallmentAmount() ;  
						$installment_id =$installment->getInstallmentId(); 
						$count_total_inst =$partialpaymentModel->getTotalInstallment();
						$count_paid_inst = $partialpaymentModel->getPaidInstallment() + 1;
						$completeType = 0 ;
	
						if ($count_total_inst == $count_paid_inst)
						{
							$completeType =  1 ;   // Last installment
						}
	
						$order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
	
						$this->setOrder($order);
						$payment = $order->getPayment()
								   ->setAuthorizationId($tranId)
								   ->setShouldCloseParentTransaction($completeType)
								  ->setAmount($amount);
	
						$ProData  = Mage::getModel('partialsubscription/pro');
						$paymentData =$ProData->capture($payment,$amount,$installment_id);
						$this->sendInstallmentConfirmationEmail($partialpaymentModel, $installment);
				}
				elseif ($order->getCimRealId() && $order->getCimRealPaymentId()) {
					$paymentModel = Mage::getModel('partialsubscription/subscription');
					$paymentData = $paymentModel->processPayment($order);

					// Set Value to the Variables
					$customerName = $partialpaymentModel->getCustomerFirstName() . ' ' . $partialpaymentModel->getCustomerLastName();
					$storeId = Mage::app()->getStore()->getId();
					if($paymentData->getOrder()->getPayment()->getCcTransId())
					{
						$installment->setInstallmentPaidDate($date->toString('yyyy-MM-dd'))
						->setInstallmentStatus("Paid")
						->setPaymentMethod($paymentData->getOrder()->getPayment()->getMethod())
						->setTxnId($paymentData->getOrder()->getPayment()->getCcTransId())
						->save();

						if ($partialpaymentModel->getRemainingInstallment() == '1') {
							$partialpaymentModel->setPartialPaymentStatus('Complete');
						}

						$partialpaymentModel->setPaidAmount($partialpaymentModel->getPaidAmount() + $installment->getInstallmentAmount());
						$partialpaymentModel->setRemainingAmount($partialpaymentModel->getRemainingAmount() - $installment->getInstallmentAmount());
						$partialpaymentModel->setPaidInstallment($partialpaymentModel->getPaidInstallment() + 1);
						$partialpaymentModel->setRemainingInstallment($partialpaymentModel->getRemainingInstallment() - 1);
						$partialpaymentModel->setUpdatedDate($date->toString('yyyy-MM-dd'));
						$partialpaymentModel->save();
						$this->sendInstallmentConfirmationEmail($partialpaymentModel, $installment);
					}
				}
			}
		}
	}


	public function sendInstallmentConfirmationEmail ($partialpaymentModel, $installment)
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		// Set Value to the Variables
		$customerName = $partialpaymentModel->getCustomerFirstName() . ' ' . $partialpaymentModel->getCustomerLastName();
		$storeId = Mage::app()->getStore()->getId();

		// Mail Data
		$installment_amount = Mage::helper('checkout')->formatPrice($installment->getInstallmentAmount());
		$pid = $partialpaymentModel->getPartialpaymentId();
		$paidamount = Mage::helper('checkout')->formatPrice($partialpaymentModel->getPaidAmount());
		$remainamount = Mage::helper('checkout')->formatPrice($partialpaymentModel->getRemainingAmount());
		$totalamount = Mage::helper('checkout')->formatPrice($partialpaymentModel->getTotalAmount());
		$paidinstallment = $partialpaymentModel->getPaidInstallment();
		$remaininstallment = $partialpaymentModel->getRemainingInstallment();
		$totalinstallment = $partialpaymentModel->getTotalInstallment();

		$data = array();
		$data['frontend_label'] = $partialpaymentHelper->getFrontendLabel();

		$data['customer_name'] = $customerName;
		$data['order_id'] = $partialpaymentModel->getOrderId();
		$data['installment_amount'] = $installment_amount;
		$data['partialpayment_id'] = $pid;
		$data['paid_amount'] = $paidamount;
		$data['remainnnig_amount'] = $remainamount;
		$data['total_amount'] = $totalamount;
		$data['paid_installment'] = $paidinstallment;
		$data['remainning_installment'] = $remaininstallment;
		$data['total_installment'] = $totalinstallment;
		$data['login_url'] = Mage::getUrl('partialpayment/index/installments',array('order_id'=> $partialpaymentModel->getOrderId(), 'partial_payment_id'=>$pid));

		// Email Template 
		$translate = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);
		$mailTemplate = Mage::getModel('core/email_template');

		$copyTo = $partialpaymentHelper->getInstallmentConfirmationEmailCCTo();//Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
		$copyMethod = 'bcc';

		$emailInfo = Mage::getModel('core/email_info');
		$emailInfo->addTo($partialpaymentModel->getCustomerEmail(), $customerName);
		if ($copyTo && $copyMethod == 'bcc') {
			// Add bcc to customer email
			$emailInfo->addBcc($copyTo);
		}
		$sender = $partialpaymentHelper->getInstallmentConfirmationSender();
		$template = $partialpaymentHelper->getInstallmentConfirmationEmailTemplate();
		$mailTemplate->addBcc($emailInfo->getBccEmails());
		$mailTemplate->setDesignConfig(array('area' => 'frontend'))
			->sendTransactional(
				$template,
				$sender,
				$emailInfo->getToEmails(),
				$emailInfo->getToNames(),
				$data,
				$storeId
			 );

		$translate->setTranslateInline(true);

		if (!$mailTemplate->getSentSuccess()) {
			throw new Exception();
		}

		$translate->setTranslateInline(true);
	}


	public function repeatWithSagePay ($order_increment_id, $billing_address_id, $shipping_address_id, $installment_id, $installment_amount, $payment_method, $vps_tx_id) {
		try {
			//$order = Mage::getModel("sales/order")->loadByIncrementId($order_id);
			$billing_address = Mage::getModel('sales/order_address')->load($billing_address_id);
			$delivery_address = Mage::getModel('sales/order_address')->load($shipping_address_id);

			$sage_pay_direct_pro = new Ebizmarts_SagePaySuite_Model_SagePayDirectPro();

			$code = $payment_method;
			$mode = Mage::getStoreConfig('payment/' . $payment_method . '/mode', Mage::app()->getStore());
			$key = 'repeat';

			$sagepaysuite_model = new Ebizmarts_SagePaySuite_Model_Api_Payment();
			$url = $sagepaysuite_model->getUrl($key, false, $code, $mode);

			$amount = round($installment_amount, 2);

			$parent_transaction = Mage::getModel('sagepaysuite2/sagepaysuite_transaction')->loadByVpsTxId($vps_tx_id);

			$request = array();
			$request['VPSProtocol'] = $sage_pay_direct_pro->getVpsProtocolVersion($mode);
			$request['TxType'] = 'REPEAT';
			$request['Vendor'] = Mage::getStoreConfig('payment/sagepaysuite/vendor', Mage::app()->getStore());
			$request['VendorTxCode'] = substr($order_increment_id . '-' . date('Y-m-d-H-i-s'), 0, 40);
			$request['Amount'] = number_format($amount, 2, '.', '');
			$request['Currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
			$request['Description'] = 'Installment Id: ' . $installment_id;
			$request['RelatedVPSTxId'] = str_replace('{', '', str_replace('}', '', $vps_tx_id));
			$request['RelatedVendorTxCode'] = $parent_transaction->getVendorTxCode();
			$request['RelatedSecurityKey'] = $parent_transaction->getSecurityKey();
			$request['RelatedTxAuthNo'] = $parent_transaction->getTxAuthNo();
			$request['DeliverySurname'] = $delivery_address->getLastname();
			$request['DeliveryFirstnames'] = $delivery_address->getFirstname();
			$request['DeliveryAddress1'] = $delivery_address['street'];
			$request['DeliveryCity'] = $delivery_address->getCity();
			$request['DeliveryPostCode'] = $delivery_address->getPostcode();
			if($delivery_address->getCountryId() == "US")
			{
				$request['DeliveryState'] = strtoupper(substr($billing_address['region'], 0, 2));
			}
			$request['DeliveryCountry'] = $delivery_address->getCountryId();

			Mage::log('Request of Installment Id: ' . $installment_id);
			Mage::log($request);

			$rd = '';

			foreach ($request as $_key => $_val) {
				$rd .= $_key . '=' . urlencode(mb_convert_encoding($_val, 'ISO-8859-1', 'UTF-8')) . '&';
			}

			$_timeout = (int)$sagepaysuite_model->getConfigData('connection_timeout');
			$timeout = ($_timeout > 0 ? $_timeout : 90);

			$output = array();

			$curlSession = curl_init();

			curl_setopt($curlSession, CURLOPT_USERAGENT, Mage::helper('sagepaysuite/data')->getUserAgent());
			curl_setopt($curlSession, CURLOPT_URL, $url);
			curl_setopt($curlSession, CURLOPT_HEADER, 0);
			curl_setopt($curlSession, CURLOPT_POST, 1);
			curl_setopt($curlSession, CURLOPT_POSTFIELDS, $rd);
			curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curlSession, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

			$rawresponse = curl_exec($curlSession);

			//Split response into name=value pairs
			$response = explode(chr(10), $rawresponse);
			
			
			// Check that a connection was made
			if (curl_error($curlSession)) {
				
				$output['Status'] = 'FAIL';
				$output['StatusDetail'] = 'FAIL';//htmlentities(curl_error($curlSession)) . '. ' . $this->getConfigData('timeout_message');
				$output;
			}
			else {
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
			}
			Indies_Fee_Model_Fee::$transactionId = $output['VPSTxId'];
			Mage::log('Response of Installment Id: ' . $installment_id);
			Mage::log($output);
			return $output;
		} catch (Exception $e) {
            Mage::throwException(
 	           $this->__('Gateway request error: %s', $e->getMessage())
            );
		}
	}


	public function savePartialPayment($obsever) {		

	}
}