<?php

class Indies_Partialpayment_Model_Mysql4_Installment extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the installment_id refers to the key field in your database table.
        $this->_init('partialpayment/installment', 'installment_id');
    }
}