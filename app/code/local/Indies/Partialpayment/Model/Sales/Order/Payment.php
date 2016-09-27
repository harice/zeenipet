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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Indies
 * @package     Indies_Fee
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order payment information
 *
 * @category    Indies
 * @package     Indies_Fee
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Indies_Partialpayment_Model_Sales_Order_Payment extends Mage_Sales_Model_Order_Payment
{
	protected function _isCaptureFinal($amountToCapture)
    {
		
        $amountToCapture = $this->_formatAmount($amountToCapture, true);
        $orderGrandTotal = $this->_formatAmount($this->getOrder()->getBaseGrandTotal(), true);
        if ($orderGrandTotal == $this->_formatAmount($this->getBaseAmountPaid(), true) + $amountToCapture) {
            if (false !== $this->getShouldCloseParentTransaction()) {
                $this->setShouldCloseParentTransaction(true);
            }
            return true;
        }
		return true;
    }
    public function capture($invoice)
    {
        if (is_null($invoice)) {
            $invoice = $this->_invoice();
            $this->setCreatedInvoice($invoice);
            return $this; // @see Mage_Sales_Model_Order_Invoice::capture()
        }
		
		// Start: Customize code for Capturing 1st Installment Amount by Kartik Maniyar on Date: - 30/07/2012
		
        $order = $this->getOrder();
		$incrementId = $order->getIncrementId();
		
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		$partialpaymentOrder = Mage::getModel('partialpayment/partialpayment')->getCollection()
			->addFieldToFilter('order_id',$incrementId)
			->load();
			
		if(sizeof($partialpaymentOrder->getData()) == 0)
		{
			if(($invoice->getBaseDepositAmount() > 0) && ($invoice->getBaseFeeAmount() > 0)&& ($invoice->getSubtotalInclTax() >= $partialpaymentHelper->getMinimumOrderAmount()) && ($invoice->getSubtotalInclTax() > $invoice->getBaseFeeAmount()) && ($invoice->getGrandTotal() > $invoice->getBaseFeeAmount()) && ($invoice->getBaseDepositAmount() > $invoice->getTaxAmount()))
			{
				$capture_amount = $invoice->getBaseDepositAmount();
				$amountToCapture = $this->_formatAmount($capture_amount);
			}else{
				$amountToCapture = $this->_formatAmount($invoice->getBaseGrandTotal());
			}
		}
		else
		{
			foreach($partialpaymentOrder as $partialpaymentModel)
			{
				$partialpaymentId = $partialpaymentModel->getPartialPaymentId();
				
				$installmentModel = Mage::getModel('partialpayment/installment')->getCollection()
					->addFieldToFilter('partial_payment_id',$partialpaymentId)
					->load();
				
				foreach($installmentModel as $installments)
				{
					if($installments->getInstallmentStatus() == 'Remain')
					{
						$capture_amount = $installments->getInstallmentAmount();
						$amountToCapture = $this->_formatAmount($capture_amount);
						break;
					}
				}
			}
		}
		
		// End: Customize code for Capturing 1st Installment Amount by Kartik Maniyar on Date: - 30/07/2012

        // prepare parent transaction and its amount
        $paidWorkaround = 0;
        if (!$invoice->wasPayCalled()) {
            $paidWorkaround = (float)$amountToCapture;
        }
        $this->_isCaptureFinal($paidWorkaround);

        $this->_generateTransactionId(
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
            $this->getAuthorizationTransaction()
        );

        Mage::dispatchEvent('sales_order_payment_capture', array('payment' => $this, 'invoice' => $invoice));

        /**
         * Fetch an update about existing transaction. It can determine whether the transaction can be paid
         * Capture attempt will happen only when invoice is not yet paid and the transaction can be paid
         */
        if ($invoice->getTransactionId()) {
            $this->getMethodInstance()
                ->setStore($order->getStoreId())
                ->fetchTransactionInfo($this, $invoice->getTransactionId());
        }
        $status = true;
        if (!$invoice->getIsPaid() && !$this->getIsTransactionPending()) {
            // attempt to capture: this can trigger "is_transaction_pending"
            $this->getMethodInstance()->setStore($order->getStoreId())->capture($this, $amountToCapture);

            $transaction = $this->_addTransaction(
                Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
                $invoice,
                true
            );

            if ($this->getIsTransactionPending()) {
                $message = Mage::helper('sales')->__('Capturing amount of %s is pending approval on gateway.', $this->_formatPrice($amountToCapture));
                $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
                if ($this->getIsFraudDetected()) {
                    $status = Mage_Sales_Model_Order::STATUS_FRAUD;
                }
                $invoice->setIsPaid(false);
            } else { // normal online capture: invoice is marked as "paid"
                $message = Mage::helper('sales')->__('Captured amount of %s online.', $this->_formatPrice($amountToCapture));
                $state = Mage_Sales_Model_Order::STATE_PROCESSING;

				// Start: Set State = "Pending" when 1st Installment Amount will be Captured by Kartik Maniyar on Date: - 23/07/2012
				/*if($invoice->getBaseFeeAmount())
                	$invoice->setIsPaid(false);
				else*/
					$invoice->setIsPaid(true);

				// End: Set State = "Pending" when 1st Installment Amount will be Captured by Kartik Maniyar on Date: - 23/07/2012
				
                $this->_updateTotals(array('base_amount_paid_online' => $amountToCapture));
            }
            if ($order->isNominal()) {
                $message = $this->_prependMessage(Mage::helper('sales')->__('Nominal order registered.'));
            } else {
                $message = $this->_prependMessage($message);
                $message = $this->_appendTransactionToMessage($transaction, $message);
            }
            $order->setState($state, $status, $message);
            $this->getMethodInstance()->processInvoice($invoice, $this); // should be deprecated
            return $this;
        }
        Mage::throwException(
            Mage::helper('sales')->__('The transaction "%s" cannot be captured yet.', $invoice->getTransactionId())
        );
    }
}
