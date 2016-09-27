<?php

class Indies_Partialpayment_Model_Mysql4_Sales_Order_Capture_Payment extends Mage_Sales_Model_Mysql4_Order_Payment
{
    protected function _construct()
    {
        $this->_init('partialpayment/partial_payment_capture', 'entity_id');
    }

}