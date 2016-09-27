<?php

class Indies_Partialpayment_Block_Adminhtml_Partialpayment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'partialpayment';
        $this->_controller = 'adminhtml_partialpayment';
        
        $this->_updateButton('save', 'label', Mage::helper('partialpayment')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('partialpayment')->__('Delete Item'));
		$this->_removeButton('delete');
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('partialpayment_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'partialpayment_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'partialpayment_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('partialpayment_data') && Mage::registry('partialpayment_data')->getId() ) {
            return Mage::helper('partialpayment')->__("Edit Partially Paid Order's Information # %s", $this->htmlEscape(Mage::registry('partialpayment_data')->getId()));
        } else {
            return Mage::helper('partialpayment')->__('Add Item');
        }
    }
}