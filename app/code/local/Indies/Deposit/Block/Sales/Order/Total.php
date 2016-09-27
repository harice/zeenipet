<?php
class Indies_Deposit_Block_Sales_Order_Total extends Mage_Core_Block_Template
{
    /**
     * Get label cell tag properties
     *
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get value cell tag properties
     *
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize reward points totals
     *
     * @return Enterprise_Reward_Block_Sales_Order_Total
     */
    public function initTotals()
    {
		
		$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');
		
		if($partialpaymentHelper->isApplyToWholeCart())
				$condition = (((float) $this->getOrder()->getFeeAmount() > 0) && ($this->getOrder()->getDepositAmount() > 0) && ($this->getOrder()->getSubtotalInclTax() >= $partialpaymentHelper->getMinimumOrderAmount() && $partialpaymentHelper->isApplyToWholeCart() ) && ($this->getOrder()->getGrandTotal() > $this->getOrder()->getFeeAmount()));
			else
				$condition = (((float) $this->getOrder()->getFeeAmount() > 0) && ($this->getOrder()->getDepositAmount() > 0) && ($this->getOrder()->getGrandTotal() > $this->getOrder()->getFeeAmount()));
		
        if ($condition) {
			
            $source = $this->getSource();
            $value  = $source->getDepositAmount();
			
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'deposit',
                'strong' => true,
                'label'  => Mage::helper('deposit')->formatDeposit($value),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value,
				'area' => 'footer'
            )), 'grand_total');
        }
		

        return $this;
    }
}
