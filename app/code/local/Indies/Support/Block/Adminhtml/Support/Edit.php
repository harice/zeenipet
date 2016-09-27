<?php

class Indies_Support_Block_Adminhtml_Support_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'support';
        $this->_controller = 'adminhtml_support';
        
        $this->_updateButton('save', 'label', Mage::helper('support')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('support')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('support_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'support_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'support_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('support_data') && Mage::registry('support_data')->getId() ) {
            return Mage::helper('support')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('support_data')->getTitle()));
        } else {
            return Mage::helper('support')->__('Add Item');
        }
    }
}