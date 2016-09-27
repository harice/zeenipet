<?php

class Indies_Partialpayment_Block_Adminhtml_Partialpayment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('partialpaymentGrid');
      $this->setDefaultSort('partial_payment_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
       $partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

	   if($partialpaymentHelper->isEnabled())
	   {
			//fetching current customer id 
			$customer_id = Mage::getSingleton('customer/session')->getId();

			/* changes Start by Indies Services on 03-11-2012 */ 
			$collection = Mage::getModel('sales/order')->getCollection()
						  ->addFieldToFilter('status',array('nin' => array('canceled','close')));

			$arrInstallment = array();

			if(sizeof($collection)) 
			{
				foreach ($collection as $order) 
				{
					$arrInstallment[] = $order->getIncrementId();
				}
			}

			if(sizeof($arrInstallment) == 0)
			{
				$arrInstallment[0] = 0;
			}

			$collection = Mage::getModel('partialpayment/partialpayment')->getCollection()
						->setOrder('partial_payment_id','DESC')
						->addFieldToFilter('order_id',array('in' => $arrInstallment));
				
			/* changes End by Indies Services on 03-11-2012 */ 
			$this->setCollection($collection);
	    }

      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('partial_payment_id', array(
          'header'    => Mage::helper('partialpayment')->__('ID'),
          'align'     =>'center',
          'width'     => '50px',
          'index'     => 'partial_payment_id',
      ));

      $this->addColumn('order_id', array(
          'header'    => Mage::helper('partialpayment')->__('Order ID'),
          'align'     =>'center',
          'index'     => 'order_id',
      ));

      $this->addColumn('customer_id', array(
          'header'    => Mage::helper('partialpayment')->__('Customer ID'),
          'align'     =>'center',
          'index'     => 'customer_id',
      ));

      $this->addColumn('customer_first_name', array(
          'header'    => Mage::helper('partialpayment')->__('First Name'),
          'align'     =>'left',
          'index'     => 'customer_first_name',
      ));

      $this->addColumn('customer_last_name', array(
          'header'    => Mage::helper('partialpayment')->__('Last Name'),
          'align'     =>'left',
          'index'     => 'customer_last_name',
      ));

      $this->addColumn('customer_email', array(
          'header'    => Mage::helper('partialpayment')->__('Email'),
          'align'     =>'left',
          'index'     => 'customer_email',
      ));

      $this->addColumn('total_amount', array(
          'header'    => Mage::helper('partialpayment')->__('Total Amount'),
          'align'     =>'right',
		  'type'  => 'number',
		  /*'type'  => 'price',
		  'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),*/
          'index'     => 'total_amount',
      ));
		
      $this->addColumn('paid_amount', array(
          'header'    => Mage::helper('partialpayment')->__('Paid Amount'),
          'align'     =>'right',
		  'type'  => 'number',
		  /*'type'  => 'price',
		  'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),*/
          'index'     => 'paid_amount',
      ));

      $this->addColumn('remaining_amount', array(
          'header'    => Mage::helper('partialpayment')->__('Due Amount'),
          'align'     =>'right',
		  'type'  => 'number',
		  /*'type'  => 'price',
		  'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),*/
          'index'     => 'remaining_amount',
      ));

      $this->addColumn('total_installment', array(
          'header'    => Mage::helper('partialpayment')->__('Total Installment'),
          'align'     =>'center',
          'index'     => 'total_installment',
      ));

      $this->addColumn('paid_installment', array(
          'header'    => Mage::helper('partialpayment')->__('Paid Installment'),
          'align'     =>'center',
          'index'     => 'paid_installment',
      ));

      $this->addColumn('created_date', array(
          'header'    => Mage::helper('partialpayment')->__('Created Date'),
          'align'     =>'center',
          'index'     => 'created_date',
      ));

      $this->addColumn('updated_date', array(
          'header'    => Mage::helper('partialpayment')->__('Updated Date'),
          'align'     =>'center',
          'index'     => 'updated_date',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('partialpayment')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('partial_payment_status', array(
          'header'    => Mage::helper('partialpayment')->__('Partial Payment Status'),
          'align'     => 'center',
          'width'     => '80px',
          'index'     => 'partial_payment_status',
          'type'      => 'options',
          'options'   => array(
              'Processing' => 'Processing',
              'Complete' => 'Complete',
			  'Pending' => 'Pending',
              'Canceled' => 'Canceled'
			 
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('partialpayment')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('partialpayment')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('partialpayment')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('partialpayment')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('partial_payment_id');
        $this->getMassactionBlock()->setFormFieldName('partialpayment');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('partialpayment')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('partialpayment')->__('Are you sure?')
        ));

        $partial_payment_status = Mage::getSingleton('partialpayment/partialpaymentstatus')->toOptionArray();

        array_unshift($partial_payment_status, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('partial_payment_status', array(
             'label'=> Mage::helper('partialpayment')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'partial_payment_status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('partialpayment')->__('Partial Payment Status'),
                         'values' => $partial_payment_status
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}