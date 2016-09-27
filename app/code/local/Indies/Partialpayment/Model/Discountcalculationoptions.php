<?php
	
class Indies_Partialpayment_Model_Discountcalculationoptions
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '1',
                'label' => Mage::helper('partialpayment')->__('Discount will be deducted from first installment')
            ),
            array(
                'value' => '2',
                'label' => Mage::helper('partialpayment')->__('Discount distributed equally in all installments')
            )
        );
    }
}