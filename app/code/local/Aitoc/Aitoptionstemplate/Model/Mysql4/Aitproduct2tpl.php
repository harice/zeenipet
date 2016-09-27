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

class Aitoc_Aitoptionstemplate_Model_Mysql4_Aitproduct2tpl extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Varien class constructor
     *
     */
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/aitproduct2tpl', 'product_id');
    }
    
    /**
     * Deletes all options relation for specified template
     *
     * @param integer $iTemplateId
     */
    public function clearProductTemplates($iProductId)
    {
    	$this->_getWriteAdapter()->delete(
    	   $this->getTable('aitoptionstemplate/aitproduct2tpl'),
    	   'product_id = "' . intval($iProductId) . '"'
    	);
    }
    
    /**
     * Adds option-template relation
     *
     * @param integer $iTemplateId
     * @param integer $iOptionId
     */
    public function addRelationship($iProductId, $aData)
    {
#        $oTemplate = Mage::getModel('aitoptionstemplate/aittemplate')->load($aData['template_id']);
        
    	$this->_getWriteAdapter()->insert(
    	    $this->getTable('aitoptionstemplate/aitproduct2tpl'),
    	    array(
    	        'product_id'       => $iProductId,
    	        'template_id'      => $aData['template_id'],
    	        'sort_order'       => $aData['sort_order'],
#    	        'required_options' => $oTemplate->getData('required_options'),
    	    )
    	);
    	if(count($this->getProductTemplateOptions($iProductId)) > 0)
    	{
    	    foreach(Mage::helper('aitoptionstemplate')->getStoreCollection()->getItems() as $store)
        	{
                Mage::getResourceModel('aitoptionstemplate/aittemplate')->updateProductHasOptions($iProductId,$store->getStoreId(),1);      	    
        	}    	    
    	}
    }
    
    public function getProductTemplates($iProductId, $bActiveOnly = true)
    {
        if ($bActiveOnly)
        {
            $sWhereActive = 'at.is_active = 1';
        }
        else 
        {
            $sWhereActive = '1';
        }
        
    	$stmt = $this->_getReadAdapter()->select()
    	   ->from($this->getTable('aitoptionstemplate/aitproduct2tpl')." AS p2t", array('template_id', 'sort_order'))
    	   ->joinInner($this->getTable('aitoptionstemplate/aittemplate')." AS at"," p2t.template_id = at.template_id")
    	   ->where('p2t.product_id = ?', $iProductId)
    	   ->where($sWhereActive)
    	   ->order('p2t.sort_order DESC')
    	   ->order('p2t.template_id DESC');
    	$aTemplates = $this->_getReadAdapter()->fetchAll($stmt);
    	return $aTemplates;
    }
    
    public function getProductTemplateOptions($id)
    {
        if(!empty($id))
        {
        	$stmt = $this->_getReadAdapter()->select()
        	   ->from($this->getTable('aitoptionstemplate/aitoption2tpl')." AS p2t", array('option_id'))
        	   ->joinInner($this->getTable('aitoptionstemplate/aittemplate')." AS at"," p2t.template_id = at.template_id")
        	   ->joinInner($this->getTable('aitoptionstemplate/aitproduct2tpl')." AS pt3"," pt3.template_id = at.template_id")
        	   ->where('pt3.product_id = ?', $id);  
        	return $this->_getReadAdapter()->fetchAll($stmt);               
        }
        return array();
    }
    
    public function checkProductHasRequiredTemplateOptions($iProductId)
    {
    	$stmt = $this->_getReadAdapter()->select()
    	   ->from($this->getTable('aitoptionstemplate/aitproduct2tpl')." AS p2t", array('template_id'))
    	   ->joinInner($this->getTable('aitoptionstemplate/aittemplate')." AS at"," p2t.template_id = at.template_id")
    	   ->where('p2t.product_id = ?', $iProductId)
    	   ->where('at.required_options = ?', 1)
    	   ->where('at.is_active = 1');
    	   
    	$aTemplateHash = $this->_getReadAdapter()->fetchCol($stmt);   
    	
    	if ($aTemplateHash)
    	{
    	    return true;
    	}
    	else 
    	{
    	    return false;
    	}
    }
    
   public function getTemplateProducts($iTemplateId, $fetchAll = false)
    {
    	$fields = array('product_id');

		if ($fetchAll)
		{
			$fields[] = 'template_id';
			$fields[] = 'sort_order';
		}

		$stmt = $this->_getReadAdapter()->select()
    	   ->from($this->getTable('aitoptionstemplate/aitproduct2tpl'), $fields)
    	   ->where('template_id = ?', $iTemplateId);

		return $fetchAll ? $this->_getReadAdapter()->fetchAssoc($stmt) : $this->_getReadAdapter()->fetchCol($stmt);
    }
    
    public function getOptionTemplateHash___($aTemplateIds)
    {
        if (!$aTemplateIds) return false;
        
    	$stmt = $this->_getReadAdapter()->select()
    	   ->from($this->getTable('aitoptionstemplate/aitoption2tpl'), array('option_id', 'template_id'))
    	   ->where('template_id IN (' . implode(',', $aTemplateIds) .  ')')
    	   ;
    	$aOptionTemplateHash = $this->_getReadAdapter()->fetchPairs($stmt);
    	return $aOptionTemplateHash;
    }
    
    public function clearTemplateProducts($iTemplateId, $aProductHash)
    {
    	$this->_getWriteAdapter()->delete(
    	   $this->getTable('aitoptionstemplate/aitproduct2tpl'),
    	   'template_id = "' . intval($iTemplateId) . '" AND product_id IN (' . implode(',', $aProductHash) .')'
    	);

        foreach($aProductHash as $id)
        {
            Mage::getResourceModel('aitoptionstemplate/aittemplate')->checkProduct($id);
        }
    	
    }
    
    public function getWriteAdapter()
    {
        return $this->_getWriteAdapter();
    }
    
    public function getReadAdapter()
    {
        return $this->_getReadAdapter();
    }

	public function getSortOrderIds(Mage_Catalog_Model_Product $product)
	{
        $productId = $product->getId();
        if(!$productId) 
        {
            return false;
        }
        
		$stmt = $this->_getReadAdapter()->select()
			->from($this->getTable('catalog/product_option') . ' AS CPO', new Zend_Db_Expr('CPO.option_id, IF(ACPPT.sort_order IS NULL, CPO.sort_order, ACPPT.sort_order) as template_order'))
			->joinLeft($this->getTable('aitoptionstemplate/aitoption2tpl') . ' AS ACPOT', ' CPO.option_id = ACPOT.option_id', false)
			->joinLeft($this->getTable('aitoptionstemplate/aitproduct2tpl') . ' AS ACPPT', 'ACPOT.template_id = ACPPT.template_id', false)
			->orWhere('CPO.product_id = ?', $productId)
			->orWhere('ACPPT.product_id = ?', $productId)
			->order('template_order ASC')
			->order('CPO.sort_order ASC');

		return $this->_getReadAdapter()->fetchCol($stmt);
	}
    
}