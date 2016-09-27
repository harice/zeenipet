<?php
class Indies_Credit_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isValidCustomer ()
	{
		$customer_groups = explode(',', Mage::getStoreConfig('partialpayment/credit_groups/customer_groups'));

		$customer = Mage::getSingleton('customer/session')->getCustomer();

		if ($customer->getId())
			$customer_group = $customer->getGroupId();
		else
			$customer_group = 0;

		if(in_array($customer_group, $customer_groups)) {
			if ($customer_group) {
				if ($customer->getCreditAmount() == '0')
					return false;
			}
			return true;
		}
		else
			return false;
	}
}