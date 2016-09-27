<?php
class Indies_Outstockdiscount_Block_Sales_Order_Total extends Mage_Core_Block_Template
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
        if (((float) $this->getOrder()->getBaseOutstockDiscountAmount()) && ((float) $this->getOrder()->getFeeAmount() > 0) && ($this->getOrder()->getDepositAmount() > 0) && ($this->getOrder()->getSubtotal() >= $partialpaymentHelper->getMinimumOrderAmount()) && ($this->getOrder()->getGrandTotal() > $this->getOrder()->getFeeAmount())) {
            $source = $this->getSource();
			$discount = $source->getOutstockDiscountAmount();
			
			$this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'outstockdiscount',
                'strong' => false,
                'label'  => Mage::helper('outstockdiscount')->formatOutstockDiscount($discount),
                'value'  => -$discount,
            )),'subtotal');
		}
		return $this;
    }
}
