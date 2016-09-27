<?php
	
class Indies_Partialpayment_Model_Installmentcalculationtype
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '1',
                'label' => Mage::helper('partialpayment')->__('Fixed Amount Value')
            ),
            array(
                'value' => '2',
                'label' => Mage::helper('partialpayment')->__('Percentage Value')
            )
        );
    }
}