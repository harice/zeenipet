<?php

class Indies_Partialpayment_Model_Api_Direct extends Morningtime_Eway_Model_Api_Direct
{
    public function getDirectFields($order)
    {
		if (empty($order)) {
            if (!($order = $this->getOrder())) {
                return array();
            }
        }
		
		if(Indies_Fee_Model_Fee::getFee()==0)
		{
			return parent::getDirectFields($order);
		}

        $storeId = $order->getStoreId();
        $billingAddress = $order->getBillingAddress();
        $paymentMethodCode = $order->getPayment()->getMethod();

        $directFields = array();
        $directFields['ewayCustomerID'] = substr($this->getConfigData('customer_id', $storeId), 0, 8);
        $amount = number_format($order['deposit_amount'], 2, '.', '');
        if ($this->getConfig()->getPaymentConfigData($paymentMethodCode, 'test_flag')) {
            $directFields['ewayTotalAmount'] = round($amount * 100);
		}
        else {
		     $directFields['ewayTotalAmount'] = round($amount * 100);
        }
		
        $directFields['ewayCustomerFirstName'] = substr($billingAddress->getFirstname(), 0, 50);
        $directFields['ewayCustomerLastName'] = substr($billingAddress->getLastname(), 0, 50);
        $directFields['ewayCustomerEmail'] = substr($billingAddress->getEmail(), 0, 50);
        $directFields['ewayCustomerAddress'] = substr($billingAddress->getStreet(1), 0, 255);
        $directFields['ewayCustomerPostcode'] = substr($billingAddress->getPostcode(), 0, 6);
        $directFields['ewayCustomerInvoiceDescription'] = substr($this->getConfig()->getOrderDescription($order), 0, 255);
        $directFields['ewayCustomerInvoiceRef'] = substr($order->getIncrementId(), 0, 50);
        $directFields['ewayTrxnNumber'] = substr($order->getIncrementId(), 0, 16);
        $directFields['ewayOption1'] = '';
        $directFields['ewayOption2'] = '';
        $directFields['ewayOption3'] = '';

        // Card holder
        $directFields['ewayCardHoldersName'] = substr($order->getPayment()->getCcOwner(), 0, 50);
        $directFields['ewayCardNumber'] = substr(Mage::helper('core')->decrypt(Mage::getSingleton('core/session')->getCcNumberEnc()), 0, 20);
        $directFields['ewayCardExpiryMonth'] = substr($order->getPayment()->getCcExpMonth(), 0, 2);
        $directFields['ewayCardExpiryYear'] = substr($order->getPayment()->getCcExpYear(), 2, 2);

        // CVN
        if ($this->getConfigData('card_security') != Morningtime_Eway_Model_Config::SECURITY_STANDARD) {
            $directFields['ewayCVN'] = substr(Mage::helper('core')->decrypt(Mage::getSingleton('core/session')->getCcCidEnc()), 0, 4);
        }

        // Beagle Anti-Fraud
        if ($this->getConfigData('card_security') == Morningtime_Eway_Model_Config::SECURITY_BEAGLE) {
            $directFields['ewayCustomerIPAddress'] = substr(Mage::helper('eway')->getRealIpAddr(), 0, 15);
            $directFields['ewayCustomerBillingCountry'] = substr($billingAddress->getCountry(), 0, 2);
        }

        // we don't keep the CC data, never stored in the DB
        // this should help for PCI certification
        // @see $_canSaveCc = false
        Mage::getSingleton('core/session')->setCcNumberEnc(null);
        Mage::getSingleton('core/session')->setCcCidEnc(null);
        Mage::getSingleton('core/session')->getCcOwner(null);
        Mage::getSingleton('core/session')->setCcLast4(null);
        Mage::getSingleton('core/session')->setCcNumber(null);
        Mage::getSingleton('core/session')->setCcCid(null);
        Mage::getSingleton('core/session')->setCcExpMonth(null);
        Mage::getSingleton('core/session')->setCcExpYear(null);
        Mage::getSingleton('core/session')->getCcSsIssue(null);
        Mage::getSingleton('core/session')->setCcSsStartYear(null);
        Mage::getSingleton('core/session')->setCcSsStartMonth(null);

        return $directFields;
    }

}
