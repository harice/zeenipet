<?php
	
class Indies_Partialpayment_Model_Paymentplan
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'monthly',
                'label' => Mage::helper('partialpayment')->__('Monthly')
            ),
            array(
                'value' => 'weekly',
                'label' => Mage::helper('partialpayment')->__('Weekly')
            ),
            array(
                'value' => 'days',
                'label' => Mage::helper('partialpayment')->__('Days')
            )
        );
    }
}