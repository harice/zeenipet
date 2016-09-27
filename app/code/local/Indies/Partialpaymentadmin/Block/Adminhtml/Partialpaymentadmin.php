<?php

class Indies_Partialpaymentadmin_Block_Adminhtml_Partialpaymentadmin extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_partialpaymentadmin';
		$this->_blockGroup = 'partialpaymentadmin';
		$this->_headerText = Mage::helper('partialpaymentadmin')->__('Partially Paid Orders');
		$this->_addButtonLabel = Mage::helper('partialpaymentadmin')->__('Add Item');
		parent::__construct();
		$this->_removeButton('add');
	}
}