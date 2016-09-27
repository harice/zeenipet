<?php
/**
 * Magento Order Grid Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the License Version.
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 *
 * @category   Order Grid
 * @package    Magedev_Gris
 * @copyright  Copyright (c) 2010 
 * @version    1.0.0
*/ 
require_once 'Zend/Json/Decoder.php'; 
include_once('Mage/Adminhtml/controllers/Sales/OrderController.php');
class Magedev_Ordermanager_Adminhtml_OrdereditorController extends Mage_Adminhtml_Controller_action
{
	private $_order;
 
 
    public function doMassInvoiceAction()// invoice + Capture(offline)
	{

		$orderIds = $this->getRequest()->getPost('order_ids', array());
        $count = $this->createInvoiceOnly($orderIds);
        if ($count>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were invoiced successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	
    public function doMassInvoiceonlyAction() // invoice only
	{

		$orderIds = $this->getRequest()->getPost('order_ids', array());
                         
        $countInvoiceOrder = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canInvoice())//$invoice->getTotalQty()
				{
							$qtyToInvoice = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$qtyToInvoice[$itemId] = $orderItem->getQtyToInvoice();
							}
							
							//$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
							$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($qtyToInvoice);
						if ($invoice->getTotalQty()) // check if return invoice obj has the few qty to invoice
						{
							//$invoice->setRequestedCaptureCase('offline');
							$invoice->register();
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_oninvoice');
							
							if($isToNotifyCustomer)	$invoice->setEmailSent(true);
							if($isToNotifyCustomer) $invoice->getOrder()->setCustomerNoteNotify(true);
							
							$invoice->getOrder()->setIsInProcess(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($invoice)
												->addObject($invoice->getOrder());
							$transactionSave->save();                
							
							if($isToNotifyCustomer) $invoice->sendEmail(true, '');
							
							$countInvoiceOrder++;
						}
				}
            }            
        }
    
	
        if ($countInvoiceOrder>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were invoiced successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function doMassShipAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
        $count = $this->createShipOnly($orderIds);
        if ($count>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were shipped successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function doMassInvoiceCaptureAction()// invoice + Capture(offline)
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
        $count = $this->createInvoiceCaptureOnly($orderIds);
        if ($count>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were invoiced and captured successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function doMassInvoiceShipOffAction() // invoice + Capture(offline) + ship
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());

        $count = $this->createInvoiceOnly($orderIds);
        $count = $this->createShipOnly($orderIds);
		
        if ($count>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were invoiced and shipped successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function doMassInvoiceShipOnlineAction() // invoice + Capture(online) + ship
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());

        $count = $this->createInvoiceCaptureOnly($orderIds);
        $count = $this->createShipOnly($orderIds);
		
        if ($count>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were invoiced and shipped successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	
	public function doMassArchiveAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
 		foreach ($orderIds as $orderId) {
			$order = Mage::getModel('sales/order')->load($orderId); 
			
			$order->setIsArchieved(1); 
            $order->save();
			
			$vals = array();
    		$vals['is_archieved'] = 1;
	
			$where = $write->quoteInto('entity_id =?', $orderId);
    		$write->update("sales_flat_order_grid", $vals ,$where);
	
		}
        $countOrder = count($orderIds);

       if ($countOrder>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were archieved successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function doMassActiveAction() 
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		foreach ($orderIds as $orderId) {
			$order = Mage::getModel('sales/order')->load($orderId); 
			
			$order->setIsArchieved(0);
            $order->save();
			
			$vals = array();
    		$vals['is_archieved'] = 0;
	
			$where = $write->quoteInto('entity_id =?', $orderId);
    		$write->update("sales_flat_order_grid", $vals ,$where);
		}
        $countOrder = count($orderIds);

       if ($countOrder>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were Active successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
		
	}
	
	
	private function _loadOrder($orderId) {
		$this->_order = Mage::getModel('sales/order')->load($orderId);
		if(!$this->_order->getId()) return false;
		return true;
	}
	
	public function saveinvoicestatusAction() {
	
		$field = $this->getRequest()->getParam('field');
		
		$invoiceId = $this->getRequest()->getParam('invoice_id');
		$value = $this->getRequest()->getPost('value');
 		
		if($field == "order_status") {
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);
		//echo $order->getId();
				$order->setStatus($value)->save();
				$this->getResponse()->setBody(ucfirst($value));
				return true;
		}
		if($field == "order_name") {
			$orderId = $this->getRequest()->getParam('order_id');
			$order = Mage::getModel('sales/order')->load($orderId);
			$order->setIncrementId($value)->save();
			$this->getResponse()->setBody(ucfirst($value));
			return true;
		}
		
		if (!empty($field) && !empty($invoiceId)) {
			$invoice = Mage::getModel('sales/order_invoice')
                    ->load($invoiceId);
			$invoiceState = $invoice->setState($value);
			$invoice->save();

			$statuses = Mage::getModel('sales/order_invoice')->getStates();
			$invoiceState = $invoice->getState();
			if(isset($invoiceState))
			echo $invoiceStateLabel = $statuses[$invoiceState];
			else echo 'error in saving..';
		}
	}
	
	public function savetrackingAction() {
	 
		$field = $this->getRequest()->getParam('field');
		
		$orderId = $this->getRequest()->getParam('order_id');
		$trackingEntityId = $this->getRequest()->getParam('entity_id');
		$value = $this->getRequest()->getPost('value');
 		
		if($field == "tracking") {
			$order = Mage::getModel('sales/order')->load($orderId);
			
			$tracking = Mage::getResourceModel('sales/order_shipment_track_collection')
						->setOrderFilter($order)
						->addFieldToFilter('entity_id',$trackingEntityId)
						->getData();
			
			foreach($tracking as $sc){
				//$trackMethod = $sc->getTrackNumber($value);
				//$trackMethod ->save();
			}
			
			 $track = Mage::getModel('sales/order_shipment_track')
                 ->setData('number',  $value)
                 ->setData('entity_id', $trackingEntityId)
                 ->save();
				 
				 echo $value;die;
				 
		
			$tracking['track_number'] = $value;
			echo $tracking->save();die;
//		echo '<pre>';print_r($tracking);die;
				//$order->setStatus($value)->save();
				//$this->getResponse()->setBody(ucfirst($value));
				//return true;
		}
		
	}
	
	public function createShipOnly($orderIds)
    {                   
        $countShipOrder = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canShip())
				{
							$itemToShip = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$itemToShip[$itemId] = $orderItem->getQtyToShip();
							}
							

							$ship = Mage::getModel('sales/service_order', $order)->prepareShipment($itemToShip);
						if ($ship->getTotalQty()) // check if return invoice obj has the few qty to chip
						{
							$ship->register();
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_onship');
							if($isToNotifyCustomer) $ship->setEmailSent(true);
							if($isToNotifyCustomer) $ship->getOrder()->setCustomerNoteNotify(true);
							
							$ship->getOrder()->setIsInProcess(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($ship)
												->addObject($ship->getOrder());
							$transactionSave->save();                
							
							if($isToNotifyCustomer) $ship->sendEmail(true, '');
							
							$countShipOrder++;
						}
				}
            }            
        }
        return $countShipOrder;        
    }
	
	public function createInvoiceOnly($orderIds)//capture offline
    {                         
        $countInvoiceOrder = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canInvoice())//$invoice->getTotalQty()
				{
							$qtyToInvoice = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$qtyToInvoice[$itemId] = $orderItem->getQtyToInvoice();
							}
							
							//$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
							$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($qtyToInvoice);
						if ($invoice->getTotalQty()) // check if return invoice obj has the few qty to invoice
						{
							$invoice->setRequestedCaptureCase('offline');
							$invoice->register();
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_oninvoice');
							if($isToNotifyCustomer) $invoice->setEmailSent(true);
							if($isToNotifyCustomer) $invoice->getOrder()->setCustomerNoteNotify(true);
							
							$invoice->getOrder()->setIsInProcess(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($invoice)
												->addObject($invoice->getOrder());
							$transactionSave->save();                
							
							if($isToNotifyCustomer) $invoice->sendEmail(true, '');
							
							$countInvoiceOrder++;
						}
				}
            }            
        }
        return $countInvoiceOrder;        
    }
	
	public function createInvoiceCaptureOnly($orderIds) //capture online
    {                              
        $countInvoice = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canInvoice())//$invoice->getTotalQty()
				{
							$savedQtys = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$savedQtys[$itemId] = $orderItem->getQtyToInvoice();
							}
							
							//$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
							$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
						if ($invoice->getTotalQty()) // check if return invoice obj has the few qty to invoice
						{
							$invoice->setRequestedCaptureCase('online');
							$invoice->register();
							$invoice->getOrder()->setIsInProcess(true);
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_oninvoice');
							if($isToNotifyCustomer) $invoice->setEmailSent(true);
							if($isToNotifyCustomer) $invoice->getOrder()->setCustomerNoteNotify(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($invoice)
												->addObject($invoice->getOrder());
							$transactionSave->save();
							 
							if($isToNotifyCustomer) $invoice->sendEmail(true, '');
							
							$countInvoice++;
						}
				}
            }            
        }
        return $count;        
    }	
	
	 
	
	public function createInvoiceNCOnly($orderIds) // notify customer
    {                                
        $countInvoiceOrder = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canInvoice())//$invoice->getTotalQty()
				{
							$savedQtys = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$savedQtys[$itemId] = $orderItem->getQtyToInvoice();
							}
							
							//$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
							$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
						if ($invoice->getTotalQty()) // check if return invoice obj has the few qty to invoice
						{
							$invoice->register();
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_oninvoice');
							if($isToNotifyCustomer) $invoice->setEmailSent(true);
							if($isToNotifyCustomer) $invoice->getOrder()->setCustomerNoteNotify(true);
							
							$invoice->getOrder()->setIsInProcess(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($invoice)
												->addObject($invoice->getOrder());
							$transactionSave->save();                
							
							if($isToNotifyCustomer) $invoice->sendEmail(true, '');
							
							$countInvoiceOrder++;
						}
				}
            }            
        }
        return $count;        
    }
	

	
	public function createInvoiceCaptureNCOnly($orderIds)//notify customer
    {                                
        $count = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canInvoice())//$invoice->getTotalQty()
				{
							$savedQtys = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$savedQtys[$itemId] = $orderItem->getQtyToInvoice();
							}
							
							//$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
							$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
						if ($invoice->getTotalQty()) // check if return invoice obj has the few qty to invoice
						{
							$invoice->setRequestedCaptureCase('online');
							$invoice->register();
							$invoice->getOrder()->setIsInProcess(true);
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_oninvoice');
							if($isToNotifyCustomer) $invoice->setEmailSent(true);
							if($isToNotifyCustomer) $invoice->getOrder()->setCustomerNoteNotify(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($invoice)
												->addObject($invoice->getOrder());
							$transactionSave->save();                
							if($isToNotifyCustomer) $invoice->sendEmail(true, '');
							$count++;
						}
				}
            }            
        }
        return $count;        
    }	
	
	 /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    public function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');

        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
	
	
	 public function editsinglehistoryAction()
	 {
	 		$field = $this->getRequest()->getParam('field');
			$orderId = $this->getRequest()->getParam('order_id');
			$historyEntityId = $this->getRequest()->getParam('item_id');
			$value = $this->getRequest()->getParam('value');
		
			$historyModel = Mage::getModel('sales/order_status_history');
			$historyModel->load($historyEntityId);
			$historyModel->setComment($value);
			$historyModel->save();
			echo $value;die;
	 }	
	 public function deleteallhistoryAction()
	 {
	 	
	 	if ($order = $this->_initOrder()) {
           try {
		   
				$response = false;
				 
				//$order = $this->_initOrder(); // it is required as it set order object while template load via ajax
	
				$field = $this->getRequest()->getParam('field');
				$orderId = $this->getRequest()->getParam('order_id');
				$historyEntityIds = $this->getRequest()->getParam('item_ids');
				$idsArr = explode(",",$historyEntityIds);
				$idsArr = array_filter($idsArr);
				if(count($idsArr) < 1)
				{
					 $response = array(
                    'error'     => true,
                    'message'   => $this->__('Please select atleast one item to delete.'),
               		 );
					$this->loadLayout('empty');
					$this->renderLayout();
				}
				else
				{
					foreach($idsArr as $historyEntityId)
					{
						$historyModel = Mage::getModel('sales/order_status_history');
						$historyModel->load($historyEntityId);
						$historyModel->delete();
						$historyModel->save();
					}
					$this->loadLayout('empty');
					$this->renderLayout();
				}
			 }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.'),
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
		}	
	
	 }
	 
	 
	 public function deletesinglehistoryAction()
	 {
	 	if ($order = $this->_initOrder()) {
           try {
		   
				$response = false;
				 
				//$order = $this->_initOrder(); // it is required as it set order object while template load via ajax
	
				$field = $this->getRequest()->getParam('field');
				$orderId = $this->getRequest()->getParam('order_id');
				$historyEntityId = $this->getRequest()->getParam('item_id');
			
				$historyModel = Mage::getModel('sales/order_status_history');
				$historyModel->load($historyEntityId);
				$historyModel->delete();
				$historyModel->save();
				
				$this->loadLayout('empty');
				$this->renderLayout();
			 }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
		}	
	}
	
	public function savewithdelAction()
	{	

	 	if ($order = $this->_initOrder()) {
           try {
		   
				$response = false;
				
				$postData = $this->getRequest()->getParams();
				
			foreach($postData as $data)
			{ 
					if($data = json_decode($data, Zend_Json::TYPE_ARRAY)) {
						//echo '<pre>';print_r($data);die;
						//$dataArr[] = $data;
					 
							foreach($data['his_item_id'] as $key => $val)
							{
								if(isset($data['deleteHistory']) && count($data['deleteHistory'])>0 && in_array($val,$data['deleteHistory'])) //delete coments
								{
									$historyModel = Mage::getModel('sales/order_status_history');
									$historyModel->load($val);
									$historyModel->delete();
									$historyModel->save();

								}else{
									$historyModel = Mage::getModel('sales/order_status_history');
									$historyModel->load($val);
									$historyModel->setComment($data['comment'][$key]);
									$historyModel->save();
								}
							}
						
					}
			}	
				$this->loadLayout('empty');
				$this->renderLayout();
				 
			 }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__($e.'Cannot add order history.'),
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
		}	
	}
	
	public function addCommentAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $order->addStatusHistoryComment($data['comment'], $data['status'])
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));

                $order->save();
                $order->sendOrderUpdateEmail($notify, $comment);

                $this->loadLayout('empty');
                $this->renderLayout();
            }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }
}