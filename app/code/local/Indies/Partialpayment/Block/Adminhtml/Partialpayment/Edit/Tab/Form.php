<?php

class Indies_Partialpayment_Block_Adminhtml_Partialpayment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('partialpayment_form', array('legend'=>Mage::helper('partialpayment')->__('Partially Paid Order\'s Information')));
     
      $fieldset->addField('partial_payment_id', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Partial Payment Id'),
          'name'      => 'partial_payment_id',
      ));

      $fieldset->addField('order_id', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Order Id'),
          'name'      => 'order_id',
	  ));

      $fieldset->addField('customer_id', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Customer Id'),
          'name'      => 'customer_id',
	  ));

      $fieldset->addField('customer_firstname', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('First Name'),
          'name'      => 'customer_firstname',
	  ));

      $fieldset->addField('customer_lastname', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Last Name'),
          'name'      => 'customer_lastname',
	  ));

      $fieldset->addField('email', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Email'),
          'name'      => 'email',
	  ));

      $fieldset->addField('total_amount', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Total Amount'),
          'name'      => 'total_amount',
	  ));

      $fieldset->addField('paid_amount', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Paid Amount'),
          'name'      => 'paid_amount',
	  ));

      $fieldset->addField('remaining_amount', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Remaining Amount'),
          'name'      => 'remaining_amount',
	  ));

      $fieldset->addField('total_installment', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Total Installment'),
          'name'      => 'total_installment',
	  ));

      $fieldset->addField('paid_installment', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Paid Installment'),
          'name'      => 'paid_installment',
	  ));

      $fieldset->addField('created_date', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Created Date'),
          'name'      => 'created_date',
	  ));

      $fieldset->addField('updated_date', 'label', array(
          'label'     => Mage::helper('partialpayment')->__('Updated Date'),
          'name'      => 'updated_date',
	  ));

      $fieldset->addField('partial_payment_status', 'select', array(
          'label'     => Mage::helper('partialpayment')->__('Partial Payment Status'),
          'name'      => 'partial_payment_status',
          'values'    => Mage::getSingleton('partialpayment/partialpaymentstatus')->toOptionArray(),
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getPartialpaymentData() )
      {
		  $firstname = '';
		  $lastname = '';
		  $email = '';

	  	  if (Mage::getSingleton('adminhtml/session')->getCustomerId()) {
		  	$customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('adminhtml/session')->getCustomerId());

			$firstname = $customer->getFirstname();
			$lastname = $customer->getLastname();
			$email = $customer->getEmail();
		  }

		  Mage::getSingleton('adminhtml/session')->setCustomerFirstname($firstname);
		  Mage::getSingleton('adminhtml/session')->setCustomerLastname($lastname);
		  Mage::getSingleton('adminhtml/session')->setEmail($email);

          $form->setValues(Mage::getSingleton('adminhtml/session')->getPartialpaymentData());
          Mage::getSingleton('adminhtml/session')->setPartialpaymentData(null);
      } elseif ( Mage::registry('partialpayment_data') ) {
		  $firstname = '';
		  $lastname = '';
		  $email = '';

	  	  if (Mage::registry('partialpayment_data')->getCustomerId()) {
		  	$customer = Mage::getModel('customer/customer')->load(Mage::registry('partialpayment_data')->getCustomerId());

			$firstname = $customer->getFirstname();
			$lastname = $customer->getLastname();
			$email = $customer->getEmail();
		  }

		  Mage::registry('partialpayment_data')->setCustomerFirstname($firstname);
		  Mage::registry('partialpayment_data')->setCustomerLastname($lastname);
		  Mage::registry('partialpayment_data')->setEmail($email);

          $form->setValues(Mage::registry('partialpayment_data')->getData());
      }
      return parent::_prepareForm();
  }
}