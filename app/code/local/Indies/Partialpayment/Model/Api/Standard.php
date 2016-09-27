<?php
 
class Indies_Partialpayment_Model_Api_Standard extends Mage_Paypal_Model_Api_Standard
{
   
    protected $_exportToRequestFilters = array(
        'amount'   => '_filterAmount',
        'shipping' => '_filterAmount',
        'tax'      => '_filterAmount',
        'discount_amount' => '_filterAmount',
    );

   
    protected $_commonRequestFields = array(
        'business', 'invoice', 'currency_code', 'paymentaction', 'return', 'cancel_return', 'notify_url', 'bn',
        'page_style', 'cpp_header_image', 'cpp_headerback_color', 'cpp_headerborder_color', 'cpp_payflow_color',
        'amount', 'shipping', 'tax', 'discount_amount', 'item_name', 'lc',
    );

  
    protected $_lineItemTotalExportMap = array(
        Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL => 'amount',
        Mage_Paypal_Model_Cart::TOTAL_DISCOUNT => 'discount_amount',
        Mage_Paypal_Model_Cart::TOTAL_TAX      => 'tax',
        Mage_Paypal_Model_Cart::TOTAL_SHIPPING => 'shipping',
    );
    protected $_lineItemExportItemsFormat = array(
        'id'     => 'item_number_%d',
        'name'   => 'item_name_%d',
        'qty'    => 'quantity_%d',
        'amount' => 'amount_%d',
    );

    protected $_lineItemExportItemsFilters = array(
         'qty'      => '_filterQty'
    );

    /**
     * Address export to request map
     * @var array
     */
    protected $_addressMap = array(
        'city'       => 'city',
        'country'    => 'country_id',
        'email'      => 'email',
        'first_name' => 'firstname',
        'last_name'  => 'lastname',
        'zip'        => 'postcode',
        'state'      => 'region',
        'address1'   => 'street',
        'address2'   => 'street2',
    );

      public function getStandardCheckoutRequest()
    {
        $request = $this->_exportToRequest($this->_commonRequestFields);
		$request['charset'] = 'utf-8';
		
		
        $order = Mage::getModel('sales/order')->loadByIncrementId($request['invoice']);
		
		if(Mage::getStoreConfig("payment/paypal_standard/line_items_enabled")==0){	
			$isLineItems = $this->_exportLineItems($request);
			$itemname = Mage::app()->getStore($this->getStore())->getFrontendName();
		
			if(Mage::getStoreConfig("payment/paypal_standard/line_items_summary")!=NULL){	
				$itemname = Mage::getStoreConfig("payment/paypal_standard/line_items_summary");
			}
			$request['tax']=number_format ($order->getTaxAmount(),2) ;
			$request['amount'] = $order->getGrandTotal() - $order->getShippingAmount();
			$request['item_name'] = $itemname;
			$request['shipping'] = $request['shipping'] - $order->getShippingTaxAmount();
			
			
		}elseif(Mage::getStoreConfig("payment/paypal_standard/line_items_enabled")==1){
			$isLineItems = 1;
				
			$request['tax_cart'] =number_format ($order->getTaxAmount(),2) ;
			$request['tax']=number_format ($order->getTaxAmount(),2) ;
			$orderItems = $order->getItemsCollection();
			 $i=1;
			foreach ($orderItems as $item){
			 
				$product_name = $item->getName();
				$product_qty = number_format ($item->getQtyOrdered() ,0);
				$product_price = $item->getPrice();
				if($product_price > 0)
				{
					$request['item_number_'.$i] = $product_name;
					$request['item_name_'.$i] = $product_name;
					$request['quantity_'.$i] = $product_qty;
					$request['amount_'.$i] = number_format (($product_price ) ,2);
					$i++;
				}
				
			   
			 
			}
			if($order->getShippingAmount()>0)
			{
				 
				$request['item_number_'.$i] ="Flat Rate - Fixed";
				$request['item_name_'.$i] ="Shipping";
				$request['quantity_'.$i] =1;
				//$request['amount_'.$i] = number_format (($order->getShippingAmount()- $order->getShippingTaxAmount()),2);
				$request['amount_'.$i] = number_format(($order->getShippingAmount()), 2);
			}
		
		}
			
        if ($isLineItems) {
            $request = array_merge($request, array(
                'cmd'    => '_cart',
                'upload' => 1,
            ));
            if (isset($request['tax'])) {
                $request['tax_cart'] = $request['tax'];
            }
            if (isset($request['discount_amount'])) {
                $request['discount_amount_cart'] = $request['discount_amount'];
            }
        } else {
            $request = array_merge($request, array(
                'cmd'           => '_ext-enter',
                'redirect_cmd'  => '_xclick',
            ));
        }

        // payer address
        $this->_importAddress($request);
        $this->_debug(array('request' => $request)); // TODO: this is not supposed to be called in getter
        return $request;
    }


    
    
}
