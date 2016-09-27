<?php

class Indies_Partialpayment_Model_Status extends Varien_Object
{
    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    static public function toOptionArray()
    {
        return array(
            array(
                'value' => self::STATUS_ENABLED,
                'label' => Mage::helper('partialpayment')->__('Enabled')
            ),
            array(
                'value' => self::STATUS_DISABLED,
                'label' => Mage::helper('partialpayment')->__('Disabled')
            )
        );
    }
}