<?php

class Indies_Partialpayment_Helper_Partialpayment extends Mage_Core_Helper_Abstract
{
	/** Function Define on 21-03-2013 By- Indies Start*/

	/** General Settings Start */
	
	public function isWholecartDiscountCalculationOptionsDeductFirstFromInstallment()
	{ 
	
		if(Mage::getStoreConfig('partialpayment/functionalities_group/wholecart_discount_calculation_options', Mage::app()->getStore()) == '1') {
			return true;
		}
		return false;
	}
	public function isWholecartDiscountCalculationOptionsDistributeEquallyInInstallment()
	{ 
	
		if(Mage::getStoreConfig('partialpayment/functionalities_group/wholecart_discount_calculation_options', Mage::app()->getStore()) == '2') {
			return true;
		}
		return false;
	}
	
	
	public function isWholecartShippingTaxCalculationOptionsChargedInFirstInstallment()
	{ 
		if(Mage::getStoreConfig('partialpayment/functionalities_group/wholecart_tax_shipping_calculation_options', Mage::app()->getStore()) == '1') {
			return true;
		}
		return false;
	}
	
	public function isWholecartShippingTaxCalculationOptionsDistributeEquallyInInstallment()
	{ 
	
		if(Mage::getStoreConfig('partialpayment/functionalities_group/wholecart_shipping_and_tax_calculation_options', Mage::app()->getStore()) == '2') {
			return true;
		}
		return false;
	}
	public function isAllproductsDiscountFixedInstallmentsCalculationOptionsDeductFromFirstInstallment()
	{ 
	
		if(Mage::getStoreConfig('partialpayment/functionalities_group/allproducts_fixedinstallments_discount_calculation_options', Mage::app()->getStore()) == '1') {
			return true;
		}
		return false;
	}
	
	public function isAllproductsFixedInstallmentsDiscountCalculationOptionsDistributeEquallyInInstallment()
	{ 
	
		if(Mage::getStoreConfig('partialpayment/functionalities_group/allproducts_discount_calculation_options', Mage::app()->getStore()) == '2') {
			return true;
		}
		return false;
	}
	public function isAllproductsDiscount2InstallmentsCalculationOptionsDeductFromFirstInstallment()
	{ 
		if(Mage::getStoreConfig('partialpayment/functionalities_group/allproducts_2installments_discount_calculation_options', Mage::app()->getStore()) == '1') {
			return true;
		}
		return false;
	}
	
	public function isAllproducts2InstallmentsDiscountCalculationOptionsDistributeEquallyInInstallment()
	{ 
		if(Mage::getStoreConfig('partialpayment/functionalities_group/allproducts_discount_calculation_options', Mage::app()->getStore()) == '2') {
			return true;
		}
		return false;
	}
		
	public function isAllproducts2InstallmentShippingTaxCalculationOptionsDistributeEquallyInInstallment()
	{ 
	
		if(Mage::getStoreConfig('partialpayment/functionalities_group/allproducts_shipping_and_tax_calculation_options', Mage::app()->getStore()) == '2') {
			return true;
		}
		return false;
	}


	public function isAllproductsFixedInstallmentShippingTaxCalculationOptionsDistributeEquallyInInstallment()
	{
		if(Mage::getStoreConfig('partialpayment/functionalities_group/allproducts_shipping_and_tax_calculation_options', Mage::app()->getStore()) == '2') {
			return true;
		}
		return false;
	}


	public function getWholecartMessage()
	{ 
		return Mage::getStoreConfig('partialpayment/functionalities_group/text_for_wholecart', Mage::app()->getStore());
	}


	public function isCaptureInstallmentsAutomatically()
	{ 
		if(Mage::getStoreConfig('partialpayment/functionalities_group/allow_auto_capturing', Mage::app()->getStore()) == '1') {
			return true;
		}
		return false;
	}	
	/** General Settings End */


	public function getFrontendLabel()
	{ 
		return Mage::getStoreConfig('partialpayment/functionalities_group/brand_label', Mage::app()->getStore());
	}


	public function getPartialPaymentOption()
	{
		return Mage::getStoreConfig('partialpayment/functionalities_group/partial_payment_options', Mage::app()->getStore());
	}


	public function getTotalNoOfInstallment()
	{
		return 	Mage::getStoreConfig('partialpayment/functionalities_group/total_installments',Mage::app()->getStore());
	}


	public function getPartialPaymentStatus()
	{
		return Mage::getStoreConfig('partialpayment/license_status_group/status', Mage::app()->getStore());
	}


	public function isEnabled ()
	{
		if(Mage::getStoreConfig('partialpayment/license_status_group/status', Mage::app()->getStore()) == '1') {
			return true;
		}
		return false;
	}


	public function isPartialPaymentOption2Installments()
	{
		if (Mage::getStoreConfig('partialpayment/functionalities_group/partial_payment_options', Mage::app()->getStore())=='2_installments'){
			return true;
		}
		return false;
	}
	
	public function isPartialPaymentOptionFixedInstallments()
	{
		if (Mage::getStoreConfig('partialpayment/functionalities_group/partial_payment_options', Mage::app()->getStore())=='fixed_installments'){
			return true;
		}
		return false;
	}
	
	public function isPartialPaymentOptionFlexyPayments()
	{
		if (Mage::getStoreConfig('partialpayment/functionalities_group/partial_payment_options', Mage::app()->getStore())=='flexy_payments'){
			return true;
		}
		return false;
	}
	
	public function isApplyToAllProducts ()
	{
		if (Mage::getStoreConfig('partialpayment/functionalities_group/apply_partial_payment_to', Mage::app()->getStore()) == '1') {
			return true;
		}
		return false;
	}

	public function isApplyToSpecificProductsOnly ()
	{
		if (Mage::getStoreConfig('partialpayment/functionalities_group/apply_partial_payment_to', Mage::app()->getStore()) == '2') {
			return true;
		}
		return false;
	}

	public function isApplyToOutOfStockProducts ()
	{
		if (Mage::getStoreConfig('partialpayment/functionalities_group/apply_partial_payment_to', Mage::app()->getStore()) == '3') {
			return true;
		}
		return false;
	}

	public function isApplyToWholeCart()
	{
		if (Mage::getStoreConfig('partialpayment/functionalities_group/apply_partial_payment_to', Mage::app()->getStore()) == '4') {
			return true;
		}
		return false;
	}


	public function isPartialPaymentOptional()
	{
		if(Mage::getStoreConfig('partialpayment/functionalities_group/partial_payment_optional', Mage::app()->getStore())== '1'){
		return true;
		}
		return false;
	}


	public function getMinimumOrderAmount()
	{
		return (float)Mage::getStoreConfig('partialpayment/functionalities_group/minimum_order_amount', Mage::app()->getStore());
	}


	/** Out of stock discount settings Start */
	public function isPreOrderDiscountCalculationTypeFixedAmount()
	{
	if (Mage::getStoreConfig('partialpayment/outofstock_discount/outofstock_discount_calculation_type', Mage::app()->getStore()) == '1'){
			return true;
		}
		return false;
	}
		
	public function isPreOrderDiscountCalculationTypePercentage()
	{
	if (Mage::getStoreConfig('partialpayment/outofstock_discount/outofstock_discount_calculation_type', Mage::app()->getStore()) == '2'){
			return true;
		}
		return false;
	}
	
	public function getPreOrderDiscount()
	{
		return Mage::getStoreConfig('partialpayment/outofstock_discount/outofstock_discount_amount', Mage::app()->getStore());
	}
	
	/** Out of stock discount settings End */
	
	/** Partial Payment Calculation Settings Start */
	
	public function isInstallmentCalculationTypeFixed ()
	{
		if (Mage::getStoreConfig('partialpayment/calculation_settings/installment_calculation_type', Mage::app()->getStore()) == '1'){
			return true;
		}
		return false;
	}

	public function isInstallmentCalculationTypePercentage()
	{
		if (Mage::getStoreConfig('partialpayment/calculation_settings/installment_calculation_type', Mage::app()->getStore()) == '2'){
			return true;
		}
		return false;
	}
	
	public function getFirstInstallmentAmount()
	{
		return (float)Mage::getStoreConfig('partialpayment/calculation_settings/first_installment_amount', Mage::app()->getStore());
	}
	
	public function getPaymentPlan()
	{
		return 	Mage::getStoreConfig('partialpayment/calculation_settings/payment_plan', Mage::app()->getStore());
	}
	public function getPaymentPlanTotalNoOfDays()
	{
		return 	Mage::getStoreConfig('partialpayment/calculation_settings/total_no_days', Mage::app()->getStore());
	}
	public function isPaymentPlanDays()
	{
		if (Mage::getStoreConfig('partialpayment/calculation_settings/payment_plan', Mage::app()->getStore()) == 'days'){
			return true;
		}
		return false;
		
	}
	
	public function isPaymentPlanWeekly()
	{
		if (Mage::getStoreConfig('partialpayment/calculation_settings/payment_plan', Mage::app()->getStore()) == 'weekly'){
			return true;
		}
		return false;
		
	}
	
	public function isPaymentPlanMonthly()
	{
		if (Mage::getStoreConfig('partialpayment/calculation_settings/payment_plan', Mage::app()->getStore()) == 'monthly'){
			return true;
		}
		return false;
		
	}
	
	public function getSurchargeOptions()
	{
		
		return 	Mage::getStoreConfig('partialpayment/calculation_settings/surcharge_options', Mage::app()->getStore());
	}
	
	public function isSurchargeOptionsNoSurcharge()
	{
		if (Mage::getStoreConfig('partialpayment/calculation_settings/surcharge_options', Mage::app()->getStore()) == 'no_surcharge'){
			return true;
		}
		return false;
		
	}
	
	public function isSurchargeOptionsSingleSurcharge()
	{
		if (Mage::getStoreConfig('partialpayment/calculation_settings/surcharge_options', Mage::app()->getStore()) == 'single_surcharge'){
			return true;
		}
		return false;
	}
	
	public function isSurchargeOptionsMultipleSurcharge()
	{
		if (Mage::getStoreConfig('partialpayment/calculation_settings/surcharge_options', Mage::app()->getStore()) == 'multiple_surcharge'){
			return true;
		}
		return false;
	}
		
	public function getSingleSurchargeValue()
	{
		return 	Mage::getStoreConfig('partialpayment/calculation_settings/single_surcharge_value', Mage::app()->getStore());
	}
	
	public function getMultipleSurchargeValues()
	{
		return 	Mage::getStoreConfig('partialpayment/calculation_settings/multiple_surcharge_values', Mage::app()->getStore());
	}
	
	public function getTotalNoOfDays()
	{
		return 	Mage::getStoreConfig('partialpayment/without_surcharge/total_no_days', Mage::app()->getStore());
	}
	
	public function getMultipleSurchargeDaysInstallment()
	{
		return 	Mage::getStoreConfig('partialpayment/without_surcharge/days_per_installment', Mage::app()->getStore());
	}
	
	
	/** Partial Payment Calculation Settings End */
	
	/** Partial Payment Credit Options Start */
public function isGuest()
	{
		if(Mage::getSingleton('customer/session')->isLoggedIn()) 
		{
			return false;
		}
		else 
		{
			return true;
		}						
	}
	public function displayPartialPaymentMessage()
	{
		if ($this->isGuest() && $this->isCustomerCreditToSpecific())
		{
			return true; 
		}
		return false ;
	}
	public function getCustomerGroup()
	{
		$group = explode(',', Mage::getStoreConfig('partialpayment/credit_group/customer_groups'));
		
		return $group;
	}


	public function isValidCustomerGroup()
	{
		$postData = Mage::app()->getRequest()->getPost();

		if (isset($postData['customer_id']))
	    {
			if ($postData['customer_id']) 
			{
				Mage::getSingleton('core/session')->setAdminhtmlCustomerId($postData['customer_id']);
				$customer = Mage::getModel('customer/customer')->load($postData['customer_id']);
			}
			else 
			{
				return false;
			}
		}
		elseif (Mage::getSingleton('core/session')->getAdminhtmlCustomerId()) {
			$customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('core/session')->getAdminhtmlCustomerId());
		}
		else
		{
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}

		$customer_group = 0;
		$customer_groups = $this->getCustomerGroup() ;
		
		if ($customer->getId())
		{
			$customer_group = $customer->getGroupId();
		}

		if(in_array($customer_group, $customer_groups)) 
		{
			return true;
		}
		
		return false;
	}


	public function isValidCustomer ()
	{
		if ($this->isCustomerCreditToAll())
		{	  
			return true;
		}
		elseif ($this->isCustomerCreditToRegistered())
		{
			return true;
		}
		elseif ($this->isValidCustomerGroup())
		{	
			return true;
		}
		return false;
	}


	public function getDefaultCreditAmount()
	{
		return 	Mage::getStoreConfig('partialpayment/credit_groups/default_credit_amount', Mage::app()->getStore());
	}


	public function getCreditLimitExceededMessage()
	{
		return 	Mage::getStoreConfig('partialpayment/credit_group/exceeded_credit_limit_message', Mage::app()->getStore());
	}


	public function isCustomerCreditToAll()
	{
		if (Mage::getStoreConfig('partialpayment/credit_group/customer_credit_options', Mage::app()->getStore()) == '1'){
			return true;
		}
		return false;
	}


	public function isCustomerCreditToRegistered()
	{
		if (Mage::getStoreConfig('partialpayment/credit_group/customer_credit_options', Mage::app()->getStore()) == '2'){
			return true;
		}
		return false;
	}


	public function isCustomerCreditToSpecific()
	{
		if (Mage::getStoreConfig('partialpayment/credit_group/customer_credit_options', Mage::app()->getStore()) == '3'){
			return true;
		}
		return false;
	}	
	/** Partial Payment Credit Options End */


	/** Partial Payment Installment Reminder Email Start */
	public function canSendInstallmentsReminderEmail ()
	{
		if(Mage::getStoreConfig('partialpayment/installment_reminder_email/send_installments_reminder_email', Mage::app()->getStore()) == '1') {
			return true;
		}
		return false;
	}


	public function getEmailReminderBeforeDays()
	{
		return 	Mage::getStoreConfig('partialpayment/installment_reminder_email/remind_before_days', Mage::app()->getStore());
	}
	
	public function getInstallmentReminderEmailTemplate()
	{
		
		return 	Mage::getStoreConfig('partialpayment/installment_reminder_email/reminder_email_template', Mage::app()->getStore());
	}
	public function getReminderSender()
	{
	
	return 	Mage::getStoreConfig('partialpayment/installment_reminder_email/reminder_email_sender', Mage::app()->getStore());
	}
	public function getReminderEmailCCTo()
	{
		return 	Mage::getStoreConfig('partialpayment/installment_reminder_email/reminder_email_cc', Mage::app()->getStore());
	}
	
	
	/** Partial Payment Installment Reminder Email End */
	
	/** Partial Payment Order Confirmation Email Start */
	
	public function getOrderConfirmationTemplate()
	{
		return 	Mage::getStoreConfig('partialpayment/order_confirmation_email/order_confirmation_template', Mage::app()->getStore());
	}
	
	public function getOrderConfirmationSender()
	{
		$sender = Mage::getStoreConfig('partialpayment/order_confirmation_email/order_confirmation_sender', Mage::app()->getStore());
		
		return $sender;
	}
	
	public function getOrderConfirmationEmailCCTo()
	{
		return 	Mage::getStoreConfig('partialpayment/order_confirmation_email/order_confirmation_cc', Mage::app()->getStore());
	}
		
	/** Partial Payment Order Confirmation Email End */
	
	/** Partial Payment Installment Confirmation Email Start */
	
	public function getInstallmentConfirmationSender()
	{
		$sender = Mage::getStoreConfig('partialpayment/installment_confirmation_email/installment_confirmation_sender', Mage::app()->getStore());
		
		return $sender;
	}
	
	
	public function getInstallmentConfirmationEmailTemplate()
	{
		return 	Mage::getStoreConfig('partialpayment/installment_confirmation_email/installment_confirmation_template', Mage::app()->getStore());
	}
	
	public function getInstallmentConfirmationEmailCCTo()
	{
		
		return 	Mage::getStoreConfig('partialpayment/installment_confirmation_email/installment_confirmation_cc', Mage::app()->getStore());
	}
		
	/** Partial Payment Installment Confirmation Email End */
	
	/**  Function Define on 21-03-2013 By- Indies End*/
	


	public function isEnabledWithSurcharge()
	{
		if($this->isSurchargeOptionsSingleSurcharge() || $this->isSurchargeOptionsMultipleSurcharge())
			return true;
		
		return false;
	}


	public function allowMultipleInstallments()
	{
		return Mage::getStoreConfig('partialpayment/functionalities_group/allow_multiple_installments', Mage::app()->getStore());
	}


	public function totalInstallments()
	{
		return Mage::getStoreConfig('partialpayment/functionalities_group/Total_Installments', Mage::app()->getStore());
	}


	public function getAllowPartialPayment($product)
	{		
		$infoBuyRequest = $product->getOptionByCode('info_buyRequest');
		$buyRequest = new Varien_Object(unserialize($infoBuyRequest->getValue()));

		if($buyRequest['super_product_config']['product_type']=="grouped")
		{
			$buyRequest->setAllowPartialPayment(Mage::getSingleton('core/session')->getData($buyRequest['super_product_config']['product_id']));
		}

		$allow_partial_payment = $buyRequest->getAllowPartialPayment();

		return $allow_partial_payment;
	}


	public function getSurchargeInstallments($product)
	{
		$infoBuyRequest = $product->getOptionByCode('info_buyRequest');
		$buyRequest = new Varien_Object(unserialize($infoBuyRequest->getValue()));
		if($buyRequest['super_product_config']['product_type']=="grouped")
		{
			$buyRequest->setSurchargeInstallments(Mage::getSingleton('core/session')->getData($buyRequest['super_product_config']['product_id']));		
		}
		$allow_partial_payment = $buyRequest->getSurchargeInstallments();
		return $allow_partial_payment;
	}
	
	public function allowPartialPayment($product)
	{
		return $product->getPartialPayment();
	}


	public function isOutOfStockProduct($product_id)
	{
		$product = Mage::getModel('catalog/product')->load($product_id);
		if ($product->getStockItem()->getStockQty()) {
			return false;
		}
		return true;
	}


	public function isProductPriceValid ($product_price)
	{
		if ($this->isInstallmentCalculationTypeFixed()) {
			if ($product_price > $this->getFirstInstallmentAmount()) {
				return true;
			}
			return false;
		}
		return true;
	}


	public function validateWithMinimumOrderAmount ($subTotal)
	{
		if ($this->getMinimumOrderAmount() > 0) {
			if ($subTotal > $this->getMinimumOrderAmount()) {
				return true;
			}
			else {
				return false;
			}
		}
		return true;
	}


	public function isGroupedProduct ($product)
	{
		if ($product->getTypeId() == 'grouped') {
			return true;
		}
		return false;
	}


	public function isPartialPaymentAvailable ()
	{
		$feeAmount = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getFeeAmount();
		if ($feeAmount > 0) {
			return true;
		}
		return false;
	}


	public function getActivPaymentMethods($order_id)
    {
		$payment = Mage::helper('payment/data');
		$paymentMethods = $payment->getStoreMethods();

		$methods = array();

		foreach ($paymentMethods as $paymentMethod) {
			$paymentCode = $paymentMethod->getCode();
			if($paymentMethod->canUseCheckout() == 1):
/*  Task: Make partial payment module compatible with sage pay - Start - Date: 22/01/2013 - By: Indies Services  */
				if (strpos($paymentCode, 'sagepay') !== false) {
					echo '<script type="text/javascript">';
					echo 'var SuiteConfig = new EbizmartsSagePaySuite.Conf(' . Mage::helper('sagepaysuite/data')->getSagePayConfigJson() . ')';
                    echo '</script>';
				}
/*  Task: Make partial payment module compatible with sage pay - End - Date: 22/01/2013 - By: Indies Services  */
				$paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
				/*And followed by your code*/
				$methods[$paymentCode] = array(
					'label' => $paymentTitle,
					'value' => $paymentCode,
				);
			endif;
		}

		return $methods;
    }


/*  Task: Make partial payment module compatible with sage pay - Start - Date: 22/01/2013 - By: Indies Services  */
	public function payWithSagepayDirectPro($post, $order_id)
	{
		try {
			$order = Mage::getModel("sales/order")->loadByIncrementId($order_id);
			$billing_address = Mage::getModel('sales/order_address')->load($order->getBillingAddressId());
			$delivery_address = Mage::getModel('sales/order_address')->load($order->getShippingAddressId());

			$amount = round($post[$post['installment_id']], 2);

			$sage_pay_direct_pro = new Ebizmarts_SagePaySuite_Model_SagePayDirectPro();
			$sagepaysuite_model = new Ebizmarts_SagePaySuite_Model_Api_Payment();

			$code = $post['payment']['method'];
			$mode = Mage::getStoreConfig('payment/' . $post['payment']['method'] . '/mode', Mage::app()->getStore());
			$key = 'post';
			$url = $sagepaysuite_model->getUrl($key, false, $code, $mode);

			$request = array();
			$request['VPSProtocol'] = $sage_pay_direct_pro->getVpsProtocolVersion($mode);
			$request['TxType'] = 'PAYMENT';
			$request['Vendor'] = Mage::getStoreConfig('payment/sagepaysuite/vendor', Mage::app()->getStore());
			$request['VendorTxCode'] = substr($order_id . '-' . date('Y-m-d-H-i-s'), 0, 40);
			$request['Description'] = $post['payment']['cc_owner'];
			$request['Amount'] = number_format($amount, 2, '.', '');
			$request['Currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
			$request['BillingSurname'] = $billing_address->getLastname();
			$request['BillingFirstnames'] = $billing_address->getFirstname();
			$request['BillingPostCode'] = $billing_address->getPostcode();

			if($delivery_address->getCountryId() == "US")
			{
				$request['BillingState'] =  strtoupper(substr($billing_address['region'], 0, 2)); 
				$request['DeliveryState'] = strtoupper(substr($billing_address['region'], 0, 2)); 
			}

			$request['BillingAddress1'] = $billing_address['street'];
			$request['BillingCity'] = $billing_address->getCity();
			$request['BillingCountry'] = $billing_address->getCountryId();
			$request['DeliverySurname'] = $delivery_address->getLastname();
			$request['DeliveryFirstnames'] = $delivery_address->getFirstname();
			$request['DeliveryPostCode'] = $delivery_address->getPostcode();
			$request['DeliveryAddress1'] = $delivery_address['street'];
			$request['DeliveryCity'] = $delivery_address->getCity();
			$request['DeliveryCountry'] = $delivery_address->getCountryId();
			$request['CardNumber'] = $post['payment']['cc_number'];
			$request['ExpiryDate'] = sprintf('%02d%02d', $post['payment']['cc_exp_month'], substr($post['payment']['cc_exp_year'], strlen($post['payment']['cc_exp_year']) - 2));
			$request['CardType'] = $post['payment']['cc_type'];
			$request['CV2'] = $post['payment']['cc_cid'];
			$request['CardHolder'] = $post['payment']['cc_owner'];

			Mage::log('Request of Installment Id: ' . $post['installment_id']);
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
			Mage::log('Response of Installment Id: ' . $post['installment_id']);
			Mage::log($output);
			return $output;
		} catch (Exception $e) {
            Mage::throwException(
 	           $this->__('Gateway request error: %s', $e->getMessage())
            );
		}
	}


	public function getSagepayServerNotificationUrl ($post, $order_id)
	{
		return Mage::getUrl('partialpayment/index/sagepayservernotify', array('order_id' => $order_id, 'partial_payment_id' => $post['partial_payment_id'], 'installment_id' => $post['installment_id'], 'payment_method' => $post['payment']['method']));
	}


	public function getSagepayServerSuccessUrl ($post, $order_id)
	{
		return Mage::getUrl('partialpayment/index/sagepayserversuccess', array('order_id' => $order_id, 'partial_payment_id' => $post['partial_payment_id'], 'installment_id' => $post['installment_id'], 'payment_method' => $post['payment']['method']));
	}


	public function getSagepayServerRedirectUrl ($post, $order_id)
	{
		return Mage::getUrl('partialpayment/index/sagepayserverredirect', array('redirect_url' => $post['refer']));
	}


	public function getSagepayServerFailureUrl ($post, $order_id)
	{
		return Mage::getUrl('partialpayment/index/sagepayserverfailure', array('order_id' => $order_id, 'partial_payment_id' => $post['partial_payment_id'], 'installment_id' => $post['installment_id'], 'payment_method' => $post['payment']['method']));
	}


	public function payWithSagepayServer($post, $order_id)
	{
		try {
			$order = Mage::getModel("sales/order")->loadByIncrementId($order_id);
			$billing_address = Mage::getModel('sales/order_address')->load($order->getBillingAddressId());
			$delivery_address = Mage::getModel('sales/order_address')->load($order->getShippingAddressId());

			$amount = round($post[$post['installment_id']], 2);

			$sagepay_server = new Ebizmarts_SagePaySuite_Model_SagePayServer();
			$sagepaysuite_model = new Ebizmarts_SagePaySuite_Model_Api_Payment();

			$request = array();
			$request['VPSProtocol'] = $sagepay_server->getVpsProtocolVersion($sagepay_server->getConfigData('mode'));
			$request['TxType'] = 'PAYMENT';
			$request['Vendor'] = Mage::getStoreConfig('payment/sagepaysuite/vendor', Mage::app()->getStore());
			$request['VendorTxCode'] = substr($order_id . '-' . date('Y-m-d-H-i-s'), 0, 40);
			$request['Amount'] = number_format($amount, 2, '.', '');
			$request['Currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
			$request['Description'] = 'Installment Id: ' . $post['installment_id'];
			$request['NotificationURL'] = $this->getSagepayServerNotificationUrl($post, $order_id);
			$request['SuccessURL'] = $this->getSagepayServerSuccessUrl($post, $order_id);
			$request['RedirectURL'] = $this->getSagepayServerRedirectUrl($post, $order_id);
			$request['FailureURL'] = $this->getSagepayServerFailureUrl($post, $order_id);
			$request['BillingSurname'] = $billing_address->getLastname();
			$request['BillingFirstnames'] = $billing_address->getFirstname();
			$request['BillingAddress1'] = $billing_address['street'];
			$request['BillingCity'] = $billing_address->getCity();
			$request['BillingPostCode'] = $billing_address->getPostcode();
			$request['BillingCountry'] = $billing_address->getCountryId();

			if($billing_address->getCountryId() == "US")
			{
				$request['BillingState'] =  strtoupper(substr($billing_address['region'], 0, 2)); 
			}

			$request['DeliverySurname'] = $delivery_address->getLastname();
			$request['DeliveryFirstnames'] = $delivery_address->getFirstname();
			$request['DeliveryAddress1'] = $delivery_address['street'];
			$request['DeliveryCity'] = $delivery_address->getCity();
			$request['DeliveryPostCode'] = $delivery_address->getPostcode();
			$request['DeliveryCountry'] = $delivery_address->getCountryId();

			if($delivery_address->getCountryId() == "US")
			{
				$request['DeliveryState'] = strtoupper(substr($billing_address['region'], 0, 2)); 
			}

			Mage::log('Request of Installment Id: ' . $post['installment_id']);
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
			curl_setopt($curlSession, CURLOPT_URL, $sagepay_server->getRequestUri());
			curl_setopt($curlSession, CURLOPT_HEADER, 0);
			curl_setopt($curlSession, CURLOPT_POST, 1);
			curl_setopt($curlSession, CURLOPT_POSTFIELDS, $rd);
			curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curlSession, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 2);

			$rawresponse = curl_exec($curlSession);

			//Split response into name=value pairs
			$response = explode(chr(10), $rawresponse);

			// Check that a connection was made
			if (curl_error($curlSession)) {
				$output['Status'] = 'FAIL';
				$output['StatusDetail'] = htmlentities(curl_error($curlSession)) . '. ' . $sagepay_server->getConfigData('timeout_message');   // 'FAIL';
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

			if (isset($output['VPSTxId'])) {
				Indies_Fee_Model_Fee::$transactionId = $output['VPSTxId'];
			}

			Mage::log('Response of Installment Id: ' . $post['installment_id']);
			Mage::log($output);
			return $output;
		} catch (Exception $e) {
            Mage::throwException(
 	           $this->__('Gateway request error: %s', $e->getMessage())
            );
		}
	}
/*  Task: Make partial payment module compatible with sage pay - End - Date: 22/01/2013 - By: Indies Services  */


/* Task : For send email template on stock avalibility - Start Date : 22/07/2013 */
 public function getStockAvailabilityEmailTemplate()
 {
	 return Mage::getStoreConfig('partialpayment/stock_availability_email/stock_availability_email_template', Mage::app()->getStore());
 }


 public function getStockAvailabilitySender()
 {
	return 	Mage::getStoreConfig('partialpayment/stock_availability_email/stock_availability_email_sender', Mage::app()->getStore());
 }


 public function canSendStockAvailabilityEmail()
 {
	return 	Mage::getStoreConfig('partialpayment/stock_availability_email/send_stock_availability_email', Mage::app()->getStore());
 }
/* Task : For send email template on stock avalibility - End Date : 22/07/2013 */
}
?>