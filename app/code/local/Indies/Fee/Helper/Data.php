<?php

class Indies_Fee_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function formatFee($amount){
		return Mage::helper('fee')->__('Amount to be Paid Later');
	}
}