<?php

class Indies_Partialpayment_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the partial_payment_product_id refers to the key field in your database table.
        $this->_init('partialpayment/product', 'partial_payment_product_id');
    }
}