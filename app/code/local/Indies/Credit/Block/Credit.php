<?php
class Indies_Credit_Block_Credit extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCredit()     
     { 
        if (!$this->hasData('credit')) {
            $this->setData('credit', Mage::registry('credit'));
        }
        return $this->getData('credit');
        
    }
}