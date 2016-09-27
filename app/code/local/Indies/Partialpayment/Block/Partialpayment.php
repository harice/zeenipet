<?php
class Indies_Partialpayment_Block_Partialpayment extends Mage_Checkout_Block_Multishipping_Billing
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
					
			/* changes Start by Indies Services on 03-11-2012 */ 
			$collection = Mage::getModel('sales/order')->getCollection()
						  ->addFieldToFilter('status',array('nin' => array('canceled','closed')));
			
			$arrInstallment=array();
			if(sizeof($collection)) 
			{
				foreach ($collection as $order) 
				{
					$arrInstallment[]=$order->getIncrementId();
				}
			}
			if(sizeof($arrInstallment)==0)
			{
				$arrInstallment[0]=0;
			}
			$collection = Mage::getModel('partialpayment/partialpayment')->getCollection()
				->addFieldToFilter('customer_id', $customer_id)
				->setOrder('partial_payment_id','DESC')
				->addFieldToFilter('order_id',array('in' => $arrInstallment));
				
			/* changes End by Indies Services on 03-11-2012 */ 
			$this->setCollection($collection);
	    }
		else
		{
			//fetching current customer id 
			$customer_id = Mage::getSingleton('customer/session')->getId();
			/* changes Start by Indies Services on 03-11-2012 */ 
			$collection = Mage::getModel('partialpayment/partialpayment')->getCollection()
							->addFieldToFilter('customer_id', $customer_id)
							->setOrder('partial_payment_id','DESC')
							->addFieldToFilter('partial_payment_status', array('nin' => array('Close','Cancel')));
			/* changes End by Indies Services on 03-11-2012 */ 
			
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