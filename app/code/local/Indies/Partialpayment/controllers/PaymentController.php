<?php

/**
 * 1997-2012 Quadra Informatique
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is available
 * through the world-wide-web at this URL: http://www.opensource.org/licenses/OSL-3.0
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to ecommerce@quadra-informatique.fr so we can send you a copy immediately.
 *
 *  @author Quadra Informatique <ecommerce@quadra-informatique.fr>
 *  @copyright 1997-2012 Quadra Informatique
 *  @version Release: $Revision: 2.1.1 $
 *  @license http://www.opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */

require_once Mage::getModuleDir('controllers', 'Quadra_Atos').DS.'PaymentController.php';
class Indies_Partialpayment_PaymentController extends Quadra_Atos_PaymentController {

    protected $_session;
    protected $_atosResponse = null;
    protected $_realOrderIds;
    protected $_quote;

   
  public function automaticAction() {
        if (!$this->getRequest()->isPost('DATA')) {
            $this->_redirect('');
            return;
        }

        $model = $this->getAtosMethod();

        if ($this->getConfig()->getCheckByIpAddress()) {
            if (!in_array($model->getApiParameters()->getIpAddress(), $this->getConfig()->getAuthorizedIps())) {
                Mage::log($model->getApiParameters()->getIpAddress() . ' tries to connect to our server' . "\n", null, 'atos.log');
                return;
            }
        }

        $response = $model->getApiResponse()
                          ->doResponse($_REQUEST['DATA'],array('bin_response' => $model->getBinResponse()));

        if ($response) {
			
			if($response['response_code']== "00")
			{
				$orderId = $response['order_id'];
				$partial_payment = Mage::getModel('partialpayment/partialpayment')->getCollection();;
				$partial_payment->addFieldToFilter('order_id', $orderId );
				$size = $partial_payment->getSize();
				
				$id=0;
				foreach ($partial_payment as $item)
				  {
					  
						$id=$item->getPartialPaymentId();
				  }
				 
				if($size)
				{		
					
					$partial_payment_save= Mage::getModel('partialpayment/partialpayment')
					->setPartialPaymentStatus('Order Pending')
					->setPartialPaymentId($id)
					->save();
				}
			}
            $this->_setAtosResponse($response);
            Mage::getModel('atos/log_response')->logResponse('automatic', $response);

            $realOrderIds = $this->_getRealOrderIds();
            if (count($realOrderIds) > 0) {
                $order = Mage::getModel('sales/order')->loadByIncrementId($realOrderIds[0]);
                $model = $this->getAtosMethod($order->getPayment()->getMethod());
                unset($order);
            }

            if ($response['merchant_id'] != $model->getMerchantId()) {
                Mage::log(sprintf('Response Merchant ID (%s) is not valid with configuration value (%s)' . "\n", $response['merchant_id'], $model->getMerchantId()), null, 'atos.log');
                return;
            }

            foreach ($realOrderIds as $realOrderId) {
                $order = Mage::getModel('sales/order')->loadByIncrementId($realOrderId);
                Mage::helper('atos')->updateOrderState($order, $response, $model);
            }
        }
    }

   

}
