<?php

class Indies_Partialpayment_Model_Mysql4_Partialpayment extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the partial_payment_id refers to the key field in your database table.
        $this->_init('partialpayment/partialpayment', 'partial_payment_id');
    }
}