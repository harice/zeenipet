<?php

class Indies_Partialpayment_Model_Mysql4_Sales_Order_Capture_Payment_Transaction extends Mage_Sales_Model_Mysql4_Order_Payment_Transaction
{
    protected function _construct()
    {
        $this->_init('partialpayment/partial_payment_capture_transaction', 'transaction_id');
    }
}