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

class Aitoc_Aitoptionstemplate_Block_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'template_id';
        $this->_controller = 'promo_quote';

        parent::__construct();
        
        $template_id = (int)$this->getRequest()->getParam('template_id');
        if (!empty($template_id)) 
        {
            // to do - when edit!!!
            $aButtonData = array
            (
                'label'     => Mage::helper('catalog')->__('Duplicate'),
                'onclick'   => 'setLocation(\''.$this->getDuplicateUrl().'\')',
                'class'     => 'add'
            );
            $this->_addButton('duplicate', $aButtonData, 0);
        }
        $aSaveButtonData = array
        (
            'label'     => Mage::helper('aitoptionstemplate')->__('Save and Continue Edit'),
            'onclick'   => 'editForm.submit(\''.$this->getSaveAndEditUrl().'\');',
            'class'     => 'save'
        );
        $this->_addButton('saveandedit', $aSaveButtonData, 0);
        $this->_updateButton('save', 'label', Mage::helper('aitoptionstemplate')->__('Save Template'));
        $this->_updateButton('delete', 'label', Mage::helper('aitoptionstemplate')->__('Delete Template'));
        
    }
    
    public function getSaveUrl()
    {
        $iStoreId = Mage::app()->getFrontController()->getRequest()->get('store');
        return $this->getUrl('*/index/save', array('store' => $iStoreId));
    }
    
    public function getSaveAndEditUrl()
    {
        $iStoreId = Mage::app()->getFrontController()->getRequest()->get('store');
        return $this->getUrl('*/index/save', array('store' => $iStoreId ,'back' => true));
    }    
    
    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
    }    
    
    public function getHeaderText()
    {
        $tpl = Mage::registry('current_aitoptionstemplate_template');
        if ($tpl->getTemplateId()) {
            return Mage::helper('aitoptionstemplate')->__("Edit Template '%s'", $this->htmlEscape($tpl->getTitle()));
        }
        else {
            return Mage::helper('aitoptionstemplate')->__('New Template');
        }
    }
    
    public function getProductsJson()
    {
        return '{}';
    }
    
    
}