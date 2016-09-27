<?php
class Indies_Partialpayment_Block_Adminhtml_Partialproduct extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'partialpayment';
        $this->_controller = 'adminhtml_partialproduct';
        $this->_headerText = Mage::helper('partialpayment')->__('Manage Contacts');      
        parent::__construct();
    }
}