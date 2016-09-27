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

class Aitoc_Aitoptionstemplate_Model_Mysql4_Aittemplate extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Varien class constructor
     *
     */
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/aittemplate', 'template_id');
    }
    
    public function saveInSetIncluding()
    {
    	return $this;
    }
    
    public function updateCatalogProduct($templateId)
    {
        foreach(Mage::helper('aitoptionstemplate')->getStoreCollection()->getItems() as $store)
        {
            $products = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl')->getTemplateProducts($templateId);
            foreach($products as $id)
            {
                $this->checkProduct($id);
            }      
        }
        return $this;
    }
 /*   
    protected function _updateProductsHasOptions(int $templateId,$storeId = null)
    {
        if(!empty($templateId))
        {
            $options = Mage::getResourceModel('aitoptionstemplate/aitoption2tpl')->getTemplateOptions($templateId);
            
            $tbl = Mage::helper('aitoptionstemplate')->getProductTableName($storeId);
            $sql = "UPDATE {$tbl} SET has_options = ";
            $sql .= empty($options) ? "0" : "1";
            $sql .= " WHERE entity_id IN(" . join(",",$products) . ")";
            if(!empty($products))
            {
                $this->_getWriteAdapter()->exec($sql);
            }
        }   
        return $this;
    }*/
    
    public function updateProductHasOptions($productId,$storeId = null,$value = 0)
    {
        if(!empty($productId))
        {
            $this->_getWriteAdapter()->update(  Mage::helper('aitoptionstemplate')->getProductTableName($storeId), 
                                                array('has_options' => new Zend_Db_Expr($value)),
                                                'entity_id = ' . intval($productId));    
        }   
        return $this; 
    }
    
    public function checkProduct($productId)
    {
        $hasOptions = null;
        $requiredOptions = null;
        if(!empty($productId))
        {
            //check for standard product options
            $requiredOptions = intval(Mage::getResourceModel('aitoptionstemplate/aitoption2tpl')->checkProductHasRequiredOptions($productId));
            $hasOptions = intval(Mage::getResourceModel('aitoptionstemplate/aitoption2tpl')->checkProductHasOptions($productId));
            //check for standard product options
            $options = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl')->getProductTemplateOptions($productId);
            if(empty($hasOptions))
            {
                $hasOptions = empty($options) ? 0 : 1;    
            }
            if(empty($requiredOptions))
            {
                $requiredOptions = null;    
            }
            foreach(Mage::helper('aitoptionstemplate')->getStoreCollection()->getItems() as $store)
            {
                $this->updateProductHasOptions($productId,$store->getStoreId(),$hasOptions);    
                Mage::getResourceModel('aitoptionstemplate/aitproduct2required')->updateProductRequireOption($productId,$store->getStoreId(),$requiredOptions);               
            }
        }
        return $this;
    }
    
    public function delete(Mage_Core_Model_Abstract $object)
    {
        $iTemplateId = $object->getId();
        
    	$option2tpl = Mage::getResourceModel('aitoptionstemplate/aitoption2tpl');
    	$aOptionTemplateHash = $option2tpl->getTemplateOptions($iTemplateId);
        
    	$productIds = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl')->getTemplateProducts($iTemplateId);
    	
    	$oOption = Mage::getModel('catalog/product_option');
    	
    	if ($aOptionTemplateHash)
    	{
    	    foreach ($aOptionTemplateHash as $iOptionId)
    	    {
    	        $oOption->load($iOptionId);
    	        
                $oOption->getValueInstance()->deleteValue($iOptionId);
                $oOption->deletePrices($iOptionId);
                $oOption->deleteTitles($iOptionId);
                $oOption->delete();
    	    }
    	}
    	
    	$option2tpl->clearTemplateOptions($iTemplateId);
    	
    	foreach($productIds as $id)
    	{
    	    $this->checkProduct($id);
    	}
    	
    	Mage::getSingleton('index/indexer')->indexEvents();
    	
    	
        parent::delete($object);
    }

}