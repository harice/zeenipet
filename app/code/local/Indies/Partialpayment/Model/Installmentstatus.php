<?php
	
class Indies_Partialpayment_Model_Installmentstatus
{
    public function toOptionArray()
    {
        return array(
			array(
                'value' => 'Paid',
                'label' => Mage::helper('partialpayment')->__('Paid')
            ),
            array(
                'value' => 'Remaining',
                'label' => Mage::helper('partialpayment')->__('Remaining')
            ),
            array(
                'value' => 'Canceled',
                'label' => Mage::helper('partialpayment')->__('Canceled')
            ),
        );
    }
}