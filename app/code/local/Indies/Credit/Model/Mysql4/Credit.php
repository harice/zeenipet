<?php

class Indies_Credit_Model_Mysql4_Credit extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the credit_id refers to the key field in your database table.
        $this->_init('credit/credit', 'credit_id');
    }
}