<?php
class Indies_Outstockdiscount_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function formatOutstockDiscount($amount)
	{
		return Mage::helper('outstockdiscount')->__('Out of Stock Discount');
	}
}