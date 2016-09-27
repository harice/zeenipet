<?php
class Indies_Partialpayment_Model_Observer
{
	const XML_PATH_DISPLAY_CART_PRICE       = 'tax/cart_display/price';
    const XML_PATH_DISPLAY_CART_SUBTOTAL    = 'tax/cart_display/subtotal';
    const XML_PATH_DISPLAY_CART_SHIPPING    = 'tax/cart_display/shipping';
    const XML_PATH_DISPLAY_CART_DISCOUNT    = 'tax/cart_display/discount';
    const XML_PATH_DISPLAY_CART_GRANDTOTAL  = 'tax/cart_display/grandtotal';

	const DISPLAY_TYPE_EXCLUDING_TAX = 1;
    const DISPLAY_TYPE_INCLUDING_TAX = 2;
    const DISPLAY_TYPE_BOTH = 3;

	protected $observer_counter = 1;

	public function ProcessInstallmentDueDate($observer)
	{
		$date = Mage::app()->getLocale()->date(new Zend_Date);
		
		$installmentModel = Mage::getModel('partialpayment/installment')->getCollection()
			->addFieldToFilter('installment_due_date',$date->toString('yyyy-MM-dd'))
			->addFieldToFilter('installment_status','Remaining')
			->load();
		
		if(sizeof($installmentModel))
		{
			foreach($installmentModel as $installment)
			{
				$installment->setInstallmentStatus('Due');
				$installment->save();
			}
		}
		
		$installmentModel = Mage::getModel('partialpayment/installment')->getCollection()
			->addFieldToFilter('installment_due_date',array('lt' =>$date->toString('yyyy-MM-dd')))
			->addFieldToFilter('installment_status',array('nin' => array('Paid')))
			->load();
		
		if(sizeof($installmentModel))
		{
			foreach($installmentModel as $installment)
			{
				$installment->setInstallmentStatus('Overdue');
				$installment->save();
			}
		}
	}
	
	public function PartialPaymentStatus($observer)
	{	
			if($observer->getEvent()->getControllerAction()->getFullActionName() == 'paypal_ipn_index')
			{		
					$partial_payment = Mage::getModel('partialpayment/partialpayment')->getCollection();;
					$partial_payment->addFieldToFilter('order_id', $_POST['invoice'] );
					$size = $partial_payment->getSize();
					
					$id=0;
					foreach ($partial_payment as $item)
						$id=$item->getPartialPaymentId();
									 
					if($size)
					{		
						if($_POST['payment_status'] == "Completed")
						{
						
							$partial_payment_save= Mage::getModel('partialpayment/partialpayment')
							->setPartialPaymentStatus('Processing')
							->setPartialPaymentId($id)
							->save();
							
							$installmentModel = Mage::getModel('partialpayment/installment')->getCollection()
							->addFieldToFilter('partial_payment_id',2)
							->addFieldToFilter('transaction_id',$_POST['txn_id'])
							->load();
							$sizeInstallment = $installmentModel->count();
							if($sizeInstallment>0)
							{
								return;
							}
						
							if(isset($_POST['item_name']))//2nd installment
							{
								
								if($_POST['item_name']=='Remaining Amount')
								{
									$partial_payment_order = Mage::getModel('partialpayment/partialpayment')->getCollection()->addFieldToFilter('order_id', $_POST['invoice'])->load();
									$partial_payment_id=0;
									if(sizeof($partial_payment_order)) {
											foreach ($partial_payment_order as $order) {
												$partial_payment_id= $order->getPartialPaymentId();
											}
									}
									$partial_payment = Mage::getModel('partialpayment/partialpayment')->load($partial_payment_id);
									$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
								
									$productModel = Mage::getModel('partialpayment/product')->getCollection()
										->addFieldToFilter('partial_payment_id', $partial_payment_id);
						
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
								
								
								
									$partial_payment->setPaidAmount($partial_payment->getPaidAmount() + $_POST['mc_gross']);
									$partial_payment->setRemainingAmount($partial_payment->getRemainingAmount() - $_POST['mc_gross']);
									$partial_payment->setUpdatedDate(date('Y-m-d'));
									$partial_payment->setPaidInstallment($partial_payment->getPaidInstallment() + 1);
									$partial_payment->setRemainingInstallment($partial_payment->getRemainingInstallment() - 1);
									$partial_payment->save();
									
									if($partial_payment->getPaidInstallment() == $partial_payment->getTotalInstallment()){
										$partial_payment->setRemainingAmount(0);
										$partial_payment->setPartialPaymentStatus('Complete');
										$partial_payment->setPaidAmount($partial_payment->getTotalAmount());
										$partial_payment->save();
									}
			
									$installmentData = Mage::getModel('partialpayment/installment')->getCollection()->addFieldToFilter('partial_payment_id',$partial_payment_id)->addFieldToFilter('installment_status', 'Remaining')->getData();
						
									if (isset($installmentData[0]['installment_id'])) {
										$installmentModel = Mage::getModel('partialpayment/installment')->load($installmentData[0]['installment_id']);
										$installmentModel->setInstallmentPaidDate(date('Y-m-d'));
										$installmentModel->setInstallmentStatus('Paid');
										$installmentModel->setPaymentMethod('paypal_standard');
										$installmentModel->setTxnId($_POST['txn_id']);
										$installmentModel->save();
									}	
								}//if remaning over
							}//2nd installment over
						
						
							$partial = Mage::getModel('partialpayment/partialpayment')->load($id);
							$storeId = Mage::app()->getStore()->getId();
							$customerName = $partial->getCustomerFirstName() . ' ' . $partial->getCustomerLastName();
							$incrementId = $partial->getOrderId();

							$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

							// Mail Data
							$data = array();
							$data['frontend_label'] = $partialpaymentHelper->getFrontendLabel();
							$data['customer_name'] = $customerName;
							$data['order_id'] = $incrementId;
							$data['result'] = 'Successful';

							// Email Template 
							$translate = Mage::getSingleton('core/translate');
							$translate->setTranslateInline(false);
							$mailTemplate = Mage::getModel('core/email_template');

							$copyTo = $partialpaymentHelper->getInstallmentConfirmationEmailCCTo();
							$copyMethod = 'bcc';
			
							$emailInfo = Mage::getModel('core/email_info');
							$emailInfo->addTo($partial->getCustomerEmail(), $customerName);
			
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
									Mage::log("errrrr ");
									throw new Exception();
									
								}
								$translate->setTranslateInline(true);
							
							//mail code end here
						}
					}
			}
	}
	public function beforeAddToCart ($observer)
    {
		
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		
		if($partialpaymentHelper->isApplyToWholeCart ())
		{
			$store = new Mage_Core_Model_Config();
			$store->saveConfig('partialpayment/functionalities_group/apply_partial_to_wholecart',1 , 'default', 0);
		}
		else
		{
			$store = new Mage_Core_Model_Config();
			$store->saveConfig('partialpayment/functionalities_group/apply_partial_to_wholecart',0 , 'default', 0);
		}
		
		$request = Mage::app()->getFrontController()->getRequest();
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		
		if(!isset($_POST['allow_partial_payment']))
		{
			$productId = $request->getParam('product');
			$product = Mage::getModel('catalog/product')->load($productId);
			$enable = $partialpaymentHelper->isEnabled();
			$enableSurcharge = $partialpaymentHelper->isEnabledWithSurcharge();
			if ( $enable || $enableSurcharge )
			{
				if ($partialpaymentHelper->isApplyToAllProducts())
				{
					if ($partialpaymentHelper->isPartialPaymentOptional())
					{
							$url = $product->getProductUrl()."?options=cart";
							Mage::app()->getFrontController()->getResponse()->setRedirect($url);
							Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;	
					}else{
						if($enable)
						$request->setParam('allow_partial_payment', 1);
						else
						$request->setParam('surcharge_installments', 0);
						
					}
				}
				elseif ($partialpaymentHelper->isApplyToSpecificProductsOnly() && $partialpaymentHelper->allowPartialPayment($product)){
					if ($partialpaymentHelper->isPartialPaymentOptional()) 
					{
							$url = $product->getProductUrl()."?options=cart";
							Mage::app()->getFrontController()->getResponse()->setRedirect($url);
							Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;
						
					}else{
						if($enable)
						$request->setParam('allow_partial_payment', 1);
						else
						$request->setParam('surcharge_installments', 0);
					}
				}
				elseif($partialpaymentHelper->isApplyToOutOfStockProducts() && $partialpaymentHelper->isOutOfStockProduct($productId)){
					if ($partialpaymentHelper->isPartialPaymentOptional()) 
					{
							$url = $product->getProductUrl()."?options=cart";
							Mage::app()->getFrontController()->getResponse()->setRedirect($url);
							Mage::app()->getFrontController()->getResponse()->sendResponse(); exit;
						
					}else{
						if($enable)
						$request->setParam('allow_partial_payment', 1);
						else
						$request->setParam('surcharge_installments', 0);
					}
				
				}
			}
		}
	}


	public function sendEmailSuccess($customer_first_name, $customer_last_name, $customer_email, $order_id, $partial_payment_id)
	{
		try
		{
			$storeId = Mage::app()->getStore()->getId();
			$customerName = $customer_first_name." ".$customer_last_name;
			$incrementId = $order_id;
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

			// Mail Data
			$partialpayment_installment_grid = '';

			if ($partial_payment_id) {
				$partialpayment_installments_collection = Mage::getModel('partialpayment/installment')->getCollection()
															->addFieldToFilter('partial_payment_id', array('eq' => $partial_payment_id));
				$partialpayment_installments_collection = $partialpayment_installments_collection->getData();

				if (sizeof($partialpayment_installments_collection)) {
					$partialpayment_installment_grid .= '<table border="1" cellpadding="3" cellspacing="3">';
					$partialpayment_installment_grid .= '<tr>';
					$partialpayment_installment_grid .= '<th>Installment No.</th>';
					$partialpayment_installment_grid .= '<th>Installment Amount</th>';
					$partialpayment_installment_grid .= '<th>Due Date</th>';
					$partialpayment_installment_grid .= '<th>Paid Date</th>';
					$partialpayment_installment_grid .= '<th>Installment Status</th>';
					$partialpayment_installment_grid .= '</tr>';

					$i = 0;
					$j = 1;

					for($i=0;$i<sizeof($partialpayment_installments_collection);$i++) {
						$partialpayment_installment_grid .= '<tr>';
						$partialpayment_installment_grid .= '<td class="align_center">' . $j . '</td>';
						$j = $j + 1;
						$partialpayment_installment_grid .= '<td class="align_right">' . Mage::helper('checkout')->formatPrice($partialpayment_installments_collection[$i]['installment_amount']) . '</td>';
						$partialpayment_installment_grid .= '<td class="align_center">' . $partialpayment_installments_collection[$i]['installment_due_date'] . '</td>';
						$partialpayment_installment_grid .= '<td class="align_center">' . $partialpayment_installments_collection[$i]['installment_paid_date'] . '</td>';
						$partialpayment_installment_grid .= '<td class="align_center">' . $partialpayment_installments_collection[$i]['installment_status'] . '</td>';
						$partialpayment_installment_grid .= '</tr>';
					}
					$partialpayment_installment_grid .= '</table>';
				}
			}

			$data = array();
			$data['frontend_label'] = $partialpaymentHelper->getFrontendLabel();
			$data['customer_name'] = $customerName;
			$data['order_id'] = $incrementId;
			$data['store_url'] = Mage::getBaseUrl();
			$data['login_url'] = Mage::getUrl('partialpayment/index/index');
			$data['partialpayment_installment_grid'] = $partialpayment_installment_grid;

			// Email Template 
			$translate = Mage::getSingleton('core/translate');
			$translate->setTranslateInline(false);
			$mailTemplate = Mage::getModel('core/email_template');
			$copyTo = $partialpaymentHelper->getOrderConfirmationEmailCCTo();
			$copyMethod = 'bcc';

			$emailInfo = Mage::getModel('core/email_info');
			$emailInfo->addTo($customer_email, $customerName);

			$sender = $partialpaymentHelper->getOrderConfirmationSender();
			$template = $partialpaymentHelper->getOrderConfirmationTemplate();		
			if ($copyTo && $copyMethod == 'bcc') {
				// Add bcc to customer email
				$emailInfo->addBcc($copyTo);
			}
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

			return;
		}
		catch(Exception $e){
			Mage::log("Exception" . $e);
			
			return;
		}
	}
    public function addProductToCart ($observer)
    {
		$data = $observer->getRequest()->getPost();
			
		if (isset($data['super_group'])) {
			if (isset($data['allow_partial_payment'])){
				$session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
				$session->setData($data['product'], $data['allow_partial_payment']);
			}
		}

		if (isset($data['allow_partial_payment'])) {
			$product = Mage::getModel('catalog/product')->load($data['product']);
			$product->addCustomOption('allow_partial_payment', $data['allow_partial_payment']);
		}
    }


	public function getSingleSurchargeCalculation($item, $allow_partial_payment = '')
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$surchargeValue = $partialpaymentHelper->getSingleSurchargeValue();
		$downPayment = $partialpaymentHelper->getFirstInstallmentAmount();
		$product = array();
		$outOfStockDiscount = 0;

		if($item->getPrice() > 0)
		{
			if ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments() && $allow_partial_payment) {
				if ($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
					if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
						$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
						$productPriceWithSurcharge = (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surchargeValue;
						$product['surcharge_amount'] = $surchargeValue;
						$product['calculationType'] = 'Fixed Amount Value';
						$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
						$downPayment = $productPriceWithSurcharge / $allow_partial_payment;
						$product['downpayment'] = $downPayment;
						$product['productRemaningAmount'] = $productPriceWithSurcharge - $downPayment;
						return  $product;
					}
					elseif (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
						$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
						$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surchargeValue;
						$product['surcharge_amount'] = $surchargeValue;
						$product['calculationType'] = 'Fixed Amount Value';
						$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
						$downPayment = $productPriceWithSurcharge / $allow_partial_payment;
						$product['downpayment'] = $downPayment;
						$product['productRemaningAmount'] = $productPriceWithSurcharge - $downPayment;
						return  $product;
					}
					else {
						$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
						$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surchargeValue;
						$product['surcharge_amount'] = $surchargeValue;
						$product['calculationType'] = 'Fixed Amount Value';
						$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
						$downPayment = $productPriceWithSurcharge / $allow_partial_payment;
						$product['downpayment'] = $downPayment;
						$product['productRemaningAmount'] = $productPriceWithSurcharge - $downPayment;
						return  $product;
					}
				}
				else {
					if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
						$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
						$surcharge_amount = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
						$productPriceWithSurcharge = (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
						$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
						$surchargeFee = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $allow_partial_payment;
						$product['downpayment'] = $surchargeFee;
						$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
						return $product;
					}
					elseif(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX){
						$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
						$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
						$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
						$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
						$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $allow_partial_payment;
						$product['downpayment'] = $surchargeFee;
						$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
						return $product;
						
					}
					else {
						$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
						$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
						$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
						$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
						$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $allow_partial_payment;
						$product['downpayment'] = $surchargeFee;
						$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
						return $product;
					}
				}
			}
			else {
				if($partialpaymentHelper->isInstallmentCalculationTypeFixed()){
					$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
					$productPriceWithSurcharge = (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surchargeValue;
					$product['surcharge_amount'] = $surchargeValue;
					$product['calculationType'] = 'Fixed Amount Value';
					$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
					$downPayment = $partialpaymentHelper->getFirstInstallmentAmount();
					$product['downpayment'] = $downPayment ;
					$product['productRemaningAmount'] = $productPriceWithSurcharge - $downPayment;
					return  $product;
				}
				else{
					$product['surcharge_amount'] = $surchargeValue;
					$product['calculationType'] = 'Percentage Value';
					if(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX){
						$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
						$surcharge_amount = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
						$productPriceWithSurcharge = (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
						$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
						$surchargeFee = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
						$product['downpayment'] = $surchargeFee;
						$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
						return $product;
					}
					elseif(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX){
						$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
						$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
						$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
						$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
						$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
						$product['downpayment'] = $surchargeFee;
						$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
						return $product;
						
					}
					else {
						$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
						$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValue) / 100;
						$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
						$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
						$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
						$product['downpayment'] = $surchargeFee;
						$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
						return $product;
					}
				}
			}
	  }
	}


	public function getMultipleSurchargeCalculation($value, $item)
	{
			$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
			$calculationHelper = Mage::helper('partialpayment/calculation');
			$downPayment = $partialpaymentHelper->getFirstInstallmentAmount();
			$surchargeValues = $partialpaymentHelper->getMultipleSurchargeValues();
			$surchargeValues = explode(",", $surchargeValues);
			$surchargeValues = array_filter($surchargeValues, 'strlen');
			$surchargeValues = array_combine(range(1, count($surchargeValues)), array_values($surchargeValues));
			$product =array();
			$outOfStockDiscount = 0;

			if($item->getPrice() > 0)
			{
				if ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments() && $value) {
					if ($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
						if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
							$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
							$productPriceWithSurcharge = (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surchargeValues[$value];
							$product['surcharge_amount'] = $surchargeValues[$value];
							$product['calculationType'] = 'Fixed Amount Value';
							$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
							$downPayment = $productPriceWithSurcharge / $value;
							$product['downpayment'] = $downPayment;
							$product['productRemaningAmount'] = $productPriceWithSurcharge - $downPayment;
							return  $product;
						}
						elseif (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX) {
							$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
							$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surchargeValues[$value];
							$product['surcharge_amount'] = $surchargeValues[$value];
							$product['calculationType'] = 'Fixed Amount Value';
							$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
							$downPayment = $productPriceWithSurcharge / $value;
							$product['downpayment'] = $downPayment;
							$product['productRemaningAmount'] = $productPriceWithSurcharge - $downPayment;
							return  $product;
						}
						else {
							$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
							$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surchargeValues[$value];
							$product['surcharge_amount'] = $surchargeValues[$value];
							$product['calculationType'] = 'Fixed Amount Value';
							$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
							$downPayment = $productPriceWithSurcharge / $value;
							$product['downpayment'] = $downPayment;
							$product['productRemaningAmount'] = $productPriceWithSurcharge - $downPayment;
							return  $product;
						}
					}
					else {
						if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX) {
							$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
							$surcharge_amount = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
							$productPriceWithSurcharge = (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
							$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
							$surchargeFee = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $value;
							$product['downpayment'] = $surchargeFee;
							$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
							return $product;
						}
						elseif(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX){
							$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
							$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
							$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
							$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
							$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $value;
							$product['downpayment'] = $surchargeFee;
							$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
							return $product;
						}
						else {
							$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);
							$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
							$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
							$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
							$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) / $value;
							$product['downpayment'] = $surchargeFee;
							$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
							return $product;
						}
					}
				}
				else {
					if($partialpaymentHelper->isInstallmentCalculationTypeFixed())
					{
							$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);	
							$productPriceWithSurcharge = (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surchargeValues[$value];
							$product['surcharge_amount'] = $surchargeValues[$value];
							$product['calculationType'] = 'Fixed Amount Value';
							$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
							$downPayment = $partialpaymentHelper->getFirstInstallmentAmount();
							$product['downpayment'] = $downPayment;
							$product['productRemaningAmount'] = $productPriceWithSurcharge - $downPayment;
							return $product;
					}
					else{
							$product['surcharge_amount'] = $surchargeValues[$value];
							$product['calculationType'] = 'Percentage Value';
							if(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_EXCLUDING_TAX){
								$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);					
								$surcharge_amount = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
								$productPriceWithSurcharge = (($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
								$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
								$surchargeFee = ((($item->getPrice() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
								$product['downpayment'] = $surchargeFee;
								$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
								return $product;
							}
							elseif(Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_PRICE) == self::DISPLAY_TYPE_INCLUDING_TAX){
								$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);	
								$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
								$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
								$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
								$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
								$product['downpayment'] = $surchargeFee;
								$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
								return $product;
							}
							else{
								$outOfStockDiscount = $calculationHelper->getOutstockDiscountByItem($item);	
								$surcharge_amount = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) * $surchargeValues[$value]) / 100;
								$productPriceWithSurcharge = (($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount;
								$product['productPriceWithSurcharge'] = $productPriceWithSurcharge;
								$surchargeFee = ((($item->getPriceInclTax() - $outOfStockDiscount) * $item->getQty()) + $surcharge_amount) * $downPayment/100;
								$product['downpayment'] = $surchargeFee;
								$product['productRemaningAmount'] = $productPriceWithSurcharge - $surchargeFee;
								return $product;
							}
					}
				}
		}
	}


	public function surchargeCalculation($allow_partial_payment, $item)
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		if ($partialpaymentHelper->isPartialPaymentOption2Installments())
		{
			if ($partialpaymentHelper->isSurchargeOptionsSingleSurcharge()) {
				return $this->getSingleSurchargeCalculation($item);
			}
			elseif ($partialpaymentHelper->isSurchargeOptionsMultipleSurcharge()) {
				return $this->getMultipleSurchargeCalculation(2,$item);
			}
		}
		elseif ($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
			if($partialpaymentHelper->isSurchargeOptionsSingleSurcharge()) {
				return $this->getSingleSurchargeCalculation($item);
			}		
			elseif ($partialpaymentHelper->isSurchargeOptionsMultipleSurcharge()) {
				$totalTotal = $partialpaymentHelper->getTotalNoOfInstallment();
				return $this->getMultipleSurchargeCalculation($totalTotal,$item);
			}
		}
		elseif ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
			if($partialpaymentHelper->isSurchargeOptionsSingleSurcharge()) {
				return $this->getSingleSurchargeCalculation($item, $allow_partial_payment);
			}
			elseif ($partialpaymentHelper->isSurchargeOptionsMultipleSurcharge()) {
				return $this->getMultipleSurchargeCalculation($allow_partial_payment,$item);
			}
		}
	}


	public function salesOrderSaveBefore ($observer)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$order = $observer->getEvent()->getOrder();

		if (Mage::getStoreConfig(self::XML_PATH_DISPLAY_CART_SUBTOTAL) == self::DISPLAY_TYPE_INCLUDING_TAX && $this->observer_counter == 1) {
			if ($order->getSubtotal() == $order->getSubtotalInclTax()) {
				$order->setFeeAmount($order->getFeeAmount() - (Indies_Fee_Model_Fee::$subTotal - $order->getSubtotal()));
			}
			$order->setSubtotal($order->getSubtotal() + Indies_Fee_Model_Fee::$surchargeAmt);
			$order->setSubtotalInclTax($order->getSubtotalInclTax() + Indies_Fee_Model_Fee::$surchargeAmt);
			$this->observer_counter++;
		}

		$order->setDepositAmount($calculationHelper->convertCurrencyAmount($order->getGrandTotal() - $order->getFeeAmount()));
		Indies_Deposit_Model_Sales_Quote_Address_Total_Deposit::$deposit = $order->getDepositAmount();

		$order->setBaseSubtotal($calculationHelper->convertCurrencyAmount($order->getSubtotal()));
		$order->setBaseSubtotalInclTax($calculationHelper->convertCurrencyAmount($order->getSubtotalInclTax()));
		$order->setBaseGrandTotal($calculationHelper->convertCurrencyAmount($order->getGrandTotal()));
		$order->setBaseDepositAmount($calculationHelper->convertCurrencyAmount($order->getDepositAmount()));
		$order->setBaseFeeAmount($calculationHelper->convertCurrencyAmount($order->getFeeAmount()));
		$order->setBaseOutstockDiscountAmount($calculationHelper->convertCurrencyAmount($order->getOutstockDiscountAmount()));

		/*Mage::log('Subtotal ' . $order->getSubtotal());
		Mage::log('Base Subtotal ' . $order->getBaseSubtotal());
		Mage::log('Subtotal Incl Tax ' . $order->getSubtotalInclTax());
		Mage::log('Base Subtotal Incl Tax ' . $order->getBaseSubtotalInclTax());
		Mage::log('Grand Total ' . $order->getGrandTotal());
		Mage::log('Base Grand Total ' . $order->getBaseGrandTotal());
		Mage::log('Deposit Amount ' . $order->getDepositAmount());
		Mage::log('Base Deposit Amount ' . $order->getBaseDepositAmount());
		Mage::log('Fee Amount ' . $order->getFeeAmount());
		Mage::log('Base Fee Amount ' . $order->getBaseFeeAmount());
		Mage::log('Outstock Discount Amount ' . $order->getOutstockDiscountAmount());
		Mage::log('Base Outstock Discount Amount ' . $order->getBaseOutstockDiscountAmount());
		Mage::log('observer_counter ' . $this->observer_counter);*/

		//Mage::throwException('Sorry...');
	}


	public function salesOrderPlaced($observer)
	{
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$wholecart = $partialpaymentHelper->isApplyToWholeCart();
		$order = $observer->getEvent()->getOrder();
		$partial_payment_id = "";

		if ($order->getFeeAmount()) {
			$partial_payment_amount = $order->getFeeAmount();			

			if($order->getCustomerId())
			{
				$customer_id = $order->getCustomerId();
				$customer_first_name = $order->getCustomerFirstname();
				$customer_last_name = $order->getCustomerLastname();
				$customer_email = $order->getCustomerEmail();
			}
			else
			{
				$customer_id = 0;
				$customer_first_name = $order->getBillingAddress()->getFirstname();
				$customer_last_name = $order->getBillingAddress()->getLastname();
				$customer_email = $order->getBillingAddress()->getEmail();
			}

			$order_id = $order->getIncrementId();
			$total_amount = $order->getGrandTotal();
			$paid_amount = $order->getDepositAmount();
			$remaining_amount = $order->getFeeAmount();
			$payment_method = $order->getPayment()->getMethod();

			if($payment_method != 'paypal_standard') {
				if($payment_method == 'atos_standard') {
					$partial_payment_status = 'Pending';
				}
				else {
					$partial_payment_status = 'Processing';
				}
			}
			else {
				$partial_payment_status = 'Pending';
			}

			$created_date = date('Y-m-d');
			$updated_date = date('Y-m-d');
			$paid_installment = 1;

			if ($partialpaymentHelper->isPartialPaymentOption2Installments()) {
				$total_installment = 2;
			}
			elseif ($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
				$total_installment = $partialpaymentHelper->getTotalNoOfInstallment();
			}
			else {
				$total_installment = 2;
			}

			$remaining_installment = $total_installment - $paid_installment;

			if ($partialpaymentHelper->isEnabledWithSurcharge()) {
				$enabled_with_surcharge = 1;
			}
			else {
				$enabled_with_surcharge = 0;
			}

			if($payment_method == 'paypal_standard') {
				$partialpaymentModelData = array('order_id' => $order_id, 'customer_id' => $customer_id, 'customer_first_name' => $customer_first_name, 'customer_last_name' => $customer_last_name, 'customer_email' => $customer_email, 'total_amount' => $total_amount, 'paid_amount' => 0, 'remaining_amount' => $total_amount,'created_date' => $created_date, 'updated_date' => $updated_date, 'paid_installment' => 0,'total_installment' =>$total_installment,'remaining_installment' =>$total_installment,'partial_payment_status' => $partial_payment_status,'enabled_with_surcharge' => $enabled_with_surcharge );
			}
			else {
				$partialpaymentModelData = array('order_id' => $order_id, 'customer_id' => $customer_id, 'customer_first_name' => $customer_first_name, 'customer_last_name' => $customer_last_name, 'customer_email' => $customer_email, 'total_amount' => $total_amount, 'paid_amount' => $paid_amount, 'remaining_amount' => $remaining_amount,'created_date' => $created_date, 'updated_date' => $updated_date, 'paid_installment' => $paid_installment,'total_installment' =>$total_installment,'remaining_installment' =>$remaining_installment,'partial_payment_status' => $partial_payment_status,'enabled_with_surcharge' => $enabled_with_surcharge );
			}

			$partialpaymentModel = Mage::getModel('partialpayment/partialpayment')->setData($partialpaymentModelData);

			try {
				$partialpaymentModel->save();
				$partial_payment_id = $partialpaymentModel->getId();
			} catch (Exception $e) {
				Mage::log("Exception". $e);
			}

			//Shipping,Tax & Discount Calculation Start			
			$tax = $order->getTaxAmount();
			$shippingTax = 0;

			if($tax > 0) {
				$shippingTax = (float) $order->getShippingAmount() + $tax;
			}
			else {
				$shippingTax = (float) $order->getShippingAmount();
			}

			$shippingTaxFinal = 0;
			$discount = 0;

			if($order->getShippingAmount() > 0) {
				$shippingTaxFinal = $calculationHelper->shippingTaxCalculation($shippingTax);
			}

			if(abs($order->getDiscountAmount()) > 0) {
				$discount = $calculationHelper->discountCalculation(abs($order->getDiscountAmount()));
			}
			//Shipping,Tax & Discount Calculation End

			// Start: Surcharge Facility
			if($partialpaymentHelper->isEnabledWithSurcharge())
			{
				$items = $order->getQuote()->getAllItems();
				$installmentValue = 0;

				foreach ($items as $item) {
					$allow_partial_payment = 0;	
					if ($partialpaymentHelper->isApplyToWholeCart())
					{
						if ($partialpaymentHelper->isPartialPaymentOption2Installments()) {
							$surcharge_installments = 2;
						}
						elseif($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
							$surcharge_installments = $partialpaymentHelper->getTotalNoOfInstallment();
						}
						elseif($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
							$surcharge_installments = Mage::getSingleton('core/session')->getPP();
						}
						$allow_partial_payment = $surcharge_installments;
						$total_installment = $surcharge_installments;
					}
					else {
						$allow_partial_payment = $partialpaymentHelper->getAllowPartialPayment($item);			
						if($partialpaymentHelper->isPartialPaymentOption2Installments()|| $partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
							$surcharge_installments = $total_installment - 1;
						}
						elseif($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
							$surcharge_installments = $partialpaymentHelper->getAllowPartialPayment($item);
							$allow_partial_payment = $surcharge_installments;
							$surcharge_installments = $surcharge_installments - 1;
						}
					}

					if ($allow_partial_payment)	{
						$productValue = $this->surchargeCalculation($allow_partial_payment,$item);

						if($partialpaymentHelper->isApplyToWholeCart())	{
							$itemId = 0;	
							$productId = 0;
							$productName = "WholeCart";
							$totalAmount = 	$order->getGrandTotal();
							$paidAmount = $order->getDepositAmount();
							$remainAmount = $order->getFeeAmount();
						} else {
							$QuoteItemId = $item->getId();	
							$quote_item  = Mage::getModel('sales/order_item') ->getCollection()
										   ->addFieldToFilter('quote_item_id',$QuoteItemId);

							$itemId = 0;

							foreach($quote_item as $quoteitem) {
								$itemId = $quoteitem->getItemId();
							}

							$productId = $item->getProductId();
							$productName = $item->getName();
							$totalAmount = 	$productValue['productPriceWithSurcharge'];
							$paidAmount = $productValue['downpayment'];
							$remainAmount = $productValue['productRemaningAmount'];
							$productPrice = $item->getPrice();

							if($productPrice == 0) {
								$totalAmount = $paidAmount = $remainAmount = $totalInstallments = $paidInstallments = $remaining_installment = 0;
							}
						}

						if($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
							$totalInstallments = $allow_partial_payment;
						}
						else {
							$totalInstallments = $total_installment;
						}

						$paidInstallments = 1;
						$remaining_installment = $totalInstallments	- $paidInstallments;
						$surchargeCalculationType = $productValue['calculationType'] ;	
						$surcharge_amount = $productValue['surcharge_amount'];

						if($totalAmount == 0) {
							$totalInstallments = $paidInstallments = $remaining_installment = $surcharge_amount = 0;
						}

						$_product = Mage::getModel('catalog/product')->load($productId);
						$productType = $_product->getTypeId();											

						$outOfStockDiscountValue = 0;
						$outOfStockDiscountCalculationType = '';
						$is_out_of_stock = 0;

						if($partialpaymentHelper->isOutOfStockProduct($item->getProductId()))
						{
							$is_out_of_stock = 1;
							$outOfStockDiscountValue = $partialpaymentHelper->getPreOrderDiscount();
							if ($partialpaymentHelper->isPreOrderDiscountCalculationTypeFixedAmount()) {
								$outOfStockDiscountCalculationType = 'Fixed Amount Value';
							} else {
								$outOfStockDiscountCalculationType = 'Percentage Value';
							}
						}

						$productModelData = array('partial_payment_id'=>$partial_payment_id,'order_id' => $order_id,
						'sales_flat_order_item_id'=> $itemId,'product_id'=>$productId,
						'product_name'=>$productName,'product_type'=>$productType,
						'total_installment'=>$totalInstallments,'paid_installment'=>$paidInstallments,
						'remaining_installment'=>$remaining_installment,'total_amount'=>$totalAmount,
						'paid_amount'=>$paidAmount,'remaining_amount'=>$remainAmount,
						'surcharge_value'=>$surcharge_amount,'surcharge_calculation_type'=>$surchargeCalculationType,'out_of_stock_discount_value'=>$outOfStockDiscountValue,'out_of_stock_discount_calculation_type'=>$outOfStockDiscountCalculationType,'is_out_of_stock'=>$is_out_of_stock);

						$productModel = Mage::getModel('partialpayment/product')->setData($productModelData);

						try {
							$productModel->save();
							$product_id = $productModel->getId();
						} catch (Exception $e) {
							Mage::log("Exception". $e);
						}
					}

					if($partialpaymentHelper->isApplyToWholeCart()) {
						break;
					}
				}

				//$this->sendEmailSuccess($customer_first_name, $customer_last_name, $customer_email, $order_id, $partial_payment_id);

				// Start: Insert Data into partial_payment_installment_master Table.
				$first_installment_amount = $order->getDepositAmount();

				$installment_due_date = date('Y-m-d');
				$first_installment_status = 'Paid';

				$installment_date = date('Y-m-d');
				$payment_method = $order->getPayment()->getMethod();

				$arr = array();

				foreach($items as $item)
				{
					$allow_partial_payment = 0;	
					if($partialpaymentHelper->isApplyToWholeCart())
					{
						if ($partialpaymentHelper->isPartialPaymentOption2Installments()) {
							$surcharge_installments = 2;
						}
						elseif ($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
							$surcharge_installments = $partialpaymentHelper->getTotalNoOfInstallment();
						}
						elseif ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
							$surcharge_installments = Mage::getSingleton('core/session')->getPP();
						}
						$allow_partial_payment = $surcharge_installments;
						$surcharge_installments = $surcharge_installments - 1;
					} else {
						$allow_partial_payment = $partialpaymentHelper->getAllowPartialPayment($item);
						if ($partialpaymentHelper->isPartialPaymentOption2Installments()|| $partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
							$surcharge_installments = $total_installment - 1;
						}
						elseif ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
							$surcharge_installments = $partialpaymentHelper->getAllowPartialPayment($item);
							$allow_partial_payment = $surcharge_installments;
							$surcharge_installments = $surcharge_installments - 1;
						}
					}

					if($allow_partial_payment)
					{
						if($partialpaymentHelper->isApplyToWholeCart() && $partialpaymentHelper->isInstallmentCalculationTypeFixed())
						{
							if ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
								$productValue = $this->surchargeCalculation($allow_partial_payment, $item);
								$remainAmount = $productValue['productRemaningAmount'];

								for($j=0;$j<$surcharge_installments;$j++) {
									if (isset($arr[$j])) {
										$arr[$j] += $remainAmount / $surcharge_installments;
									}
									else {
										$arr[$j] = $remainAmount / $surcharge_installments;
									}
								}
							}
							else {
								if($partialpaymentHelper->isSurchargeOptionsSingleSurcharge())
								{
									$surchargeValue = $partialpaymentHelper->getSingleSurchargeValue();
									$surcharge = $order->getSubtotal() - $partialpaymentHelper->getFirstInstallmentAmount();
									$remainAmount = $surcharge;
									for($j=0;$j<$surcharge_installments;$j++) {
										if (isset($arr[$j])) {
											$arr[$j] += $remainAmount / $surcharge_installments;
										}
										else {
											$arr[$j] = $remainAmount / $surcharge_installments;
										}
									}
									break;			
								}
								elseif($partialpaymentHelper->isSurchargeOptionsMultipleSurcharge())
								{
									$surchargeValues = $partialpaymentHelper->getMultipleSurchargeValues();
									$surchargeValues = explode(",", $surchargeValues);
									$surchargeValues = array_filter($surchargeValues, 'strlen');
									$surchargeValues = array_combine(range(1, count($surchargeValues)), array_values($surchargeValues));
									if ($partialpaymentHelper->isPartialPaymentOption2Installments()) {
										$total_installments = 2;
									}
									elseif ($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
										$total_installments = $partialpaymentHelper->getTotalNoOfInstallment();
									}
									elseif ($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
										$total_installments = Mage::getSingleton('core/session')->getPP();
									}

									$surchargeValue = $surchargeValues[$total_installments];
									$surcharge =  $order->getSubtotal() - $partialpaymentHelper->getFirstInstallmentAmount();
									$remainAmount = $surcharge;

									for($j=0;$j<$surcharge_installments;$j++) {
										if (isset($arr[$j])) {
											$arr[$j] += $remainAmount / $surcharge_installments;
										}
										else {
											$arr[$j] = $remainAmount / $surcharge_installments;
										}
									}
									break;
								}
							}
						}
						else {
							$productValue = $this->surchargeCalculation($allow_partial_payment,$item);
							$remainAmount = $productValue['productRemaningAmount'];
							for($j=0;$j<$surcharge_installments;$j++) {
								if (isset($arr[$j])) {
									$arr[$j] += $remainAmount / $surcharge_installments;
								}
								else {
									$arr[$j] = $remainAmount / $surcharge_installments;
								}
							}
						}
					}
				}

				$arr = array_combine(range(1, count($arr)), array_values($arr));	

				$total_installment = count($arr) + 1;

				$partialpaymentModel = Mage::getModel('partialpayment/partialpayment')->load($partial_payment_id);
				$partialpaymentModel->setTotalInstallment($total_installment);
				$partialpaymentModel->setRemainingInstallment($total_installment-1);
				$partialpaymentModel->save();

				for ($i=1;$i<=$total_installment;$i++) {
					if ($i == 1)
     				{
       					if($payment_method == 'paypal_standard')
       					{
         					$installmentModelData = array('partial_payment_id' => $partial_payment_id, 'installment_amount' => $first_installment_amount, 'installment_due_date' => $installment_due_date, 'installment_status' => 'Remaining');
       					}
       					else
       					{ 
         					$installmentModelData = array('partial_payment_id' => $partial_payment_id, 'installment_amount' => $first_installment_amount, 'installment_due_date' => $installment_due_date, 'installment_paid_date' => $installment_due_date, 'installment_status' => 'Paid', 'payment_method'=>$payment_method);
         				}
        			}
					else {
						$installmentAmount = $arr[$i-1];

						if ($shippingTaxFinal > 0) {
							$shippingTaxFinalValue = $shippingTaxFinal/($total_installment - 1);
							$installmentAmount = $arr[$i-1] + $shippingTaxFinalValue;
						}
						if($discount > 0) {
							$discountValue = $discount / ($total_installment - 1);
							$installmentAmount = $installmentAmount - $discountValue;
						}

						// next installment date calculation
						$date = new Zend_Date();

						if($partialpaymentHelper->isPaymentPlanDays()) {						
							$days = $partialpaymentHelper->getPaymentPlanTotalNoOfDays() * ($i-1); 			
							$date->addDay($days);
						} elseif ($partialpaymentHelper->isPaymentPlanWeekly()) {
							$date->addWeek(($i-1));
						} elseif ($partialpaymentHelper->isPaymentPlanMonthly()) {
							$date->addMonth(($i-1));
						}
	
						$installmentModelData = array('partial_payment_id' => $partial_payment_id, 'installment_amount' => $installmentAmount, 'installment_due_date' => $date->toString('yyyy-MM-dd'),'installment_status' => 'Remaining');
					}

					$installmentModel = Mage::getModel('partialpayment/installment')->setData($installmentModelData);

					try {
						$installmentModel->save();
					} catch (Exception $e) {
						Mage::log("Exception". $e);
					}
				}
			}
			// Start: Partial Payment Without Surcharge Facility
			else
			{
				$items = $order->getQuote()->getAllItems();
				$installmentValue = 0;

				foreach($items as $item) {
					$calculationHelper = Mage::helper('partialpayment/calculation');
					$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

					$allow_partial_payment = 0;

					if ($partialpaymentHelper->isApplyToWholeCart()) {
						if($partialpaymentHelper->isPartialPaymentOption2Installments()) {
							$surcharge_installments = 2;
						}
						elseif($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
							$surcharge_installments = $partialpaymentHelper->getTotalNoOfInstallment();
						}
						elseif($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
							$surcharge_installments = Mage::getSingleton('core/session')->getPP();
						}
						$allow_partial_payment = $surcharge_installments;
						$totalInstallments = $surcharge_installments;
					} else {
						$allow_partial_payment = $partialpaymentHelper->getAllowPartialPayment($item);			
						if($partialpaymentHelper->isPartialPaymentOption2Installments()|| $partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
							$surcharge_installments = $total_installment - 1;
							$totalInstallments	= $total_installment;
						} elseif($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
							$surcharge_installments = $partialpaymentHelper->getAllowPartialPayment($item);
							$totalInstallments = $surcharge_installments;
							$allow_partial_payment = $surcharge_installments;
							$surcharge_installments = $surcharge_installments - 1;
						}
					}

					if($allow_partial_payment)
					{
						if ($partialpaymentHelper->isApplyToWholeCart())
						{
							if($partialpaymentHelper->isPartialPaymentOption2Installments()) {
								$surcharge_installments = 2;
							}
							elseif($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
								$surcharge_installments = $partialpaymentHelper->getTotalNoOfInstallment();
							}
							elseif($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
								$totalInstallments =  Mage::getSingleton('core/session')->getPP();
							}
						}

						$paidInstallments = 1;
						$remaining_installment = $totalInstallments	- $paidInstallments;
						$totalAmount = 	$item->getPrice();
						$is_out_of_stock = 0;

						if($partialpaymentHelper->isApplyToWholeCart())
						{
							$itemId = 0;	
							$productId = 0;
							$productName = "WholeCart";
							$totalAmount = 	$order->getGrandTotal();
							$paidAmount = $order->getDepositAmount();
							$remainAmount = $order->getFeeAmount();
							if($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
								$surchargeCalculationType = 'Fixed Amount Value';
							}
							else {
								$surchargeCalculationType = 'Percentage Value';
							}
						} else {
							$QuoteItemId = $item->getId();	
							$quote_item  = Mage::getModel('sales/order_item') ->getCollection()
										   ->addFieldToFilter('quote_item_id',$QuoteItemId);
							$itemId = 0;

							foreach($quote_item as $quoteitem) {
								$itemId = $quoteitem->getItemId();
							}

							$productId = $item->getProductId();
							$productName = $item->getName();

							if($partialpaymentHelper->isOutOfStockProduct($item->getProductId()))
							{
								$is_out_of_stock = 1;
								$outOfStockDiscountValue = $partialpaymentHelper->getPreOrderDiscount();
								if($partialpaymentHelper->isPreOrderDiscountCalculationTypeFixedAmount()) {
										$totalAmount = $totalAmount - $outOfStockDiscountValue;
								} else {
										$totalAmount = $totalAmount - ($totalAmount * $outOfStockDiscountValue/100);
								}
							}

							if($partialpaymentHelper->isInstallmentCalculationTypeFixed()) {
								$paidAmount = $partialpaymentHelper->getFirstInstallmentAmount();
								$surchargeCalculationType = 'Fixed Amount Value';
								$remainAmount = $totalAmount - $paidAmount;
							} else {
								$paidAmount = $totalAmount * $partialpaymentHelper->getFirstInstallmentAmount()/100;
								$surchargeCalculationType = 'Percentage Value';
								$remainAmount = $totalAmount - $paidAmount;
							}

							if($totalAmount==0)
							{
								$paidAmount = $remainAmount = $totalInstallments = $paidInstallments = $remaining_installment = 0;
							}
						}

						$surcharge_amount = 0;

						$_product = Mage::getModel('catalog/product')->load($productId);
						$productType = $_product->getTypeId();
						$outOfStockDiscountValue = 0;
						$outOfStockDiscountCalculationType = '';

						if($partialpaymentHelper->isOutOfStockProduct($item->getProductId()))
						{
							$is_out_of_stock = 1;
							$outOfStockDiscountValue = $partialpaymentHelper->getPreOrderDiscount();
							if($partialpaymentHelper->isPreOrderDiscountCalculationTypeFixedAmount()) {
								$outOfStockDiscountCalculationType = 'Fixed Amount Value';
							} else {
								$outOfStockDiscountCalculationType = 'Percentage Value';		
							}
						}

						$productModelData = array('partial_payment_id'=>$partial_payment_id,'order_id' => $order_id,
						'sales_flat_order_item_id'=> $itemId,'product_id'=>$productId,'product_name'=>$productName,
						'product_type'=>$productType,
						'total_installment'=>$totalInstallments,'paid_installment'=>$paidInstallments,
						'remaining_installment'=>$remaining_installment,'total_amount'=>$totalAmount,
						'paid_amount'=>$paidAmount,'remaining_amount'=>$remainAmount,
						'surcharge_value'=>$surcharge_amount,'surcharge_calculation_type'=>$surchargeCalculationType,'out_of_stock_discount_value'=>$outOfStockDiscountValue ,'out_of_stock_discount_calculation_type'=>$outOfStockDiscountCalculationType,'is_out_of_stock'=>$is_out_of_stock);
						$productModel = Mage::getModel('partialpayment/product')->setData($productModelData);

						try {
							$productModel->save();
							$product_id = $productModel->getId();
						} catch (Exception $e) {
							Mage::log("Exception". $e);
						}
					}

					if($partialpaymentHelper->isApplyToWholeCart()) {
						break;
					}
				}

				//$this->sendEmailSuccess($customer_first_name, $customer_last_name, $customer_email, $order_id, $partial_payment_id);

				$first_installment_amount = $order->getDepositAmount();
				$installment_due_date = date('Y-m-d');
				$first_installment_status = 'Paid';

				$installment_date = date('Y-m-d');
				$payment_method = $order->getPayment()->getMethod();

				$arr = array();

				foreach($items as $item)
				{
					$allow_partial_payment = 0;
					if($partialpaymentHelper->isApplyToWholeCart())
					{
						if($partialpaymentHelper->isPartialPaymentOption2Installments()) {
							$surcharge_installments = 2;
						}
						elseif($partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
							$surcharge_installments = $partialpaymentHelper->getTotalNoOfInstallment();
						}
						elseif($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
							$surcharge_installments = Mage::getSingleton('core/session')->getPP();
						}
						$surcharge_installments = $surcharge_installments - 1;
						$allow_partial_payment = $surcharge_installments;
					} else {
						$allow_partial_payment = $partialpaymentHelper->getAllowPartialPayment($item);	
						if($partialpaymentHelper->isPartialPaymentOption2Installments()|| $partialpaymentHelper->isPartialPaymentOptionFixedInstallments()) {
							$surcharge_installments = $total_installment - 1;
							$allow_partial_payment = $surcharge_installments;
						} elseif($partialpaymentHelper->isPartialPaymentOptionFlexyPayments()) {
							$surcharge_installments = $allow_partial_payment - 1;
							$allow_partial_payment = $surcharge_installments;
						}

						if($allow_partial_payment)
						{
							$remainAmount = $calculationHelper->getFeeByItem($item);
							for($j=0;$j<$allow_partial_payment;$j++) {
								if (isset($arr[$j])) {
									$arr[$j] += $remainAmount / $allow_partial_payment;
								}
								else {
									$arr[$j] = $remainAmount / $allow_partial_payment;
								}
							}	
						}
					}
				}

				if($partialpaymentHelper->isApplyToWholeCart())
				{
					if($allow_partial_payment)
					{
						$remainAmount = $calculationHelper->getCalculationWithoutSurcharge($order->getQuote(),$order->getSubtotal());
						for($j=0;$j<$allow_partial_payment;$j++) {
							if (isset($arr[$j])) {
								$arr[$j] += $remainAmount / $allow_partial_payment;
							}
							else {
								$arr[$j] = $remainAmount / $allow_partial_payment;
							}
						}		
					}
				}

				$arr = array_combine(range(1, count($arr)), array_values($arr));	
				$total_installment = count($arr) + 1;

				$partialpaymentModel = Mage::getModel('partialpayment/partialpayment')->load($partial_payment_id);
				$partialpaymentModel->setTotalInstallment($total_installment);
				$partialpaymentModel->setRemainingInstallment($total_installment-1);
				$partialpaymentModel->save();

				for ($i=1;$i<=$total_installment;$i++) {
					if ($i == 1)
     				{
       					if($payment_method == 'paypal_standard')
       					{
         					$installmentModelData = array('partial_payment_id' => $partial_payment_id, 'installment_amount' => $first_installment_amount, 'installment_due_date' => $installment_due_date, 'installment_status' => 'Remaining');
       					}
       					else
       					{ 
         					$installmentModelData = array('partial_payment_id' => $partial_payment_id, 'installment_amount' => $first_installment_amount, 'installment_due_date' => $installment_due_date, 'installment_paid_date' => $installment_due_date, 'installment_status' => 'Paid', 'payment_method'=>$payment_method);
         				}
        			}
					else {
						$installmentAmount = $arr[$i-1];
						if($shippingTaxFinal > 0) {
							$shippingTaxFinalValue = $shippingTaxFinal/($total_installment - 1);
							$installmentAmount = $arr[$i-1] + $shippingTaxFinalValue;
						}
						if($discount > 0) {
							$discountValue = $discount/($total_installment - 1);
							$installmentAmount = $installmentAmount - $discountValue;
						}

						// next installment date calculation
						$date = new Zend_Date();

						if($partialpaymentHelper->isPaymentPlanDays())
						{						
							$days = $partialpaymentHelper->getPaymentPlanTotalNoOfDays() * ($i-1); 			
							$date->addDay($days);
							
						} elseif($partialpaymentHelper->isPaymentPlanWeekly()) {
							$date->addWeek(($i-1));
						} elseif($partialpaymentHelper->isPaymentPlanMonthly()) {
							$date->addMonth(($i-1));
						}

						$installmentModelData = array('partial_payment_id' => $partial_payment_id, 'installment_amount' =>$installmentAmount, 'installment_due_date' => $date->toString('yyyy-MM-dd'),'installment_status' => 'Remaining');
					}

					$installmentModel = Mage::getModel('partialpayment/installment')->setData($installmentModelData);

					try {
						$installmentModel->save();
					} catch (Exception $e) {
						Mage::log("Exception". $e);
					}
				}
			}
			$this->sendEmailSuccess($customer_first_name, $customer_last_name, $customer_email, $order_id, $partial_payment_id);
			Mage::getSingleton('core/session')->unsPP();
			Mage::getSingleton('core/session')->unsAdminhtmlCustomerId();
		}
	}
}