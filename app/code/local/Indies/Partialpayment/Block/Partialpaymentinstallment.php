<?php

class Indies_Partialpayment_Block_PartialpaymentInstallment extends Mage_Checkout_Block_Multishipping_Billing
{
	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
		$pager->setAvailableLimit(array(10=>10,15=>15,20=>20,'all'=>'all'));
		$pager->setCollection($this->getCollection());
		$this->setChild('pager', $pager);
		$this->getCollection()->load();
		return $this;
	}


	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}


	public function __construct()
	{
		parent::__construct();
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

		if($partialpaymentHelper->isEnabled())
	    {
			//fetching current customer id
			$customer_id = Mage::getSingleton('customer/session')->getId();
			$order_id = Mage::app()->getRequest()->getParam('order_id');
			$partial_payment_id = Mage::app()->getRequest()->getParam('partial_payment_id');

			if ($customer_id && $order_id && $partial_payment_id) {
				$partial_payment_order_collection = Mage::getModel('partialpayment/partialpayment')->getCollection()
														->addFieldToFilter('customer_id', $customer_id)
														->addFieldToFilter('order_id', $order_id)
														->addFieldToFilter('partial_payment_id', $partial_payment_id);
				if (!sizeof($partial_payment_order_collection)) {
					$partial_payment_id = 0;
				}
			}
			else {
				$partial_payment_id = 0;
			}

			$collection = Mage::getModel('partialpayment/installment')->getCollection()
							->addFieldToFilter('partial_payment_id', array('eq' => $partial_payment_id ));
			$this->setCollection($collection);
	   }
	}


	public function getPartialpayment()     
	{
		if (!$this->hasData('partialpayment')) 
		{
			$this->setData('partialpayment', Mage::registry('partialpayment'));
		}
		return $this->getData('partialpayment');
	}
}