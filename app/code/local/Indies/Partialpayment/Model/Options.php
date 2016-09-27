<?php
	
class Indies_Partialpayment_Model_Options
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '2_installments',
                'label' => Mage::helper('partialpayment')->__('2 Installments Only (Initial Deposit + 1 Installment)')
            ),
            array(
                'value' => 'fixed_installments',
                'label' => Mage::helper('partialpayment')->__('Fixed Installments (Buyer will pay, defined by admin)')
            ),
			array(
                'value' => 'flexy_payments',
                'label' => Mage::helper('partialpayment')->__('Flexy Payments (Buyer can select from installment plans)')
            )
        );
    }
}