<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Block_Adminhtml_Cnslidercat_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cnslidercat_list_grid');
        $this->setDefaultSort('cat_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for Grid
     *
     * @return Cybernetikz_Cnslider_Block_Adminhtml_Grid
     */
    protected function _prepareCollection()
    {
		$collection = Mage::getModel('cybernetikz_cnslider/cat')->getResourceCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('cat_id', array(
            'header'    => Mage::helper('cybernetikz_cnslider')->__('ID'),
            'width'     => '50px',
            'index'     => 'cat_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('cybernetikz_cnslider')->__('Title'),
            'index'     => 'title',
        ));
		
		$this->addColumn('settings', array(
            'header'    => Mage::helper('cybernetikz_cnslider')->__('Configuration'),
            'index'     => 'settings',
			'type'  	=> 'options',
			'options' => array("system_configuration"=>"System Configuration","custom_configuration"=>"Custom Configuration"),
			'width' 	=> '250px',
        ));
		
		$this->addColumn('sort_order', array(
            'header'    => Mage::helper('cybernetikz_cnslider')->__('Order'),
            'index'     => 'sort_order',
			'width' 	=> '150px',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('cybernetikz_cnslider')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(/*array(
                    'caption' => Mage::helper('cybernetikz_cnslider')->__('Edit'),
                    *///'url'     => array('base' => '*/*/edit'),
                    /*'field'   => 'id'
                ),*/array(
                    'caption' => Mage::helper('cybernetikz_cnslider')->__('Preview'),
                    'url'     => array('base' => 'slider/index/preview'),
					'target'=>'_blank',
                    'field'   => 'id'
                )),
				'align'		=>'center',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'slider',
        ));

        return parent::_prepareColumns();
    }

   protected function _prepareMassaction()
    {
        $this->setMassactionIdField('cat_id');
        $this->getMassactionBlock()->setFormFieldName('slidercat');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('cybernetikz_cnslider')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('cybernetikz_cnslider')->__('Are you sure?')
        ));

        return $this;
    }
	
    /**
     * Return row URL for js event handlers
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}