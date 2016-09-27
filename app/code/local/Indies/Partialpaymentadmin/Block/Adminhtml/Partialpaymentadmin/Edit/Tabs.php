<?php

class Indies_Partialpaymentadmin_Block_Adminhtml_Partialpaymentadmin_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('partialpaymentadmin_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('partialpaymentadmin')->__('Partial Payment'));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
		  'label'     => Mage::helper('partialpaymentadmin')->__('Order Information'),
		  'title'     => Mage::helper('partialpaymentadmin')->__('Order Information'),
		  'content'   => $this->getLayout()->createBlock('partialpaymentadmin/adminhtml_partialpaymentadmin_edit_tab_form')->toHtml(),
		));

		return parent::_beforeToHtml();
	}
}