<?php
class Eagle_AdditionalOptions_Model_Observer
{
	public function catalogProductLoadAfter(Varien_Event_Observer $observer)
	{
		ini_set('display_errors', 'On');
error_reporting(E_ALL);
		
		$action = Mage::app()->getFrontController()->getAction();
		
		if ($action->getFullActionName() == 'checkout_cart_add')
    	{

    		/****************** TIM /******************/
   //  		// assuming you are posting your custom form values in an array called extra_options...
   //  		if ($options = $action->getRequest()->getParam('extra_options'))
   //       	{
	  //   		$quote = $observer->getEvent()->getQuote();
			// 	$quote_items = $quote->getItemsCollection();

			// 	foreach ($quote_items as $item) {
			// 		 $additionalOptions = array(array(
			// 		 'code' => 'uniform_customization',
			// 		 'label' => 'This text is displayed through additional options',
			// 		 'value' => 'ID is ' . $item->getProductId() . ' and SKU is ' . $item->getSku()
			// 		 ));

			// 		 $item->addOption(array(
			// 		 'code' => 'additional_options',
			// 		 'value' => serialize($additionalOptions),
			// 		 ));
			// 	}

			// 	//$quote->save();
			// }
    		/****************** TIM:END **************/
    		
    		
    		//****************** POST SOLUTION /******************/
    		// assuming you are posting your custom form values in an array called extra_options...
	        if ($options = $action->getRequest()->getParam('extra_options'))
	        {
	            $product = $observer->getProduct();

	            // add to the additional options array
	            $additionalOptions = array();
	            if ($additionalOption = $product->getCustomOption('additional_options'))
	            {
	                $additionalOptions = (array) unserialize($additionalOption->getValue());
	            }
	            foreach ($options as $key => $value)
	            {
	                $additionalOptions[] = array(
	                    'label' => $key,
	                    'value' => $value,
	                );
	            }
	            // add the additional options array with the option code additional_options
	            $observer->getProduct()
	                ->addCustomOption('additional_options', serialize($additionalOptions));
	        }
    		//****************** POST SOLUTION /******************/
        	

		
		}
    }

    //save additional options in the order
    public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
	{
	    $quoteItem = $observer->getItem();
	    if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
	        $orderItem = $observer->getOrderItem();
	        $options = $orderItem->getProductOptions();
	        $options['additional_options'] = unserialize($additionalOptions->getValue());
	        $orderItem->setProductOptions($options);
	    }
	}

}
?>