<?php
/**
 * Custom Options Templates
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitoptionstemplate
 * @version      3.2.2
 * @license:     FoqAPsDYiV5k0g2ao7Zx8pPoJiVNkUYZBpxixzEerG
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitoptionstemplate_Block_Template_Grid  extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('aittemplateGrid');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
#        $this->setUseAjax(true);
        $this->setDefaultSort('template_id');
    }

    protected function _prepareLayout()
    {
    	parent::_prepareLayout();
        $this->setChild('new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Add New Template'),
                    'onclick'   => "setLocation('".$this->getUrl('*/*/new', array('template_id' => 0))."')",
                    'class'     => 'scalable add'
                ))
        );
        return $this;
    }
    
    public function getNewButtonHtml()
    {
        return $this->getChildHtml('new_button');
    }
    
    public function getMainButtonsHtml()
    {
    	$html = parent::getMainButtonsHtml();
    	$html .= $this->getNewButtonHtml();
    	return $html;
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('aitoptionstemplate/aittemplate_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
    	$this->addColumn('template_id', array(
            'header'=>Mage::helper('aitoptionstemplate')->__('ID'),
            'width'     => '50px',
            'sortable'=>true,
            'index'=>'template_id',
            'type'  => 'number',
        ));
        
    	$this->addColumn('title', array(
            'header'=>Mage::helper('aitoptionstemplate')->__('Template Title'),
            'sortable'=>true,
            'index'=>'title'
        ));
        
        $this->addColumn('description', array(
            'header'=>Mage::helper('aitoptionstemplate')->__('Template Description'),
            'sortable'=>true,
            'index'=>'description'
        ));
        
        $this->addColumn('is_active', array(
            'header'=>Mage::helper('catalog')->__('Active'),
            'sortable'=>true,
            'width'     => '50px',
            'index'=>'is_active',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ));
    	
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('template_id' => $row->getTemplateId()));
    }
}

?>