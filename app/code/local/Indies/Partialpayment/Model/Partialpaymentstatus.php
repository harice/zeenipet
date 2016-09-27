<?php
	
class Indies_Partialpayment_Model_Partialpaymentstatus
{
    public function toOptionArray()
    {
        return array(
			 array(
                'value' => 'Pending',
                'label' => Mage::helper('partialpayment')->__('Pending')
            ),
            array(
                'value' => 'Processing',
                'label' => Mage::helper('partialpayment')->__('Processing')
            ),
			
            array(
                'value' => 'Complete',
                'label' => Mage::helper('partialpayment')->__('Complete')
            ),		
			 array(
                'value' => 'Canceled',
                'label' => Mage::helper('partialpayment')->__('Canceled')
            ),
        );
    }
}