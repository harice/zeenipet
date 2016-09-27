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
class Aitoc_Aitoptionstemplate_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_storeCollection = null;
    protected $_default_product_id = null;
    protected $_templateHash = null;
    
    public function getDefaultProductId()
    {
        if (is_null($this->_default_product_id))
        {
            $this->_default_product_id = Mage::getStoreConfig('general/aitoptionstemplate/default_product_id');
        }
        
        return $this->_default_product_id;
    }

    public function cacheManager()
    {
        if (0 == $this->getDefaultProductId())
        {
            Mage::app()->cleanCache();
        }        
    }
    
    public function getRequestAitflag()
    {
        return Mage::app()->getFrontController()->getRequest()->get('aitflag');
    }
    
    public function getRequestTemplateId()
    {
        if (Mage::registry('current_aitoptionstemplate_template'))
        {
            return Mage::registry('current_aitoptionstemplate_template')->getTemplateId();
        }
        
        return Mage::app()->getFrontController()->getRequest()->get('template_id');
    }
    
    /**
     * Return value of product which options are saved to template
     * @return integer
     */
    public function getRequestProductId()
    {
        return Mage::app()->getFrontController()->getRequest()->get('optproduct_id');
    }

    public function getTemplateHash()
    {
        if(is_null($this->_templateHash)) {
            $collection = Mage::getResourceModel('aitoptionstemplate/aittemplate_collection')
            ->addFieldToFilter('is_active', array('eq' => 1))
            ->load()
            ;

            $this->_templateHash = array();
            
            foreach ($collection as $oItem)
            {
                $this->_templateHash[$oItem->getId()] = $this->htmlEscape($oItem->getTitle());
            }
        }
        
        return $this->_templateHash;
    }    
    
    /**
     * Check is enable catalog product flat for store
     *
     * @param mixed $store
     * @return bool
     */   
     
    public function isFlatEnabled($store = null)
    {
        return Mage::helper('catalog/product_flat')->isEnabled($store);
    }
    
    public function getProductTableName($store = null)
    {
        return $this->isFlatEnabled($store) ? Mage::getResourceModel('catalog/product_flat')->getFlatTableName($store) : Mage::getResourceModel('catalog/product')->getTable('catalog/product');
    }
     
    public function getStoreCollection()
    {
        if(empty($this->_storeCollection))
        {
            $this->_storeCollection = Mage::getResourceModel('core/store_collection')->load();   
        }
        return $this->_storeCollection;
    }
}