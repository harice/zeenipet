<?php

class Indies_Deposit_Helper_Data extends Mage_Core_Helper_Abstract
{	
	public function formatDeposit($amount){
		return Mage::helper('deposit')->__('Paying Now');
	}
}