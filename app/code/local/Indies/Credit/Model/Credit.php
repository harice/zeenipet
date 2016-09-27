<?php

class Indies_Credit_Model_Credit extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('credit/credit');
    }
}