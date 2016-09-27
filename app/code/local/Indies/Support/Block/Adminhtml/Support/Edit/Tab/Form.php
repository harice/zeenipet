<?php

class Indies_Support_Block_Adminhtml_Support_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('support_form', array('legend'=>Mage::helper('support')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('support')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('support')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('support')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('support')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('support')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('support')->__('Content'),
          'title'     => Mage::helper('support')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getSupportData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSupportData());
          Mage::getSingleton('adminhtml/session')->setSupportData(null);
      } elseif ( Mage::registry('support_data') ) {
          $form->setValues(Mage::registry('support_data')->getData());
      }
	  echo "hiii";
      return parent::_prepareForm();
  }
}