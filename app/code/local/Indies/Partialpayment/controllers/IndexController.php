<?php
class Indies_Partialpayment_IndexController extends Mage_Core_Controller_Front_Action
{
	const STATE_OPEN = 1;
	const XML_PATH_EMAIL_TEMPLATE	= 'partialpayment/email_group/email_template';
	const XML_PATH_EMAIL_RECIPIENT	= 'partialpayment/email_group/recipient_email';
    const XML_PATH_EMAIL_SENDER		= 'partialpayment/email_group/sender_email_identity';
	/* variable for eway */
	const EWAY_CURL_ERROR_OFFSET = 1000;
	const EWAY_XML_ERROR_OFFSET = 2000;
	const EWAY_TRANSACTION_OK = 0;
	const EWAY_TRANSACTION_FAILED = 1;
	const EWAY_TRANSACTION_UNKNOWN =  2;
	
	var $parser;
    var $xmlData;
    var $currentTag;
	
	var $myResultTrxnStatus;
    var $myResultTrxnNumber;
    var $myResultTrxnOption1;
    var $myResultTrxnOption2;
    var $myResultTrxnOption3;
    var $myResultTrxnReference;
    var $myResultTrxnError;
    var $myResultAuthCode;
    var $myResultReturnAmount;
    
    var $myError;
    var $myErrorMessage;
	/* variable for eway end*/
	// ideal variable start
	var $SetupUrl;
	// ideal variable end
	public function installmentsAction ()
	{
		
			if(!Mage::getSingleton('customer/session')->isLoggedIn()) {  // if not logged in
			header("Status: 301");
			$this->_redirect("customer/account/login/") ;  // send to the login page
		}
		else
		{
			$cartHelper = Mage::helper('checkout/cart');
			if (!sizeof($cartHelper->getCart()->getItems())) {
				$cart = Mage::getSingleton('checkout/cart'); 
				$product = new Mage_Catalog_Model_Product();
				// Build the product
				$product->setSku('some-sku-value-here');
				$product->setAttributeSetId('some_int_value_of_some_attribute');
				$product->setTypeId('simple');
				$product->setName('Some cool product name');
				$product->setCategoryIds(Mage::app()->getStore()->getRootCategoryId()); # some cat id's, my is 7
				$product->setWebsiteIDs(array(Mage::app()->getStore(true)->getWebsite()->getId())); # Website id, my is 1 (default frontend)
				$product->setDescription('Full description here');
				$product->setShortDescription('Short description here');
				$product->setPrice(39.99); # Set some price
				# Custom created and assigned attributes
				$product->setHeight('my_custom_attribute1_val');
				$product->setWidth('my_custom_attribute2_val');
				$product->setDepth('my_custom_attribute3_val');
				$product->setType('my_custom_attribute4_val');
				//Default Magento attribute
				$product->setWeight(4.0000);
				$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
				$product->setStatus(1);
				$product->setTaxClassId(0); # My default tax class
				$product->setStockData(array(
					'is_in_stock' => 1,
					'qty' => 99999
				));
				$product->setCreatedAt(strtotime('now'));
				try {
					//$product->save();
				}
				catch (Exception $ex) {
					//Mage::log("Exception: " . $ex);
				}
						
							$quote = Mage::getSingleton('checkout/session')->getQuote();
							$customerSession = Mage::getSingleton('customer/session')->getCustomer();
							$arr=array( 'country_id' => 'US',
									'region_id' => 2,
									'region' =>0, 
									'city' => 'xyz',
									'postcode' => 90404,
									'cart' => 1
									);
							
							$addToCartInfo = (array) $product->getAddToCartInfo();
							$addressInfo = (array) $arr;
			  
							$shippingAddress = $quote->getShippingAddress();
							$shippingAddress->setCountryId('US');
							$shippingAddress->setRegionId(2);
							$shippingAddress->setPostcode(90404);
							$shippingAddress->setRegion(0);
							$shippingAddress->setCity("test");
							$shippingAddress->setCollectShippingRates(true);
				
							$request = new Varien_Object($addToCartInfo);
							$result =  $quote->addProduct($product, $request);
						
							$quote->collectTotals();
							
				
			}
		}
			
		$this->loadLayout();
	
		$orderId = Mage::app()->getRequest()->getParam('order_id');
		
		$this->getLayout()->getBlock('head')->setTitle($this->__('Installments of Order # %s',$orderId));
		$this->renderLayout();
		}
    public function indexAction ()
    {
		if(!Mage::getSingleton('customer/session')->isLoggedIn()) 
		{  // if not logged in
			header("Status: 301");
			$this->_redirect("customer/account/login/") ;  // send to the login page
		}
		else
		{
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Partially Paid Orders'));
		}
		$this->renderLayout();

	 }
	public function postRequest($incrementId,$partial_payment_amount,$ccType)
	{
		// getting start currencies
		 $currentCurrency = Mage::app()->getStore()->getCurrentCurrencyCode(); 
		 $currencies = array();
		 foreach (Mage::getConfig()->getNode('global/payment/atos_standard/currencies')->asArray() as $data) {
			$currencies[$data['iso']] = $data['code'];
		}

         $currencies[$currentCurrency];
		// end currencies
		
		// getting start country 
		$_allowedCountryCode = array('be', 'fr', 'de', 'it', 'es', 'en');
		$Acountry = Mage::getStoreConfig('general/country');
        $current_country_code = strtolower($Acountry['default']);
		$merchantCountry;
        if (in_array($current_country_code, $_allowedCountryCode)) {
            $merchantCountry = $current_country_code;
        } else {
            $merchantCountry = 'en';
        }
		//end country
		
		//get language
		
		$language = substr(Mage::getStoreConfig('general/locale/code'), 0, 2);

		$languages = array();
        foreach (Mage::getConfig()->getNode('global/payment/atos_standard/languages')->asArray() as $data) {
            $languages[$data['code']] = $data['name'];
        }
		
		$Alanguages = $languages;
		$merchantLanguage;
        if (count($Alanguages) === 1) {
            $merchantLanguage = strtolower($Alanguages[0]);
        }
		else
		{$merchantLanguage = 'fr';}

        if (array_key_exists($language, $Alanguages)) {
            $Acode = array_keys($Alanguages);
            $key = array_search($language, $Acode);

            $merchantLanguage = strtolower($Acode[$key]);
        }
		else
		{$merchantLanguage = 'fr';}
		
		//end language
		
		$merchantId = Mage::getStoreConfig('atos/config_standard/merchant_id');
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$response = Mage::helper('core/url')->getHomeUrl()."partialpayment/index/atosResponse";
		$normal = Mage::helper('core/url')->getHomeUrl()."partialpayment/index/atosNormalResponse";
		$requestBinary = Mage::getStoreConfig('atos/config_bin_files/bin_request');
		// start path file
		$path = DS . 'lib' . DS . 'atos' . DS;
        $fullPath = Mage::getBaseDir('base') . $path;
		$pathfile = Mage::getBaseDir('base') . $path . 'pathfile.' . $merchantId;
		// end path file
		 
		$amount = $partial_payment_amount * 100;
		
		if($amount<100)
		$amount = 100;
		
		$parm="merchant_id=".$merchantId;
		$parm="$parm language=".$merchantLanguage;
		$parm="$parm merchant_country=".$merchantCountry;
		$parm="$parm amount=".$amount;
		$parm="$parm currency_code=".$currencies[$currentCurrency];
		$parm="$parm pathfile=".$pathfile;
		$parm="$parm normal_return_url=".$normal;
		$parm="$parm cancel_return_url=".$response;
		$parm="$parm automatic_response_url=".$response;
		$parm="$parm order_id=".$incrementId;
		$parm="$parm customer_id=".$customerId;
		$parm="$parm payment_means=".$ccType.",2";
		$path_bin = $requestBinary;//"/home/kouzinac/public_html/new/lib/atos/binary/request";//$requestBinary."\\request";//"/home/kouzinac/public_html/new/lib/atos/binary/request";;
		
		$parm = escapeshellcmd($parm);	
		$result=exec("$path_bin $parm");
		
		$tableau = explode ("!", $result);
		
		$code = $tableau[1];
		$error = $tableau[2];
		$message = $tableau[3];
		
		if ($code == 0){
		echo $error ;
		echo $message;
		print(" <script src='http://code.jquery.com/jquery-latest.js'></script>   
		<script type='text/javascript'>
			$('input').trigger('click');
		</script>");
		}
	}
	public function atosNormalResponseAction()
	{
		$merchantId = Mage::getStoreConfig('atos/config_standard/merchant_id');
		$path = DS . 'lib' . DS . 'atos' . DS;
        $fullPath = Mage::getBaseDir('base') . $path;
		$pathfile = "pathfile=".Mage::getBaseDir('base') . $path . 'pathfile.' . $merchantId;
		
		$requestBinary = Mage::getStoreConfig('atos/config_bin_files/bin_response');
		
		$message="message=$_POST[DATA]";
		//$pathfile=$pathfile;//"pathfile=/home/kouzinac/public_html/new/lib/atos/pathfile.011223344551111";
				
		$path_bin = $requestBinary;//"/home/kouzinac/public_html/new/lib/atos/binary/response";
		$message = escapeshellcmd($message);
		$result=exec("$path_bin $pathfile $message");
		
  
		$tableau = explode ("!", "$result");
		$code = $tableau[1];
		
		
		//11 response code //18 bank response code
		
		
		if ($code == 0 && $tableau[11]== "00" && $tableau[18]=="00" ){
		$url = Mage::helper('core/url')->getHomeUrl()."partialpayment/";
		Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;
		}
	}
	public function atosResponseAction()
	{
		
		//response code setting
		$merchantId = Mage::getStoreConfig('atos/config_standard/merchant_id');
		$path = DS . 'lib' . DS . 'atos' . DS;
        $fullPath = Mage::getBaseDir('base') . $path;
		$pathfile = "pathfile=".Mage::getBaseDir('base') . $path . 'pathfile.' . $merchantId;
		
		$requestBinary = Mage::getStoreConfig('atos/config_bin_files/bin_response');
		
		$message="message=$_POST[DATA]";
				//$pathfile=$pathfile;//"pathfile=/home/kouzinac/public_html/new/lib/atos/pathfile.011223344551111";
				
		$path_bin = $requestBinary;//"/home/kouzinac/public_html/new/lib/atos/binary/response";
		$message = escapeshellcmd($message);
		$result=exec("$path_bin $pathfile $message");
		
  
		$tableau = explode ("!", "$result");
		
		$code = $tableau[1];
		
		
		//11 response code //18 bank response code
		
		
		if ($code == 0 && $tableau[11]== "00" && $tableau[18]=="00" ){
		
		$session = Mage::getSingleton('customer/session',  array("name"=>"frontend"));
		$session->setData($tableau[27], true);
			
		$partial_payment = Mage::getModel('partialpayment/partialpayment')->getCollection();;
		$partial_payment->addFieldToFilter('order_id', $tableau[27]);
		$size = $partial_payment->getSize();
		
		$id=0;
		foreach ($partial_payment as $item)
				$id=$item->getPartialPaymentId();
				
		// start
		$partial_payment = Mage::getModel('partialpayment/partialpayment')->load($id);
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');	
			
		$productModel = Mage::getModel('partialpayment/product')->getCollection()
						->addFieldToFilter('partial_payment_id',$id);
						
		foreach($productModel as $product)
		{
			if($product->getRemainingInstallment()>0)
			{
				$remain = $product->getRemainingAmount()/$product->getRemainingInstallment();
				$product->setRemainingInstallment($product->getRemainingInstallment() - 1);
				$product->setRemainingAmount($product->getRemainingAmount() - $remain);
				$product->setPaidInstallment($product->getPaidInstallment() + 1);
				$product->setPaidAmount($product->getPaidAmount() + $remain);
				$product->save();
				
			}
		}	
								
		$partial_payment->setPaidAmount(($partial_payment->getPaidAmount() + ($tableau[5]/100)));
		$partial_payment->setRemainingAmount(($partial_payment->getRemainingAmount() - ($tableau[5]/100)));
		$partial_payment->setUpdatedDate(date('Y-m-d'));
		$partial_payment->setPaidInstallment($partial_payment->getPaidInstallment() + 1);
		$partial_payment->setRemainingInstallment($partial_payment->getRemainingInstallment() - 1);
		$partial_payment->save();
					
		//edited by indies on 2-1-2013 start
		if($partial_payment->getTotalAmount() == $partial_payment->getPaidAmount())
		{
			$partial_payment->setRemainingAmount(0);
			$partial_payment->setPartialPaymentStatus('Complete');
			$partial_payment->setPaidAmount($partial_payment->getTotalAmount());
			$partial_payment->save();
		}

$installmentData = Mage::getModel('partialpayment/installment')->getCollection()->addFieldToFilter('partial_payment_id', $id)->addFieldToFilter('installment_status', array('in' => array('Remaining' , 'Canceled')))->getData();
		
		if (isset($installmentData[0]['installment_id'])) {
			$this->sendEmailSuccess($partial_payment);
			$installmentModel = Mage::getModel('partialpayment/installment')->load($installmentData[0]['installment_id']);

			$installmentModel->setInstallmentPaidDate(date('Y-m-d'));
			$installmentModel->setInstallmentStatus('Paid');
			$installmentModel->setPaymentMethod('atos_standard');
			$installmentModel->setTxnId($tableau[6]);
			$installmentModel->save();
		}	

		$paid_installment = $partial_payment->getPaidInstallment();
		if($paid_installment == 2)
			Mage::getSingleton('core/session')->addSuccess($paid_installment.'nd installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
		elseif($paid_installment == 3)
			Mage::getSingleton('core/session')->addSuccess($paid_installment.'rd installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
		else
			Mage::getSingleton('core/session')->addSuccess($paid_installment.'th installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
		
		}
		$url = Mage::helper('core/url')->getHomeUrl()."partialpayment/";
		Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;
	
	}
	public function overviewAction()
	{
		$post = $this->getRequest()->getPost();
		$currentUrl = $post['refer'];
		$message = $this->validateSecondInstallmentForm($post);

		if ($message != '') {
			Mage::getSingleton('core/session')->addError($message);
			Mage::app()->getFrontController()->getResponse()->setRedirect($currentUrl);
			Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;
		}

		$partial_payment = Mage::getModel('partialpayment/partialpayment')->load($post['partial_payment_id']);
		Mage::getSingleton('core/session')->setPartialPaymentId($post['partial_payment_id']);
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		if(isset($post['payment']['cc_type']))
		$ccType = $post['payment']['cc_type'];

		$incrementId = $partial_payment['order_id'];
		try
		{
/*  Task: Make partial payment module compatible with sage pay - Start - Date: 22/01/2013 - By: Indies Services  */
			if(strpos($post['payment']['method'], 'sagepay') === false && !$this->_capturePayment($partial_payment)){
			}

			if(strpos($post['payment']['method'], 'sagepaydirectpro') !== false)
			{
				$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
				$response = $partialpaymentHelper->payWithSagepayDirectPro($post, $partial_payment->getOrderId());
				try {
					$error_message = '';
					
					if(empty($response) || !isset($response['Status'])) {
						$error_message = $this->__('Sage Pay is not available at this time. Please try again later.');
					}

					switch($response['Status']) {
						case 'FAIL':
							$error_message = $this->__($response['StatusDetail']);
							break;
						case 'FAIL_NOMAIL':
							$error_message = $this->__($response['StatusDetail']);
							break;
						case 'INVALID':
							$error_message = $this->__('INVALID. %s', $response['StatusDetail']);
							break;
						case 'MALFORMED':
							$error_message = $this->__('MALFORMED. %s', $response['StatusDetail']);
							break;
						case 'ERROR':
							$error_message = $this->__('ERROR. %s', $response['StatusDetail']);
							break;
						case 'REJECTED':
							$error_message = $this->__('REJECTED. %s', $response['StatusDetail']);
							break;
						case '3DAUTH':
							$error_message = $this->__($response['StatusDetail']);
							break;
						default:
							break;
					}
					if (!empty($error_message)) {
						
						Mage::getSingleton('core/session')->addError($error_message);
						Mage::app()->getFrontController()->getResponse()->setRedirect($currentUrl);
						Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;	
					}
				} catch (Exception $e) {
					Mage::throwException(
					   $this->__('%s', $e->getMessage())
					);
				}
			}

			if(strpos($post['payment']['method'], 'sagepayserver') !== false)
			{
				$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
				$response = $partialpaymentHelper->payWithSagepayServer($post, $partial_payment->getOrderId());
				try {
					$error_message = '';
					if(empty($response) || !isset($response['Status'])) {
						$error_message = $this->__('Sage Pay is not available at this time. Please try again later.');
					}

					switch($response['Status']) {
						case 'FAIL':
							$error_message = $this->__($response['StatusDetail']);
							break;
						case 'FAIL_NOMAIL':
							$error_message = $this->__($response['StatusDetail']);
							break;
						case 'INVALID':
							$error_message = $this->__('INVALID. %s', $response['StatusDetail']);
							break;
						case 'MALFORMED':
							$error_message = $this->__('MALFORMED. %s', $response['StatusDetail']);
							break;
						case 'ERROR':
							$error_message = $this->__('ERROR. %s', $response['StatusDetail']);
							break;
						case 'REJECTED':
							$error_message = $this->__('REJECTED. %s', $response['StatusDetail']);
							break;
						case '3DAUTH':
							$error_message = $this->__($response['StatusDetail']);
							break;
						default:
							break;
					}
					if (!empty($error_message)) {
						
						Mage::getSingleton('core/session')->addError($error_message);
						Mage::app()->getFrontController()->getResponse()->setRedirect($currentUrl);
						Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;	
					}
					$html = '<html><body>';
					$html .= $this->getSagepayServerForm($response);
					$html .= '<script type="text/javascript">document.getElementById("sagepay_server_checkout").submit();</script>';
					$html .= '</body></html>';
					echo $html;
					exit;
				} catch (Exception $e) {
					Mage::throwException(
					   $this->__('%s', $e->getMessage())
					);
				}
			}
/*  Task: Make partial payment module compatible with sage pay - End - Date: 22/01/2013 - By: Indies Services  */

			if($post['payment']['method'] == 'atos_standard')
			{
					$partial_payment_amount = number_format($post[$post['installment_id']],2);
					$this->postRequest($incrementId,$partial_payment_amount,$ccType);
			}

			if($post['payment']['method'] == 'eway_direct')
			{
					$partial_payment_amount = number_format($post[$post['installment_id']],2);
					$err=$this->doPayment($incrementId,$partial_payment_amount);
					if( $err!=0)
					{
							Mage::getSingleton('core/session')->addError($this->myResultTrxnError);	
							Mage::app()->getFrontController()->getResponse()->setRedirect($currentUrl);
							Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;					
					}
			}
			elseif($post['payment']['method'] == 'paypal_standard')
			{
					$partial_payment_amount = number_format($post[$post['installment_id']],2);
					$instllmentid = $post['installment_id'];
					$partialid = $post['partial_payment_id'];
					$html = '<html><body>';
					$html.= $this->__('You will be redirected to PayPal in a few seconds.');
					$html.= $this->getPaypalForm($incrementId, $partial_payment_amount, Mage::getUrl("partialpayment/standard/success",array('installment_id'=>$instllmentid,'partial_payment_id'=>$partialid)), Mage::getUrl("partialpayment/standard/cancel",array('installment_id'=>$instllmentid,'partial_payment_id'=>$partialid)), $instllmentid);
					$html.= '<script type="text/javascript">document.getElementById("paypal_standard_checkout").submit();</script>';
					$html.= '</body></html>';
					sleep(4);
					echo $html;
			}if($post['payment']['method'] == 'idealcheckoutideal')
			{
				$partial_payment_amount = number_format($post[$post['installment_id']],2);
				$description="Installment Partial Payment";
				$sales = Mage::getModel('sales/order')->loadByIncrementId($incrementId)->getData();
				
				$model = Mage::getModel('partialpayment/partialpayment')->load($post['partial_payment_id']);
				$isnew = $model->getTotalInstallment() < $model->getPaidInstallment() ? true : false ;
				if($isnew){
					return false;
				}
				$orderid = $model->getOrderId();
				$order = Mage::getModel("sales/order")->loadByIncrementId($orderid); 
				 // Get the id of the orders shipping address
				$shippingId = $order->getShippingAddressId();
				// Get shipping address data using the id
				$address = Mage::getModel('sales/order_address')->load($shippingId);
				
				// Validate amount
				if($order->getGrandTotal() < 1.00)
				{
					Mage::throwException(Mage::helper('idealcheckoutideal')->__('The total amount of order #' . $orderid . ' is ' . $order->getGrandTotal() . ', but should be at least 1.00.'));
				}
		
				// Load database settings
				$aDatabaseSettings = idealcheckout_getDatabaseSettings();
		
				$sStoreCode = idealcheckout_getStoreCode(); // Mage::app()->getStore()->getCode();
				$sGatewayCode = 'ideal';
				$sLanguageCode = substr(Mage::app()->getLocale()->getDefaultLocale(), 0, 2); // nl, de, en
				$sCountryCode = '';
				$sCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
		
				if(strcasecmp($sCurrencyCode, 'GBP') === 0)
				{
					// $sLanguageCode = 'EN';
					$sCountryCode = 'UK';
				}
		
				$sOrderId = $order->getRealOrderId();
				$sOrderCode = idealcheckout_getRandomCode(32);
				$aOrderParams = array();
				$sTransactionId = idealcheckout_getRandomCode(32);
				$sTransactionCode = idealcheckout_getRandomCode(32);
				$fTransactionAmount = $partial_payment_amount;
				$sTransactionDescription = idealcheckout_getTranslation($sLanguageCode, 'idealcheckout', 'Webshop order #{0}', array($sOrderId));
		
				$sReturnUrl = Mage::getUrl('idealcheckoutideal/idealcheckoutideal/return', array('_secure' => true, 'order_id' => $sOrderId, 'order_code' => $sOrderCode));
				
				$sReturnUrl = $this->fixUrl($sReturnUrl, false);
				
				$url =  Mage::getUrl('partialpayment/index/return', array('_secure' => true, 'order_id' => $sOrderId, 'order_code' => $sOrderCode));				
				$url = $this->fixUrl($url, false);
				
				$sTransactionPaymentUrl = $sReturnUrl;
				$sTransactionSuccessUrl = $url;
				$sTransactionPendingUrl = $url;
				$sTransactionFailureUrl = $url;
		
				// Store ORDER information
				$aOrderParams['order'] = array(
					'id' => $orderid
				);
				
				// Store CONTACT information
				$oAddress = $order->getBillingAddress();
		
				$aOrderParams['contact'] = array(
					'company' => $oAddress->getCompany(), 
					'name' => $oAddress->getName(), 
					'address' => $oAddress->getStreetFull(), 
					'postalcode' => $oAddress->getPostcode(), 
					'city' => $oAddress->getCity(), 
					'phone' => $oAddress->getTelephone(), 
					'email' => $order->getCustomerEmail()
				);
				
				
				// Insert data into idealcheckout-table
				$sql = "INSERT INTO `" . $aDatabaseSettings['table'] . "` SET 
				`id` = NULL, 
				`order_id` = '" . idealcheckout_escapeSql($sOrderId) . "', 
				`order_code` = '" . idealcheckout_escapeSql($sOrderCode) . "', 
				`order_params` = '" . idealcheckout_escapeSql(idealcheckout_serialize($aOrderParams)) . "', 
				`store_code` = " . (empty($sStoreCode) ? "NULL" : "'" . idealcheckout_escapeSql($sStoreCode) . "'") . ", 
				`gateway_code` = '" . idealcheckout_escapeSql($sGatewayCode) . "', 
				`language_code` = " . (empty($sLanguageCode) ? "NULL" : "'" . idealcheckout_escapeSql($sLanguageCode) . "'") . ", 
				`country_code` = " . (empty($sCountryCode) ? "NULL" : "'" . idealcheckout_escapeSql($sCountryCode) . "'") . ", 
				`currency_code` = '" . idealcheckout_escapeSql($sCurrencyCode) . "', 
				`transaction_id` = '" . idealcheckout_escapeSql($sTransactionId) . "', 
				`transaction_code` = '" . idealcheckout_escapeSql($sTransactionCode) . "', 
				`transaction_params` = NULL, 
				`transaction_date` = '" . idealcheckout_escapeSql(time()) . "', 
				`transaction_amount` = '" . idealcheckout_escapeSql($fTransactionAmount) . "', 
				`transaction_description` = '" . idealcheckout_escapeSql($sTransactionDescription) . "', 
				`transaction_status` = NULL, 
				`transaction_url` = NULL, 
				`transaction_payment_url` = '" . idealcheckout_escapeSql($sTransactionPaymentUrl) . "', 
				`transaction_success_url` = '" . idealcheckout_escapeSql($sTransactionSuccessUrl) . "', 
				`transaction_pending_url` = '" . idealcheckout_escapeSql($sTransactionPendingUrl) . "', 
				`transaction_failure_url` = '" . idealcheckout_escapeSql($sTransactionFailureUrl) . "', 
				`transaction_log` = NULL";
				
				// Add record to transaction table
				Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);
				// Return iDEAL URL
				$sSetupUrl = Mage::getBaseUrl() . 'idealcheckout/setup.php?order_id=' . $sOrderId . '&order_code=' . $sOrderCode;
				$SetupUrl = $this->fixUrl($sSetupUrl, true);
			}
				
			if($post['payment']['method'] != 'paypal_standard' && $post['payment']['method'] != 'atos_standard'){
				
				if(($partial_payment->getTotalAmount() > $partial_payment->getPaidAmount()) && ($partial_payment->getRemainingAmount() > 0) && (round($partial_payment->getRemainingAmount(),2) >= round($post[$post['installment_id']],2)) )
					{
						
						if($post['payment']['method'] == 'idealcheckoutideal')
						{
							header('Location: ' . $SetupUrl);
							exit;
						}
						
						$productModel = Mage::getModel('partialpayment/product')->getCollection()
										->addFieldToFilter('partial_payment_id', $post['partial_payment_id']);
						
						foreach($productModel as $product)
						{
							if($product->getRemainingInstallment()>0)
							{
								$remain = $product->getRemainingAmount()/$product->getRemainingInstallment();
								$product->setRemainingInstallment($product->getRemainingInstallment() - 1);
								$product->setRemainingAmount($product->getRemainingAmount() - $remain);
								$product->setPaidInstallment($product->getPaidInstallment() + 1);
								$product->setPaidAmount($product->getPaidAmount() + $remain);
								$product->save();
								
							}
						}
						// Start: For Two Installments: - Update partial_payment_master Table.
						$partial_payment->setPaidAmount(($partial_payment->getPaidAmount() + $post[$post['installment_id']]));
						$partial_payment->setRemainingAmount(($partial_payment->getRemainingAmount() - $post[$post['installment_id']]));
						
						$partial_payment->setUpdatedDate(date('Y-m-d'));
						$partial_payment->setPaidInstallment($partial_payment->getPaidInstallment() + 1);
						$partial_payment->setRemainingInstallment($partial_payment->getRemainingInstallment() - 1);
						$partial_payment->save();
						
						//edited by indies on 2-1-2013 start
						if($partial_payment->getTotalInstallment() == $partial_payment->getPaidInstallment())
						{
							$partial_payment->setRemainingAmount(0);
							$partial_payment->setPartialPaymentStatus('Complete');
							$partial_payment->setPaidAmount($partial_payment->getTotalAmount());
							$partial_payment->save();
						}
						
						//edited by indies on 2-1-2013 end
						
						$installmentData = Mage::getModel('partialpayment/installment')->getCollection()->addFieldToFilter('partial_payment_id', $post['partial_payment_id'])->addFieldToFilter('installment_status', array('in' => array('Canceled','Remaining')))->getData();
						
						if (isset($installmentData[0]['installment_id'])) {
							$this->sendEmailSuccess($partial_payment);
							$installmentModel = Mage::getModel('partialpayment/installment')->load($installmentData[0]['installment_id']);

							$installmentModel->setInstallmentPaidDate(date('Y-m-d'));
							$installmentModel->setInstallmentStatus('Paid');
							$installmentModel->setPaymentMethod($post['payment']['method']);
							$installmentModel->setTxnId(Indies_Fee_Model_Fee::$transactionId);
							$installmentModel->save();
						}

							if($post['payment']['method'] != 'paypal_standard' && $post['payment']['method'] != 'atos_standard'){
							$paid_installment = $partial_payment->getPaidInstallment();
					if($paid_installment == 2)
						Mage::getSingleton('core/session')->addSuccess($paid_installment.'nd installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
					elseif($paid_installment == 3)
						Mage::getSingleton('core/session')->addSuccess($paid_installment.'rd installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
					else
						Mage::getSingleton('core/session')->addSuccess($paid_installment.'th installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
							Mage::app()->getFrontController()->getResponse()->setRedirect($currentUrl);
							Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;
							}
						
					}
				}
		}
		catch (Mage_Core_Exception $e)
		{
			// Payment Failed Email
			$this->sendEmailFailed($partial_payment);
			
			// Error Message
			Mage::getSingleton('core/session')->addError($e->getMessage());
			Mage::app()->getFrontController()->getResponse()->setRedirect($currentUrl);
			Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;
		}
	}


	public function validateSecondInstallmentForm ($post)
	{
		$message = '';

		if (!isset($post['installment_id']))
			$message = 'Please select an installment.';

		if (!isset($post['payment']['method']))
			$message = 'Please select a payment method.';

		if (!isset($post['installment_id']) && !isset($post['payment']['method']))
			$message = 'Please select an installment and payment method.';

		return $message;
	}


	protected function _capturePayment($model)
	{
		$data = $this->getRequest()->getPost();
		$model = Mage::getModel('partialpayment/partialpayment')->load($data['partial_payment_id']);
		$isnew = $model->getTotalInstallment() < $model->getPaidInstallment() ? true : false ;
		if($isnew){
			return false;
		}
		$orderid = $model->getOrderId();
		$order = Mage::getModel("sales/order")->loadByIncrementId($orderid);
		$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
		$invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_OPEN);
		$capturePayment = false;
		$paymentInfo = $this->getRequest()->getPost('payment', array());
		try
		{
			if(is_array($paymentInfo) && count($paymentInfo) && isset($paymentInfo['method']))
			{
				$calculationHelper = Mage::helper('partialpayment/calculation');
				$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

				$invoice->setBaseGrandTotal($calculationHelper->convertCurrencyAmount($data[$data['installment_id']]));
				$invoice->setGrandTotal($data[$data['installment_id']]);
				$invoice->getOrder()->setTotalDue($data[$data['installment_id']]);
				$capturePayment = Mage::getModel('partialpayment/sales_order_capture_payment');
				$capturePayment->setOrder($invoice->getOrder());
				$capturePayment->importData($paymentInfo);	
				$capturePayment->setAmountOrdered($data[$data['installment_id']]);
				$capturePayment->setBaseAmountOrdered($calculationHelper->convertCurrencyAmount($data[$data['installment_id']]));
				$capturePayment->setShippingAmount(0);
				$capturePayment->setBaseShippingAmount(0);
				$capturePayment->setAmountAuthorized($data[$data['installment_id']]);
				$capturePayment->setBaseAmountAuthorized($calculationHelper->convertCurrencyAmount($data[$data['installment_id']]));

				$clonedInvoice = clone $invoice;
				$invoice->getOrder()->addRelatedObject($capturePayment);
				if($capturePayment->canCapture()){							
					$capturePayment->capture($clonedInvoice);
					$capturePayment->pay($clonedInvoice);
				}
				else{
					$capturePayment->pay($clonedInvoice);
				}
				return true;
			}
		}catch(Exception $e){
			 Mage::throwException($this->__("Gateway error : {$e -> getMessage()}")); 
			  $this->_redirect('*/*/');
		}
	}
	
	// Paypal Second Installment Form.
	public function getPaypalForm($incrementId, $partial_payment_amount, $successUrl, $cancelUrl, $instllmentid)
	{
		$order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
		$business_account = Mage::getStoreConfig('paypal/general/business_account');
		$standard = Mage::getModel('paypal/standard');
		$html =	'
		<form id="paypal_standard_checkout" action="' . $standard->getConfig()->getPaypalUrl() . '" method="POST">
			<input type="hidden" id="business" name="business" value="' . $business_account . '" />
			<input type="hidden" id="invoice" name="invoice" value="' . $order->getIncrementId().'-'. $instllmentid .'-'. date('Ymd-His') . '" />
			<input type="hidden" id="currency_code" name="currency_code" value="' . $order->getOrderCurrencyCode() . '" />
			<input type="hidden" id="paymentaction" name="paymentaction" value="sale" />
			<input type="hidden" id="cmd" name="cmd" value="_xclick" />
			<input type="hidden" id="upload" name="upload" value="1" />
			<input type="hidden" id="return" name="return" value="' . $successUrl . '" />
			<input type="hidden" id="cancel_return" name="cancel_return" value="' .$cancelUrl .  '" />
			<input type="hidden" id="notify_url" name="notify_url" value="' . Mage::getUrl("paypal/ipn/") . '"/>
			<input type="hidden" id="bn" name="bn" value="Varien_Cart_WPS_US" />
			<input type="hidden" id="charset" name="charset" value="utf-8" />
			<input type="hidden" name="amount" value="' . $partial_payment_amount . '" />
			<input type="hidden" name="no_shipping" value="0" />
			<input type="hidden" name="item_name" value="Remaining Amount" />
			<input type="hidden" name="item_number" value="' . $order->getIncrementId() . '" />
		</form>';
		return $html;
	}


	// Sage Pay Server Second Installment Form.
	public function getSagepayServerForm($response)
	{
		$html =	'<form id="sagepay_server_checkout" action="' . $response['NextURL'] . '" method="POST"></form>';
		return $html;
	}


	public function sagepayservernotifyAction ()
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		$post = array();
		$post['partial_payment_id'] = Mage::app()->getRequest()->getParam('partial_payment_id');
		$post['installment_id'] = Mage::app()->getRequest()->getParam('installment_id');
		$post['payment']['method'] = Mage::app()->getRequest()->getParam('payment_method');

		$strResponse = 'Status=OK' . chr(13) . chr(10);
		$strResponse .= 'StatusDetail=Transaction completed successfully' . chr(13) . chr(10);
		$strResponse .= 'RedirectURL=' . $partialpaymentHelper->getSagepayServerSuccessUrl($post, Mage::app()->getRequest()->getParam('order_id')) . chr(13) . chr(10);

		echo $strResponse;
		exit;
	}


	public function sagepayserversuccessAction ()
	{
		$partial_payment = Mage::getModel('partialpayment/partialpayment')->load(Mage::app()->getRequest()->getParam('partial_payment_id'));
		$installmentModel = Mage::getModel('partialpayment/installment')->load(Mage::app()->getRequest()->getParam('installment_id'));

		if($partial_payment->getTotalInstallment() != $partial_payment->getPaidInstallment())
		{
			$productModel = Mage::getModel('partialpayment/product')->getCollection()->addFieldToFilter('partial_payment_id', Mage::app()->getRequest()->getParam('partial_payment_id'));
			
			foreach($productModel as $product)
			{
				if($product->getRemainingInstallment() > 0)
				{
					$remain = $product->getRemainingAmount() / $product->getRemainingInstallment();
					$product->setRemainingInstallment($product->getRemainingInstallment() - 1);
					$product->setRemainingAmount($product->getRemainingAmount() - $remain);
					$product->setPaidInstallment($product->getPaidInstallment() + 1);
					$product->setPaidAmount($product->getPaidAmount() + $remain);
					$product->save();
				}
			}

			$partial_payment->setPaidAmount(($partial_payment->getPaidAmount() + $installmentModel->getInstallmentAmount()));
			$partial_payment->setRemainingAmount(($partial_payment->getRemainingAmount() - $installmentModel->getInstallmentAmount()));
			$partial_payment->setUpdatedDate(date('Y-m-d'));
			$partial_payment->setPaidInstallment($partial_payment->getPaidInstallment() + 1);
			$partial_payment->setRemainingInstallment($partial_payment->getRemainingInstallment() - 1);
			$partial_payment->save();

			if($partial_payment->getTotalInstallment() == $partial_payment->getPaidInstallment())
			{
				$partial_payment->setRemainingAmount(0);
				$partial_payment->setPartialPaymentStatus('Complete');
				$partial_payment->setPaidAmount($partial_payment->getTotalAmount());
				$partial_payment->save();
			}

			$this->sendEmailSuccess($partial_payment);

			$installmentModel->setInstallmentPaidDate(date('Y-m-d'));
			$installmentModel->setInstallmentStatus('Paid');
			$installmentModel->setPaymentMethod(Mage::app()->getRequest()->getParam('payment_method'));
			$installmentModel->setTxnId(Indies_Fee_Model_Fee::$transactionId);
			$installmentModel->save();

			$paid_installment = $partial_payment->getPaidInstallment();

			if($paid_installment == 1) {
				Mage::getSingleton('core/session')->addSuccess($paid_installment.'st installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
			}
			elseif($paid_installment == 2) {
				Mage::getSingleton('core/session')->addSuccess($paid_installment.'nd installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
			}
			elseif($paid_installment == 3) {
				Mage::getSingleton('core/session')->addSuccess($paid_installment.'rd installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
			}
			else {
				Mage::getSingleton('core/session')->addSuccess($paid_installment.'th installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
			}
		}
		Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('partialpayment/index/installments', array('order_id' => $partial_payment->getOrderId(), 'partial_payment_id' => $partial_payment->getPartialPaymentId())));
		Mage::app()->getFrontController()->getResponse()->sendResponse();
		exit;
	}


	public function sagepayserverredirectAction ()
	{
		Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('partialpayment/index/installments', array('order_id' => Mage::app()->getRequest()->getParam('order_id'), 'partial_payment_id' => Mage::app()->getRequest()->getParam('partial_payment_id'))));
		Mage::app()->getFrontController()->getResponse()->sendResponse();
		exit;
	}


	public function sagepayserverfailureAction ()
	{
		Mage::getSingleton('core/session')->addError('Sage pay server transaction failed.');
		Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('partialpayment/index/installments', array('order_id' => Mage::app()->getRequest()->getParam('order_id'), 'partial_payment_id' => Mage::app()->getRequest()->getParam('partial_payment_id'))));
		Mage::app()->getFrontController()->getResponse()->sendResponse();
		exit;
	}


	public function sendEmailSuccess($partial_payment)
	{
		try
		{
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
			$storeId = Mage::app()->getStore()->getId();
			
			$installmentData = Mage::getModel('partialpayment/installment')->getCollection()
			->addFieldToFilter('partial_payment_id', $partial_payment->getPartialPaymentId())
			->addFieldToFilter('installment_status', array('in' => array('Remaining' , 'Canceled')))->getData();
			
			if (isset($installmentData[0]['installment_id'])) {
				$installmentModel = Mage::getModel('partialpayment/installment')->load($installmentData[0]['installment_id']);
				{
					$amount = $installmentModel->getInstallmentAmount();
				}
			}

			// Mail Data
			$customerName = $partial_payment->getCustomerFirstName() . ' ' . $partial_payment->getCustomerLastName();
			$incrementId = $partial_payment->getOrderId();
			$installment_amount = Mage::helper('checkout')->formatPrice($amount);
			$pid = $partial_payment->getPartialPaymentId();
			$paidamount = Mage::helper('checkout')->formatPrice($partial_payment->getPaidAmount());
			$remainamount = Mage::helper('checkout')->formatPrice($partial_payment->getRemainingAmount());
			$totalamount = Mage::helper('checkout')->formatPrice($partial_payment->getTotalAmount());
			$paidinstallment = $partial_payment->getPaidInstallment();
			$remaininstallment = $partial_payment->getRemainingInstallment();
			$totalinstallment = $partial_payment->getTotalInstallment();

			$data = array();
			$data['frontend_label'] = $partialpaymentHelper->getFrontendLabel();
			$data['customer_name'] = $customerName;
			$data['order_id'] = $incrementId;
			$data['installment_amount'] = $installment_amount;
			$data['partialpayment_id'] = $pid;
			$data['paid_amount'] = $paidamount;
			$data['remainnnig_amount'] = $remainamount;
			$data['total_amount'] = $totalamount;
			$data['paid_installment'] = $paidinstallment;
			$data['remainning_installment'] = $remaininstallment;
			$data['total_installment'] = $totalinstallment;
			$data['login_url'] = Mage::getUrl('partialpayment/index/installments',array('order_id'=>  $incrementId,'partial_payment_id'=>$pid));

			// Email Template 
			$translate = Mage::getSingleton('core/translate');
			$translate->setTranslateInline(false);
			$mailTemplate = Mage::getModel('core/email_template');

			$copyTo = $partialpaymentHelper->getInstallmentConfirmationEmailCCTo();
			$copyMethod = 'bcc';
			
			$emailInfo = Mage::getModel('core/email_info');
			$emailInfo->addTo($partial_payment->getCustomerEmail(), $customerName);
			
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
			$this->_redirect('*/*/');
			return;
		}
		catch(Exception $e){
			Mage::log("Exception" . $e);
			$this->_redirect('*/*/');
			return;
		}
	}
	
	public function sendEmailFailed($partial_payment)
	{
		try
		{
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
			$storeId = Mage::app()->getStore()->getId();
			$customerName = $partial_payment->getCustomerFirstName() . ' ' . $partial_payment->getCustomerLastName();
			$incrementId = $partial_payment->getOrderId();
			// Mail Data
			$data = array();
			$data['frontend_label'] = $partialpaymentHelper->getFrontendLabel();
			$data['customer_name'] = $customerName;
			$data['order_id'] = $incrementId;
			$data['result'] = 'Fail';
			// Email Template 
			$translate = Mage::getSingleton('core/translate');
			$translate->setTranslateInline(false);
			$mailTemplate = Mage::getModel('core/email_template');

			$copyTo = $partialpaymentHelper->getInstallmentConfirmationEmailCCTo();
			$copyMethod = 'bcc';
			
			$emailInfo = Mage::getModel('core/email_info');
			$emailInfo->addTo($partial_payment->getCustomerEmail(), $customerName);
			
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
			$this->_redirect('*/*/');
			return;
		}
		catch(Exception $e){
			Mage::log("Exception" . $e);
			$this->_redirect('*/*/');
			return;
		}
	}
	//eway method
	function doPayment($incrementId,$partial_payment_amount) {
		$post = $this->getRequest()->getPost();
		$customerId=Mage::getStoreConfig('payment/eway_direct/customer_id');
		$amount = number_format($partial_payment_amount, 2, '.', '');
		$ewayTotalAmount = round($amount * 100);
		$description="Installment Partial Payment";
		$sales = Mage::getModel('sales/order')->loadByIncrementId($incrementId)->getData();
		
		$model = Mage::getModel('partialpayment/partialpayment')->load($post['partial_payment_id']);
		$isnew = $model->getTotalInstallment() < $model->getPaidInstallment() ? true : false ;
		if($isnew){
			return false;
		}
		$orderid = $model->getOrderId();
		$order = Mage::getModel("sales/order")->loadByIncrementId($orderid); 
		 // Get the id of the orders shipping address
		$shippingId = $order->getShippingAddressId();
		// Get shipping address data using the id
		$address = Mage::getModel('sales/order_address')->load($shippingId);
		//".htmlentities($ewayTotalAmount)."
        $xmlRequest = "<ewaygateway>".
                "<ewayCustomerID>".htmlentities(substr($customerId, 0, 8))."</ewayCustomerID>".
                "<ewayTotalAmount>".htmlentities($ewayTotalAmount)."</ewayTotalAmount>".
				 "<ewayCustomerFirstName>".htmlentities(substr($sales['customer_firstname'], 0, 50))."</ewayCustomerFirstName>".
                "<ewayCustomerLastName>".htmlentities(substr($sales['customer_lastname'], 0, 50))."</ewayCustomerLastName>".
                "<ewayCustomerEmail>".htmlentities(substr($sales['customer_email'], 0, 50))."</ewayCustomerEmail>".
                "<ewayCustomerAddress>".htmlentities(substr($address['street'].",".$address['city'], 0, 255))."</ewayCustomerAddress>".
                "<ewayCustomerPostcode>".htmlentities(substr($address['postcode'], 0, 6))."</ewayCustomerPostcode>".
                "<ewayCustomerInvoiceDescription>".htmlentities(substr($description, 0, 255))."</ewayCustomerInvoiceDescription>".
                "<ewayCustomerInvoiceRef>".htmlentities(substr($incrementId,0, 50))."</ewayCustomerInvoiceRef>".
                "<ewayCardHoldersName>".htmlentities(substr($post['payment']['cc_owner1'], 0, 50))."</ewayCardHoldersName>".
                "<ewayCardNumber>".htmlentities(substr($post['payment']['cc_number1'], 0, 20))."</ewayCardNumber>".
                "<ewayCardExpiryMonth>".htmlentities(substr($post['payment']['cc_exp_month1'], 0, 2))."</ewayCardExpiryMonth>".
                "<ewayCardExpiryYear>".htmlentities(substr($post['payment']['cc_exp_year1'], 0, 2))."</ewayCardExpiryYear>".
				"<ewayTrxnNumber>".htmlentities(substr($incrementId, 0, 16))."</ewayTrxnNumber>".
				"<ewayCVN>".htmlentities(substr($post['payment']['cc_cid1'], 0, 4))."</ewayCVN>".
				"<ewayOption1></ewayOption1>".
                "<ewayOption2></ewayOption2>".
                "<ewayOption3></ewayOption3>".
                "</ewaygateway>";
		//Mage::log($xmlRequest);
		
        /* Use CURL to execute XML POST and write output into a string */
		if(Mage::getStoreConfig('payment/eway_direct/test_flag')==1)
		{
			$myGatewayURL='https://www.eway.com.au/gateway/xmltest/testpage.asp';
		}
		else
		{
			$myGatewayURL='https://www.eway.com.au/gateway_cvn/xmlpayment.asp';
		}
		
					
        $ch = curl_init($myGatewayURL);
        curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
 		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xmlRequest );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 240 );
        $xmlResponse = curl_exec( $ch );
		
		
        // Check whether the curl_exec worked.
        if( curl_errno( $ch ) == CURLE_OK ) {
            // It worked, so setup an XML parser for the result.
            $this->parser = xml_parser_create();
            
            // Disable XML tag capitalisation (Case Folding)
            xml_parser_set_option ($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
            
            // Define Callback functions for XML Parsing
            xml_set_object($this->parser, $this);
            xml_set_element_handler ($this->parser, "epXmlElementStart", "epXmlElementEnd");
            xml_set_character_data_handler ($this->parser, "epXmlData");
            
            // Parse the XML response
            xml_parse($this->parser, $xmlResponse, TRUE);
           
            if( xml_get_error_code( $this->parser ) == XML_ERROR_NONE ) {
                // Get the result into local variables.
				
                $this->myResultTrxnStatus = $this->xmlData['ewayTrxnStatus'];
				if(isset($this->xmlData['ewayTrxnNumber'])){
                $this->myResultTrxnNumber = $this->xmlData['ewayTrxnNumber'];
             	Indies_Fee_Model_Fee::$transactionId =  $this->myResultTrxnNumber;
				}
				if(isset($this->xmlData['ewayTrxnReference']))
                $this->myResultTrxnReference = $this->xmlData['ewayTrxnReference'];
				if(isset($this->xmlData['ewayAuthCode']))
                $this->myResultAuthCode = $this->xmlData['ewayAuthCode'];
                $this->myResultReturnAmount = $this->xmlData['ewayReturnAmount'];
                $this->myResultTrxnError = $this->xmlData['ewayTrxnError'];
                $this->myError = 0;
                $this->myErrorMessage = '';
				
            } else {
                // An XML error occured. Return the error message and number.
				
                $this->myError = xml_get_error_code( $this->parser ) + self::EWAY_XML_ERROR_OFFSET;
                $this->myErrorMessage = xml_error_string( $myError );
            }
            // Clean up our XML parser
            xml_parser_free( $this->parser );
        } else {
            // A CURL Error occured. Return the error message and number. (offset so we can pick the error apart)
            $this->myError = curl_errno( $ch ) + self::EWAY_CURL_ERROR_OFFSET;
            $this->myErrorMessage = curl_error( $ch );
        }
        // Clean up CURL, and return any error.
        curl_close( $ch );
		
        return $this->getError();
    }


	function getError()
    {
        if( $this->myError != 0 ) {
            // Internal Error
            return $this->myError;
        } else {
            // eWAY Error
            if( $this->myResultTrxnStatus == 'True' ) {
                return self::EWAY_TRANSACTION_OK;
            } elseif( $this->myResultTrxnStatus == 'False' ) {
                return self::EWAY_TRANSACTION_FAILED;
            } else {
                return self::EWAY_TRANSACTION_UNKNOWN;
            }
        }
    }


	public function gethelptooltipAction ()
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$post_data = Mage::app()->getRequest()->getPost();
		$product_price = $calculationHelper->convertCurrencyAmount(floatval($post_data['product_price']));
		$surcharge_value = 0;
		$remaining_installments = 1;
		$remaining_balance = 0;
		$help_tooltip = '';

		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		if ($partialpaymentHelper->isPartialPaymentOption2Installments() || $partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
			if ($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
				$remaining_installments = $partialpaymentHelper->getTotalNoOfInstallment() - 1;
			}

			if ($partialpaymentHelper->isSurchargeOptionsSingleSurcharge()) {
				$surcharge_value = $partialpaymentHelper->getSingleSurchargeValue();								
			}
			elseif ($partialpaymentHelper->isSurchargeOptionsMultipleSurcharge()) {
				$surcharge_value = explode(',', $partialpaymentHelper->getMultipleSurchargeValues());
				$surcharge_value = $surcharge_value[$remaining_installments];
			}

			if ($partialpaymentHelper->isSurchargeOptionsSingleSurcharge() || $partialpaymentHelper->isSurchargeOptionsMultipleSurcharge()) {
				if ($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
					$product_price += $surcharge_value;
				}
				else {
					$product_price += (($product_price * $surcharge_value) / 100);
				}
			}

			$initial_deposit = 0;

			if ($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
				$initial_deposit = $partialpaymentHelper->getFirstInstallmentAmount();
			}
			else {
				$initial_deposit = ($product_price * $partialpaymentHelper->getFirstInstallmentAmount()) / 100;
			}

			$help_tooltip .= 'Initial deposit of ' . Mage::helper('core')->currency($initial_deposit, true, false) . '.';

			$remaining_balance = $product_price - $initial_deposit;
		}

		$payment_plan = '';
		if ($partialpaymentHelper->isPaymentPlanDays()) {
			$payment_plan = $partialpaymentHelper->getPaymentPlanTotalNoOfDays() . ' days';
		}
		elseif ($partialpaymentHelper->isPaymentPlanWeekly()) {
			$payment_plan = 'week';
		}
		else {
			$payment_plan = 'month';
		}

		if ($partialpaymentHelper->isPartialPaymentOption2Installments() || $partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
			if ($remaining_installments == 1) {
				$help_tooltip .= '<br />Remaining balance in ' . $remaining_installments . ' installment of ' . Mage::helper('core')->currency($remaining_balance / $remaining_installments, true, false) . ' to be paid in ' . $payment_plan . '.';
			}
			else {
				$help_tooltip .= '<br />Remaining balance in ' . $remaining_installments . ' installments of ' . Mage::helper('core')->currency($remaining_balance / $remaining_installments, true, false) . ' to be paid every ' . $payment_plan . '.';
			}
		}
		else {
			$help_tooltip .= 'Installments to be paid every ' . $payment_plan . '.';
		}

		if ($partialpaymentHelper->isCaptureInstallmentsAutomatically()) {
			$help_tooltip .= '<br />Your CC will be charged automatic.';
			$help_tooltip .= "<br />We don't save your CC details. You are secure.";
		}

		if ($partialpaymentHelper->isPartialPaymentOption2Installments() || $partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
			$help_tooltip .= '<div class="total-to-be-paid">Total to be paid = ' . Mage::helper('core')->currency($product_price, true, false) . '</div>';
		}
		else {
			$help_tooltip .= '<br /><br />';
		}

		$help_tooltip .= '* Final amount varies depending on shipping, tax & other charges.';

		if ($surcharge_value) {
			$help_tooltip .= '<br />* ';
			if ($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
				$help_tooltip .= '<span id="surcharge_value">' . Mage::helper('core')->currency($surcharge_value, true, false) . '</span>';
			}
			else {
				$help_tooltip .= '<span id="surcharge_value">' . $surcharge_value . '</span>%';
			}
			$help_tooltip .= ' surcharge applies to all amount.';
		}
		header('Access-Control-Allow-Origin: *');
		echo $help_tooltip;
	}


	public function removepartialpaymentAction () 
	{
		Mage::getSingleton('core/session')->setRemovePartialPayment(1);
	}


	public function allowpartialpaymentAction () 
	{
		Mage::getSingleton('core/session')->unsRemovePartialPayment();
	}


    function epXmlElementStart ($parser, $tag, $attributes) {
        $this->currentTag = $tag;
    }
    
    function epXmlElementEnd ($parser, $tag) {
        $this->currentTag = "";
    }
    
    function epXmlData ($parser, $cdata) {
        $this->xmlData[$this->currentTag] = $cdata;
    }
	public function partialproductAction()
	{
		$this->loadLayout();     
		$this->renderLayout();
	}
	/*************************************************************************
	********** IdealCheckout Function****************************************/
	protected function fixUrl($sUrl, $bRemoveLanguageCode = false)
	{
		if($bRemoveLanguageCode)
		{
			$sRegex = '/\/[a-z]{2,2}\//';

			while(preg_match($sRegex, $sUrl))
			{
				$sUrl = preg_replace($sRegex, '/', $sUrl);
			}
		}

		// Remove /index.php/ from URL
		while(strpos($sUrl, '/index.php/') !== false)
		{
			$sUrl = str_replace('/index.php/', '/', $sUrl);
		}

		// Remove ___SID from query string
		$sUrl = str_replace('/?___SID=U/', '/', $sUrl);

		// Replace // with /
		$sUrl = substr($sUrl, 0, 10) . str_replace('//', '/', substr($sUrl, 10));

		return $sUrl;
	}
	
	public function returnAction()
	{
		$oIdealcheckoutidealModel = Mage::getModel('idealcheckoutideal/idealcheckoutideal');

		$sOrderId = $this->getRequest()->get('order_id');
		$sOrderCode = $this->getRequest()->get('order_code');
		$partial_payment = Mage::getModel('partialpayment/partialpayment')->load($sOrderId,'order_id');
		
		$reply = $oIdealcheckoutidealModel->validateReturn($sOrderId, $sOrderCode);
		if($reply)
		{
				
			$productModel = Mage::getModel('partialpayment/product')->getCollection()
							->addFieldToFilter('partial_payment_id', $post['partial_payment_id']);
						
			foreach($productModel as $product)
			{
				if($product->getRemainingInstallment()>0)
				{
					$remain = $product->getRemainingAmount()/$product->getRemainingInstallment();
					$product->setRemainingInstallment($product->getRemainingInstallment() - 1);
					$product->setRemainingAmount($product->getRemainingAmount() - $remain);
					$product->setPaidInstallment($product->getPaidInstallment() + 1);
					$product->setPaidAmount($product->getPaidAmount() + $remain);
					$product->save();
					
				}
			}
			
			// Start: For Two Installments: - Update partial_payment_master Table.
			$partial_payment->setPaidAmount(($partial_payment->getPaidAmount() + $post[$post['installment_id']]));
			$partial_payment->setRemainingAmount(($partial_payment->getRemainingAmount() - $post[$post['installment_id']]));
			
			$partial_payment->setUpdatedDate(date('Y-m-d'));
			$partial_payment->setPaidInstallment($partial_payment->getPaidInstallment() + 1);
			$partial_payment->setRemainingInstallment($partial_payment->getRemainingInstallment() - 1);
			$partial_payment->save();
			
			//edited by indies on 2-1-2013 start
			if($partial_payment->getTotalAmount() == $partial_payment->getPaidAmount())
			{
				$partial_payment->setRemainingAmount(0);
				$partial_payment->setPartialPaymentStatus('Complete');
				$partial_payment->setPaidAmount($partial_payment->getTotalAmount());
				$partial_payment->save();
			}
			
			//edited by indies on 2-1-2013 end
			
			$installmentData = Mage::getModel('partialpayment/installment')->getCollection()->addFieldToFilter('partial_payment_id', $post['partial_payment_id'])->addFieldToFilter('installment_status', array('in' => array('Remaining' , 'Canceled')))->getData();

			if (isset($installmentData[0]['installment_id'])) {
				$this->sendEmailSuccess($partial_payment);
				$installmentModel = Mage::getModel('partialpayment/installment')->load($installmentData[0]['installment_id']);

				$installmentModel->setInstallmentPaidDate(date('Y-m-d'));
				$installmentModel->setInstallmentStatus('Paid');
				$installmentModel->setPaymentMethod($post['payment']['method']);
				$installmentModel->setTxnId(Indies_Fee_Model_Fee::$transactionId);
				$installmentModel->save();
			}			

			// Success Message
			Mage::getSingleton('core/session')->addSuccess('Second installment of order # ' . $partial_payment['order_id'] . ' has been done successfully.');
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('partialpayment'));
			Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;
		}
		else
		{
			Mage::getSingleton('core/session')->addError('Invalid Transaction of Data.');
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('partialpayment'));
			Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;
		}
	}


	public function  updatePartialpaymentAmountAction()
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
	    $post_data = Mage::app()->getRequest()->getPost();
	
		$product_price = $calculationHelper->convertCurrencyAmount(floatval($post_data['product_price']));
		if ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments())
		 {
			echo '<select id="allow_partial_payment" name="allow_partial_payment" class="required-entry" style="margin-right:15px;">';
				
			echo '<option selected="selected" value="">' . $this->__('Please Select') . '</option>';
			echo '<option value="0" '.($post_data['select_combo'] == '0' ?'selected="selected"':'').'>' . $this->__('Full Payment of ') . Mage::helper('core')->currency($product_price, true, false) . '</option>';

			for ($i=2;$i<=$partialpaymentHelper->getTotalNoOfInstallment();$i++) {
				$installment_price = $product_price;
				$surcharge_value = 0;

				if ($partialpaymentHelper->isSurchargeOptionsSingleSurcharge()) {
					$surcharge_value = $partialpaymentHelper->getSingleSurchargeValue();								
				}
				elseif ($partialpaymentHelper->isSurchargeOptionsMultipleSurcharge()) {
					$surcharge_value = explode(',', $partialpaymentHelper->getMultipleSurchargeValues());
					$surcharge_value = $surcharge_value[$i-1];
				}

				if ($partialpaymentHelper->isSurchargeOptionsSingleSurcharge() || $partialpaymentHelper->isSurchargeOptionsMultipleSurcharge()) {
					if ($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
						$installment_price += $surcharge_value;
					}
					else {
						$installment_price += (($installment_price * $surcharge_value) / 100);
					}
				}

				echo '<option value="' . $i . '" '.($post_data['select_combo'] == $i ?'selected="selected"':'').'>' . $i . $this->__(' Installments of ') . Mage::helper('core')->currency($installment_price / $i, true, false) . '</option>';
			}
			echo '</select>';
		}
		 header('Access-Control-Allow-Origin: *');
	}
}