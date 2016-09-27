<?php
	
class Indies_Partialpayment_Model_Shippingandtaxcalculationoptions
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '1',
                'label' => Mage::helper('partialpayment')->__('Shipping & Tax will be charged in first installment')
            ),
            array(
                'value' => '2',
                'label' => Mage::helper('partialpayment')->__('Shipping & Tax distributed equally in all installments')
            )
        );
    }
}