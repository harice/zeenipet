<?php

/**
 * SERVER main model
 *
 * @category   Ebizmarts
 * @package    Ebizmarts_SagePaySuite
 * @author     Ebizmarts <info@ebizmarts.com>
 */

class Indies_Partialpayment_Model_SagePayServer extends Ebizmarts_SagePaySuite_Model_SagePayServer {

    protected function _buildRequest($adminParams = array()) {
		$partialpaymentAuthentication = Mage::helper('partialpayment/data');
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		$quoteObj = $this->_getQuote();

		$billing  = $quoteObj->getBillingAddress();
		$shipping = $quoteObj->getShippingAddress();

		$request = new Varien_Object;

		$vendor = $this->getConfigData('vendor');

		$this->_vendorTxC = $this->_getTrnVendorTxCode();

		$confParam = (isset($adminParams['order']['send_confirmation'])) ? '&e=' . (int) $adminParams['order']['send_confirmation'] : '';

		if (isset($adminParams['order']['account']['email'])) {
			$confParam .= '&l=' . $adminParams['order']['account']['email'];
		}
		
		if (isset($adminParams['order']['account']['group_id'])) {
			$confParam .= '&g=' . $adminParams['order']['account']['group_id'];
		}

		// Transaction registration action
		$action = $this->getConfigData('payment_action');

		$customerEmail = $this->getCustomerEmail();

		$data = array();
		$data['VPSProtocol'] = $this->getVpsProtocolVersion($this->getConfigData('mode'));
		$data['TxType'] = strtoupper($action);
		$data['ReferrerID'] = $this->getConfigData('referrer_id');
		$data['CustomerEMail'] = ($customerEmail == null ? $billing->getEmail() : $customerEmail);
		$data['Vendor'] = $vendor;
		$data['VendorTxCode'] = $this->_vendorTxC;
		if ($this->_getIsAdmin()) {
			$data['User'] = Mage::getSingleton('admin/session')->getUser()->getUsername();
		}
		else {
			$data['User'] = ($customerEmail == null ? $billing->getEmail() : $customerEmail);
		}

		if ((string) $this->getConfigData('trncurrency') == 'store') {
			$data['Amount'] = $this->formatAmount($quoteObj->getGrandTotal(), $quoteObj->getQuoteCurrencyCode());
			$data['Currency'] = $quoteObj->getQuoteCurrencyCode();
		} else if ((string) $this->getConfigData('trncurrency') == 'switcher') {
			$data['Amount'] = $this->formatAmount($quoteObj->getGrandTotal(), Mage::app()->getStore()->getCurrentCurrencyCode());
			$data['Currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
		}
		else {
			$data['Amount'] = $this->formatAmount($quoteObj->getBaseGrandTotal(), $quoteObj->getBaseCurrencyCode());
			$data['Currency'] = $quoteObj->getBaseCurrencyCode();
		}

/*  Task: Make deposit payment / partial payment module compatible with Sage Pay Suite – Server Integration - Start - Date: 03/06/2013 - By: Indies Services  */
		$fee_amount = Indies_Fee_Model_Fee::getFee();
		if (!$partialpaymentAuthentication->canRun() || !$partialpaymentHelper->isEnabled()) {
			$deposit = Indies_Deposit_Model_Deposit::getGrandTotalDeposit($shipping);
			$deposit_amount = round($deposit, 2);
			$data['Amount'] = number_format($deposit_amount, 2, '.', '');
		}
		elseif ($fee_amount > 0) {
			$deposit = Indies_Deposit_Model_Sales_Quote_Address_Total_Deposit::$deposit;
			$deposit_amount = round($deposit, 2);
			$data['Amount'] = number_format($deposit_amount, 2, '.', '');
		}
		else {
			$deposit = Indies_Deposit_Model_Deposit::$grandTotal;
			$deposit_amount = round($deposit, 2);
			$data['Amount'] = number_format($deposit_amount, 2, '.', '');
		}
/*  Task: Make deposit payment / partial payment module compatible with Sage Pay Suite – Server Integration - End - Date: 03/06/2013 - By: Indies Services  */

		$data['Description'] = $this->ss($this->cleanInput($this->getConfigData('purchase_description') . ' User: ', 'Text') . $data['User'], 100);
		$data['NotificationURL'] = $this->getNotificationUrl() . $confParam;
		$data['SuccessURL'] = $this->getSuccessUrl() . $confParam;
		$data['RedirectURL'] = $this->getRedirectUrl() . $confParam;
		$data['FailureURL'] = $this->getFailureUrl() . $confParam;
		$data['BillingSurname'] = $this->ss($billing->getLastname(), 20);
		$data['BillingFirstnames'] = $this->ss($billing->getFirstname(), 20);
		$data['BillingAddress1'] = ($this->getConfigData('mode') == 'test') ? 88 : $this->ss($billing->getStreet(1), 100);
		$data['BillingAddress2'] = ($this->getConfigData('mode') == 'test') ? 88 : $this->ss($billing->getStreet(2), 100);
		$data['BillingPostCode'] = ($this->getConfigData('mode') == 'test') ? 412 : preg_replace("/[^a-zA-Z0-9-\s]/", "", $this->ss($billing->getPostcode(), 10));
		$data['BillingCity'] = $this->ss($billing->getCity(), 40);
		$data['BillingCountry'] = $billing->getCountry();
		$data['BillingPhone'] = $this->ss($this->_cphone($billing->getTelephone()), 20);

		// Set delivery information for virtual products ONLY orders
		if ($quoteObj->getIsVirtual()) {
			$data['DeliverySurname'] = $this->ss($billing->getLastname(), 20);
			$data['DeliveryFirstnames'] = $this->ss($billing->getFirstname(), 20);
			$data['DeliveryAddress1'] = $this->ss($billing->getStreet(1), 100);
			$data['DeliveryAddress2'] = $this->ss($billing->getStreet(2), 100);
			$data['DeliveryCity'] = $this->ss($billing->getCity(), 40);
			$data['DeliveryPostCode'] = preg_replace("/[^a-zA-Z0-9-\s]/", "", $this->ss($billing->getPostcode(), 10));
			$data['DeliveryCountry'] = $billing->getCountry();
			$data['DeliveryPhone'] = $this->ss($this->_cphone($billing->getTelephone()), 20);
		}
		else {
			$data['DeliveryPhone'] = $this->ss($this->_cphone($shipping->getTelephone()), 20);
			$data['DeliverySurname'] = $this->ss($shipping->getLastname(), 20);
			$data['DeliveryFirstnames'] = $this->ss($shipping->getFirstname(), 20);
			$data['DeliveryAddress1'] = $this->ss($shipping->getStreet(1), 100);
			$data['DeliveryAddress2'] = $this->ss($shipping->getStreet(2), 100);
			$data['DeliveryCity'] = $this->ss($shipping->getCity(), 40);
			$data['DeliveryPostCode'] = preg_replace("/[^a-zA-Z0-9-\s]/", "", $this->ss($shipping->getPostcode(), 10));
			$data['DeliveryCountry'] = $shipping->getCountry();
		}

		if ($data['DeliveryCountry'] == 'US') {
			if ($quoteObj->getIsVirtual()) {
				$data['DeliveryState'] = $billing->getRegionCode();
			} else {
				$data['DeliveryState'] = $shipping->getRegionCode();
			}
		}
		if ($data['BillingCountry'] == 'US') {
			$data['BillingState'] = $billing->getRegionCode();
		}

		if (empty($data['DeliveryPostCode'])) {
			$data['DeliveryPostCode'] = '000';
		}
		
		if (empty($data['BillingPostCode'])) {
			$data['BillingPostCode'] = '000';
		}
		
		$data['ContactNumber'] = substr($this->_cphone($billing->getTelephone()), 0, 20);        
		
		if ($this->getSendBasket()) {
			$data['BasketXML'] = $this->getBasketXml($quoteObj);            
		}

		$data['Website'] = Mage::app()->getStore()->getWebsite()->getName();
		
		$data['Profile'] = (string)$this->getConfigData('template_profile');
		
		//Setting template style to NORMAL if customer is redirected to Sage Pay
		$tplStyle = (string)$this->getConfigData('payment_iframe_position');
		if($tplStyle == 'full_redirect') {
			$data['Profile'] = 'NORMAL';
		}

		if ($this->_getIsAdmin() !== false) {
			$data['AccountType']   = 'M';
			$data['Apply3DSecure'] = '2';
		}

		$data['AllowGiftAid'] = (int) $this->getConfigData('allow_gift_aid');
		$data['ApplyAVSCV2']  = $this->getConfigData('avscv2');


		$customerXML = $this->getCustomerXml($quoteObj);
		if (!is_null($customerXML)) {
			$data['CustomerXML'] = $customerXML;
		}

		//Set to CreateToken if needed
		if($this->_createToken() OR (isset($adminParams['payment']) && isset($adminParams['payment']['remembertoken']))) {
			$data['CreateToken'] = '1';
		}
		
		$request->setData($data);

		/**
		 * Reward Points
		 */
		if ($this->_getQuote()->getRewardInstance()) {
			Mage::getSingleton('checkout/session')->setSagePayRewInst($this->_getQuote()->getRewardInstance());
		}
		
		if ($this->_getQuote()->getCustomerBalanceInstance()) {
			Mage::getSingleton('checkout/session')->setSagePayCustBalanceInst($this->_getQuote()->getCustomerBalanceInstance());
		}
		/**
		 * Reward Points
		 */
		return $request;
    }
}

