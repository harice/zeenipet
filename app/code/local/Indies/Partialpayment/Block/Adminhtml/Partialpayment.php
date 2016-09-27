<?php
class Indies_Partialpayment_Block_Adminhtml_Partialpayment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_partialpayment';
    $this->_blockGroup = 'partialpayment';
    $this->_headerText = Mage::helper('partialpayment')->__('Partially Paid Orders');
    $this->_addButtonLabel = Mage::helper('partialpayment')->__('Add Item');
    parent::__construct();
	$this->_removeButton('add');
  }
}