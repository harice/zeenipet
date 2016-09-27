<?php
	
class Indies_Partialpayment_Model_Applyto
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '1',
                'label' => Mage::helper('partialpayment')->__('All Products')
            ),
            array(
                'value' => '2',
                'label' => Mage::helper('partialpayment')->__('Selected Products Only')
            ),
			array(
                'value' => '3',
                'label' => Mage::helper('partialpayment')->__('Pre Order Products Only (Out of Stock)')
            ),
			array(
                'value' => '4',
                'label' => Mage::helper('partialpayment')->__('Whole Cart')
            )
        );
    }
}