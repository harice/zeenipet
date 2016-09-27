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

class Aitoc_Aitoptionstemplate_Block_Rewrite_AdminhtmlCatalogProductEditTabOptions extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitcommonfiles/design--adminhtml--default--default--template--catalog--product--edit--options.phtml');
    }

	protected function _toHtml()
    {
        if($this->getRequest()->getParam('aitflag') == 1) {
            if (strpos(parent::_toHtml(), '<option value="aitcustomer_image">')) {
			    return str_replace('<option value="aitcustomer_image">Aitoc Customer Image</option>', '', parent::_toHtml());
            } else {
                return str_replace('<option value="aitcustomer_image" >Aitoc Customer Image</option>', '', parent::_toHtml());
			}
        } else {
            return parent::_toHtml();
        }
    }

    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Add New Option'),
                    'class' => 'add',
                    'id'    => 'add_new_defined_option'
                ))
        );

        $this->setChild('options_box',
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_options_option')
        );
        
        // START AITOC OPTIONS TEMPLATE          

        $this->setChild('save_template_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Save All Options As New Template'),
                    'class' => 'scalable save',
                    'id'    => 'save_template_button'
                ))
        );

        $this->setChild('add_template_set_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Add Template Options'),
                    'class' => 'add',
                    'id'    => 'add_new_template_set'
                ))
        );

        $this->setChild('add_template_tpl_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Assign Whole Template'),
                    'class' => 'add',
                    'id'    => 'add_new_template_tpl'
                ))
        );

        // FINISH AITOC OPTIONS TEMPLATE

        
        return $this;
#        return parent::_prepareLayout();
    }

    public function getSaveTemplateButtonHtml()
    {
        return '';
//        return $this->getChildHtml('save_template_button');
    }

    public function getAddTemplateAsSetButtonHtml()
    {
        return $this->getChildHtml('add_template_set_button');
    }

    public function getAddTemplateAsTplButtonHtml()
    {
        return $this->getChildHtml('add_template_tpl_button');
    }
    
    public function getAddTemplateAsSaveButtonHtml()
    {
        return $this->getChildHtml('save_template_button');
    }

    public function checkTemplateAllowed()
    {
    	if (Mage::helper('aitoptionstemplate')->getRequestAitflag())
        {
            return false;
        }
        else 
        {
            return true;
        }
    }
    
    
    public function getTemplateHash()
    {
        return Mage::helper('aitoptionstemplate')->getTemplateHash();
    }

}