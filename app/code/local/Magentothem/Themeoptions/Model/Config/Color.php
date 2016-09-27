<?php
/*------------------------------------------------------------------------
# Websites: http://www.plazathemes.com/
-------------------------------------------------------------------------*/ 
class Magentothem_Themeoptions_Model_Config_Color
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'green', 'label'=>Mage::helper('adminhtml')->__('Green')),
            array('value'=>'blue', 'label'=>Mage::helper('adminhtml')->__('Blue')),
            array('value'=>'red', 'label'=>Mage::helper('adminhtml')->__('Red')),
            array('value'=>'orange', 'label'=>Mage::helper('adminhtml')->__('Orange'))           
        );
    }

}
