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

class Aitoc_Aitoptionstemplate_Model_Mysql4_Aitproduct2required extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Varien class constructor
     *
     */
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/aitproduct2required', 'product_id');
    }
    
    public function getTemplateProductsByRequired($templateId,$value)
    {
        if(!empty($templateId) && in_array($value, array(1,0)))
        {
            $products = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl')->getTemplateProducts($templateId);
            $sql = "SELECT product_id FROM " . $this->getMainTable();
            $sql .= " WHERE required_options = {$value}";
            $sql .= " AND  product_id IN(" . join(",",$products) . ")";
            if(!empty($products))
            {
                return $this->_getWriteAdapter()->fetchAll($sql);
            }
        }           
        return array();        
    }
    
    public function updateProductsRequireOptions($templateId,$store = null)
    {
        if(!empty($templateId))
        {
            $this->_updateProductsRequireOption($this->getTemplateProductsByRequired($templateId,0), 0,$store);
            $this->_updateProductsRequireOption($this->getTemplateProductsByRequired($templateId,1), 1,$store);
        }           
        return $this;
    }
    
    protected function _updateProductsRequireOption(array $ids,$value,$store = null)
    {
        $tbl = Mage::helper('aitoptionstemplate')->getProductTableName($store);
        if(!empty($ids))
        {
            $ids = array_map(array($this,'prepareProductIds'), $ids);
            $this->_getWriteAdapter()->update($tbl, 
                                              array('required_options' => new Zend_Db_Expr($value)),
                                              'entity_id IN(' . join(",",$ids) . ')');
        }   
        return $this; 
    }
    
    public function prepareProductIds(array &$product)
    {
        return $product['product_id'];
    }
    
    public function setTemplateHasRequiredOptions($iTemplateId, $bHasRequired, $aProductOldHash)
    {
        $product2tpl = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
        
        $aProductHash = $product2tpl->getTemplateProducts($iTemplateId);
        
        
    	if ($aProductHash)
    	{
        	$product2required = Mage::getResourceModel('aitoptionstemplate/aitproduct2required');
        	
    	    if ($bHasRequired)
    	    {
    	        foreach ($aProductHash as $iProductId)
    	        {
                	$model = Mage::getModel('aitoptionstemplate/aitproduct2required');
        	
        	        $model->load($iProductId);
        	        
        	        if (!$model->getData())
        	        {
        	            $this->_afterSaveUpdateRequiredProductOption($iProductId,1);
                    	$this->_getWriteAdapter()->insert(
                    	    $product2required->getTable('aitoptionstemplate/aitproduct2required'),
                    	    array('product_id' => $iProductId, 'required_options' => 1)
                    	);
                        
        	        }
    	        }
    	    }
    	    else 
    	    {
    	        foreach ($aProductHash as $iProductId)
    	        {
                	$model = Mage::getModel('aitoptionstemplate/aitproduct2required');
        	
        	        $model->load($iProductId);
        	        
        	        if ($model->getData())
        	        {
        	            if (!$product2tpl->checkProductHasRequiredTemplateOptions($iProductId))
        	            {
                            $this->_afterSaveUpdateRequiredProductOption($iProductId,0);
                        	$this->_getWriteAdapter()->delete(
                        	   $this->getTable('aitoptionstemplate/aitproduct2required'),
                        	   'product_id = "' . intval($iProductId) . '"'
                        	);
                            
        	            }
        	        }
    	        }
    	    }
    	}
    	
    	if ($aProductOldHash)
    	{
    	    $aProductIds = array_flip($aProductHash);
    	    
    	    foreach ($aProductOldHash as $iProductId)
    	    {
        	    if (!isset($aProductIds[$iProductId]))
        	    {
    	            if (!$product2tpl->checkProductHasRequiredTemplateOptions($iProductId))
    	            {
    	                $this->_afterSaveUpdateRequiredProductOption($iProductId,0);
                    	$this->_getWriteAdapter()->delete(
                    	   $this->getTable('aitoptionstemplate/aitproduct2required'),
                    	   'product_id = "' . intval($iProductId) . '"'
                    	);
    	            }
        	    }
    	    }
    	}
    }    
    
    protected function _afterSaveUpdateRequiredProductOption($iProductId,$value = null)
    {
        if(!empty($iProductId))
        {
            foreach(Mage::helper('aitoptionstemplate')->getStoreCollection()->getItems() as $store)
            {
                $this->updateProductRequireOption($iProductId,$store->getStoreId(),$value);           
            }             
        }
        return $this;
    }
    
    public function setProductHasRequiredOptions($iProductId)
    {
        $product2tpl = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
        
        if ($product2tpl->checkProductHasRequiredTemplateOptions($iProductId))
        {
        	$model = Mage::getModel('aitoptionstemplate/aitproduct2required');
	
	        $model->load($iProductId);
	        
	        if (!$model->getData())
	        {
            	$this->_getWriteAdapter()->insert(
            	    $this->getTable('aitoptionstemplate/aitproduct2required'),
            	    array('product_id' => $iProductId, 'required_options' => 1)
            	);
	        }
        }
        else 
        {
        	$this->_getWriteAdapter()->delete(
        	   $this->getTable('aitoptionstemplate/aitproduct2required'),
        	   'product_id = "' . intval($iProductId) . '"'
        	);
        }
        
        return true;
    }

    public function isProductHasRequiredOptions($productId)
    {
         if(!empty($productId))
         {
              return $this->_getReadAdapter()->select()
                                                ->from($this->getMainTable(),'required_options')
                                                ->where('product_id = ?',$productId)
                                                ->query()
                                                    ->fetch();
         }
         return null;
    }
    
    public function updateProductRequireOption($productId,$storeId = null,$value = null)
    {
        if(!empty($productId))
        {
            $tbl = Mage::helper('aitoptionstemplate')->getProductTableName($storeId);
            if(empty($value) && $value != '0')
            {
                $value = $this->isProductHasRequiredOptions($productId);  
                $value = $value['required_options'];  
            }
            if(empty($value))
            {
                $value = '0';
            }
            $sql = "UPDATE {$tbl} SET required_options = {$value} WHERE entity_id = {$productId}";
            $this->_getWriteAdapter()->exec($sql);              
        }   
        return $this; 
    }
    
}