<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Phoenix
 * @package    Mango_Loworderfee
 * @copyright  Copyright (c) 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mango_Loworderfee_Model_Observer extends Mage_Core_Model_Abstract {

   
 

    
 public function sales_quote_collect_totals_after($observer)
    {
    // KIPL CODE

       $quote=$observer->getEvent()->getQuote();
       $quoteid=$quote->getId();
       $store = $quote->getStore();
      $_fee =Mage::getStoreConfig('sales/minimum_order/low_order_fee', $store->getId());
      $_method =Mage::getStoreConfig('sales/minimum_order/low_order_fee_method', $store->getId());
      if($_method == "percentage"){
            $_fee = ($address->getBaseSubtotalWithDiscount() * $_fee / 100);
      }


       

       $discountAmount= $_fee;      
    if($quoteid) {
       
       
      
        if($discountAmount>0) {
        $total=$quote->getBaseSubtotal();
            $quote->setSubtotal(0);
            $quote->setBaseSubtotal(0);

            $quote->setSubtotalWithDiscount(0);
            $quote->setBaseSubtotalWithDiscount(0);

            $quote->setGrandTotal(0);
            $quote->setBaseGrandTotal(0);
        
             
            $canAddItems = $quote->isVirtual()? ('billing') : ('shipping');    
            foreach ($quote->getAllAddresses() as $address) {
                
            $address->setSubtotal(0);
            $address->setBaseSubtotal(0);

            $address->setGrandTotal(0);
            $address->setBaseGrandTotal(0);

            $address->collectTotals();

            $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
            $quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

            $quote->setSubtotalWithDiscount(
                (float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
            );
            $quote->setBaseSubtotalWithDiscount(
                (float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
            );

            $quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
            $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());
    
            $quote ->save(); 
    
               $quote->setGrandTotal($quote->getBaseSubtotal()-$discountAmount)
               ->setBaseGrandTotal($quote->getBaseSubtotal()-$discountAmount)
               ->setSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
               ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
               ->save(); 
               
                
               
            } //end: foreach
            //echo $quote->getGrandTotal();
        
        foreach($quote->getAllItems() as $item){
                 //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                 $rat=$item->getPriceInclTax()/$total;
                 $ratdisc=$discountAmount*$rat;
                 $item->setDiscountAmount(($item->getDiscountAmount()+$ratdisc) * $item->getQty());
                 $item->setBaseDiscountAmount(($item->getBaseDiscountAmount()+$ratdisc) * $item->getQty())->save();
                
               }
            
                
            }
            
    }
   

 }


 /**
     * Adds codFee to order
     * @param Varien_Event_Observer $observer
     */
    public function sales_order_payment_place_end(Varien_Event_Observer $observer) {
        $payment = $observer->getPayment();
        /* if ($payment->getMethodInstance()->getLoworderfeee() != 'cashondelivery'){
          return;
          } */

        $order = $payment->getOrder();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (!$quote->getId()) {
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        }
        $order->setLoworderfee($quote->getLoworderfee());
        $order->setBaseLoworderfee($quote->getBaseLoworderfee());

        // $order->setLoworderfeeTaxAmount($quote->getLoworderfeeTaxAmount());
        // $order->setBaseLoworderfeeTaxAmount($quote->getBaseLoworderfeeTaxAmount());

        $order->save();
    }

    /**
     * Performs order_creage_loadBlock response update
     * adds totals block to each response
     * This function is depricated, the totals block update is implemented     * 
     * @param Varien_Event_Observer $observer
     */
    public function controller_action_layout_load_before(Varien_Event_Observer $observer) {
        $action = $observer->getAction();
        if ($action->getFullActionName() != 'adminhtml_sales_order_create_loadBlock' || !$action->getRequest()->getParam('json')) {
            return;
        }
        $layout = $observer->getLayout();
        $layout->getUpdate()->addHandle('adminhtml_sales_order_create_load_block_totals');
    }

    /**
     * Save order tax information
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesEventOrderAfterSave(Varien_Event_Observer $observer) {
        //echo "loworderfee";
        //   Mage::log( "loworderfee" );

        $order = $observer->getEvent()->getOrder();

        $order_tax = Mage::getResourceModel('sales/order_tax_collection')->loadByOrder($order);

        /*    $collection = Mage::getResourceModel('sales/order_tax_collection')
          // add all attributes when loading data
          ->addAttributeToSelect('*')
          // add your unique attribute value to the filter
          ->addAttributeToFilter('order_id', $order->getId())
          // load only one item
          ->setPage(1, 1); */

// $store is ID or code for specific store you want to get data from
//$collection->getEntity()->setStore($store);
// load collection, retrieve it's iterator and get first item
        $tax = current($order_tax->getIterator());


        Mage::log($order->getAppliedLofTaxIsSaved());

     

        /* $order = $observer->getEvent()->getOrder(); */

        if ($order->setAppliedLofTaxIsSaved()) {
            return;
        }

       /* Mage::getModel('tax/sales_order_tax')
                ->loadById($tax->getId())
                ->setAmount($tax->getAmount() + 5)
                ->setBaseAmount($tax->getBaseAmount() + 5)
                ->setBaseRealAmount($tax->getBaseRealAmount() + 5)
                ->save();*/

           Mage::log($tax);


        
        
        /* $tax->setAmount($tax->getAmount() + 5);
          $tax->setBaseAmount($tax->getBaseAmount() + 5);
          $tax->setBaseRealAmount($tax->getBaseRealAmount() + 5); */

        /* $tax->getId();

          $tax->save(); */

         
           
        $order->setAppliedLofTaxIsSaved(1);

          Mage::log($order->getAppliedLofTaxIsSaved());

        

        /*  $getTaxesForItems   = $order->getQuote()->getTaxesForItems();
          $taxes              = $order->getAppliedTaxes();

          $ratesIdQuoteItemId = array();
          if (!is_array($getTaxesForItems)) {
          $getTaxesForItems = array();
          }
          foreach ($getTaxesForItems as $quoteItemId => $taxesArray) {
          foreach ($taxesArray as $rates) {
          if (count($rates['rates']) == 1) {
          $ratesIdQuoteItemId[$rates['id']][] = array(
          'id'        => $quoteItemId,
          'percent'   => $rates['percent'],
          'code'      => $rates['rates'][0]['code']
          );
          } else {
          $percentDelta   = $rates['percent'];
          $percentSum     = 0;
          foreach ($rates['rates'] as $rate) {
          $ratesIdQuoteItemId[$rates['id']][] = array(
          'id'        => $quoteItemId,
          'percent'   => $rate['percent'],
          'code'      => $rate['code']
          );
          $percentSum += $rate['percent'];
          }

          if ($percentDelta != $percentSum) {
          $delta = $percentDelta - $percentSum;
          foreach ($ratesIdQuoteItemId[$rates['id']] as &$rateTax) {
          if ($rateTax['id'] == $quoteItemId) {
          $rateTax['percent'] = (($rateTax['percent'] / $percentSum) * $delta)
          + $rateTax['percent'];
          }
          }
          }
          }
          }
          }

          foreach ($taxes as $id => $row) {
          foreach ($row['rates'] as $tax) {
          if (is_null($row['percent'])) {
          $baseRealAmount = $row['base_amount'];
          } else {
          if ($row['percent'] == 0 || $tax['percent'] == 0) {
          continue;
          }
          $baseRealAmount = $row['base_amount'] / $row['percent'] * $tax['percent'];
          }
          $hidden = (isset($row['hidden']) ? $row['hidden'] : 0);
          $data = array(
          'order_id'          => $order->getId(),
          'code'              => $tax['code'],
          'title'             => $tax['title'],
          'hidden'            => $hidden,
          'percent'           => $tax['percent'],
          'priority'          => $tax['priority'],
          'position'          => $tax['position'],
          'amount'            => $row['amount'],
          'base_amount'       => $row['base_amount'],
          'process'           => $row['process'],
          'base_real_amount'  => $baseRealAmount,
          );

          $result = Mage::getModel('tax/sales_order_tax')->setData($data)->save();

          if (isset($ratesIdQuoteItemId[$id])) {
          foreach ($ratesIdQuoteItemId[$id] as $quoteItemId) {
          if ($quoteItemId['code'] == $tax['code']) {
          $item = $order->getItemByQuoteItemId($quoteItemId['id']);
          if ($item) {
          $data = array(
          'item_id'       => $item->getId(),
          'tax_id'        => $result->getTaxId(),
          'tax_percent'   => $quoteItemId['percent']
          );
          Mage::getModel('tax/sales_order_tax_item')->setData($data)->save();
          }
          }
          }
          }
          }
          }

          $order->setAppliedTaxIsSaved(true); */
    }




}

?>
