<?php

class Indies_Partialpaymentadmin_Block_Partialpaymentadmin extends Indies_Partialpayment_Block_Partialpayment
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    public function getPartialpaymentadmin()
    { 
        if (!$this->hasData('partialpaymentadmin')) {
            $this->setData('partialpaymentadmin', Mage::registry('partialpaymentadmin'));
        }
        return $this->getData('partialpaymentadmin');
    }
}