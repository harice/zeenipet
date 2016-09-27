<?php

class Indies_Partialpaymentadmin_Block_Adminhtml_Partialpaymentadmin_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'partialpaymentadmin';
        $this->_controller = 'adminhtml_partialpaymentadmin';

        $this->_updateButton('save', 'label', Mage::helper('partialpaymentadmin')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('partialpaymentadmin')->__('Delete'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue'),
            'onclick'   => 'saveAndContinue()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('partialpaymentadmin_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'partialpaymentadmin_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'partialpaymentadmin_content');
                }
            }

            function saveAndContinue(){
                editForm.submit('" . $this->getUrl('partialpaymentadmin/adminhtml_partialpaymentadmin/save', array('action' => 'saveandcontinue')) . "');
            }
        ";
    }

    public function getHeaderText()
    {
		return Mage::helper('partialpaymentadmin')->__("Edit Partially Paid Order's Information");
    }
}