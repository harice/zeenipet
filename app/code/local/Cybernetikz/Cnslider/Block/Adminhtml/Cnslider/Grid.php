<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Block_Adminhtml_Cnslider_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cnslider_list_grid');
        $this->setDefaultSort('slider_id');
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
		$collection = Mage::getModel('cybernetikz_cnslider/slider')->getResourceCollection();
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
        $this->addColumn('slider_id', array(
            'header'    => Mage::helper('cybernetikz_cnslider')->__('ID'),
            'width'     => '50px',
            'index'     => 'slider_id',
        ));

        $this->addColumn('slider_image', array(
            'header'    => Mage::helper('cybernetikz_cnslider')->__('Banner Image'),
			'align' => 'center',
			'width'     => '70',
			'filter'	=>false,
            'index'     => 'slider_image',
			'renderer' => 'Cybernetikz_Cnslider_Block_Adminhtml_Cnslider_Grid_Image'
        ));
		
		$this->addColumn('name', array(
            'header'    => Mage::helper('cybernetikz_cnslider')->__('Title'),
            'index'     => 'name',
			'width'    => '150px',
        ));
		
		$this->addColumn('link_url', array(
			'header'    => Mage::helper('cybernetikz_cnslider')->__('Call to Action URL'),
			'index'     => 'link_url',
		));
					
		$this->addColumn('active_from', array(
            'header'   => Mage::helper('cybernetikz_cnslider')->__('Active From'),
            'sortable' => true,
            'width'    => '100px',
            'index'    => 'active_from',
            'type'     => 'datetime',
			'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)
        ));
		
		$this->addColumn('active_to', array(
            'header'   => Mage::helper('cybernetikz_cnslider')->__('Active To'),
            'sortable' => true,
            'width'    => '100px',
            'index'    => 'active_to',
            'type'     => 'datetime',
			'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)
        ));
		
		$this->addColumn('is_active', array(
            'header'    => Mage::helper('cybernetikz_cnslider')->__('Status'),
            'index'     => 'is_active',
			'width' 	=> '70px',
			'type'  => 'options',
            'options' => Mage::getSingleton('cybernetikz_cnslider/status')->getOptionArray(),
        ));
		
        $this->addColumn('created_at', array(
            'header'   => Mage::helper('cybernetikz_cnslider')->__('Created'),
            'sortable' => true,
            'width'    => '100px',
            'index'    => 'created_at',
            'type'     => 'datetime',
			'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)
        ));
		
		$this->addColumn('sort_order', array(
            'header'    => Mage::helper('cybernetikz_cnslider')->__('Order'),
            'index'     => 'sort_order',
			'width' 	=> '20px',
			'align'		=>'center'
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('cybernetikz_cnslider')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(/*array(
                    'caption' => Mage::helper('cybernetikz_cnslider')->__('Edit'),*/
                    //'url'     => array('base' => '*/*/edit'),
                    /*'field'   => 'id'
                )*/array(
                    'caption' => Mage::helper('cybernetikz_cnslider')->__('Preview'),
                    'url'     => array('base' => 'slider/index/bannerpreview/id'),
					'target'=>'_blank',
                    'field'   => 'id'
                )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'slider',
        ));

        return parent::_prepareColumns();
    }

   protected function _prepareMassaction()
    {
        $this->setMassactionIdField('slider_id');
        $this->getMassactionBlock()->setFormFieldName('sliderids');

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