<?php
	
class Indies_Partialpayment_Model_Surchargeoptions
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'no_surcharge',
                'label' => Mage::helper('partialpayment')->__('No Surcharge')
            ),
            array(
                'value' => 'single_surcharge',
                'label' => Mage::helper('partialpayment')->__('Single Surcharge')
            ),
            array(
                'value' => 'multiple_surcharge',
                'label' => Mage::helper('partialpayment')->__('Multiple Surcharge')
            )
        );
    }
}