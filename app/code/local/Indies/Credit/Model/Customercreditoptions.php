<?php
class Indies_Credit_Model_Customercreditoptions
{
	
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '1',
                'label' => Mage::helper('partialpayment')->__('All Customers Including Guest')
            ),
            array(
                'value' => '2',
                'label' => Mage::helper('partialpayment')->__('Registered Customers Only')
            ),
			array(
                'value' => '3',
                'label' => Mage::helper('partialpayment')->__('Specific Customer Groups Only')
            )
        );
    }
}