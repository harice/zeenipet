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
class Aitoc_Aitoptionstemplate_Model_Mysql4_Aitoption2tpl extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Varien class constructor
     *
     */
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/aitoption2tpl', 'template_id');
    }
    
    /**
     * Deletes all options relation for specified template
     *
     * @param integer $iTemplateId
     */
    public function clearTemplateOptions($iTemplateId)
    {
    	$this->_getWriteAdapter()->delete(
    	   $this->getTable('aitoptionstemplate/aitoption2tpl'),
    	   'template_id = "' . intval($iTemplateId) . '"'
    	);
    }
    
    /**
     * Adds option-template relation
     *
     * @param integer $iTemplateId
     * @param integer $iOptionId
     */
    public function addRelationship($iTemplateId, $iOptionId)
    {
    	$this->_getWriteAdapter()->insert(
    	    $this->getTable('aitoptionstemplate/aitoption2tpl'),
    	    array(
    	        'option_id'    => $iOptionId,
    	        'template_id'  => $iTemplateId,
    	    )
    	);   
    }
    
    public function getTemplateOptions($iTemplateId)
    {
    	$stmt = $this->_getReadAdapter()->select()
    	   ->from($this->getTable('aitoptionstemplate/aitoption2tpl'), 'option_id')
    	   ->where('template_id = ?', $iTemplateId);
    	$aOptions = $this->_getReadAdapter()->fetchCol($stmt);
    	return $aOptions;
    }
    
    public function getOptionTemplateHash($aTemplateIds)
    {
        if (!$aTemplateIds) return false;
        
    	$stmt = $this->_getReadAdapter()->select()
    	   ->from($this->getTable('aitoptionstemplate/aitoption2tpl'), array('option_id', 'template_id'))
    	   ->where('template_id IN (' . implode(',', $aTemplateIds) .  ')')
    	   ;
    	$aOptionTemplateHash = $this->_getReadAdapter()->fetchPairs($stmt);
    	return $aOptionTemplateHash;
    }
    
    public function checkProductHasRequiredTemplateOptions($iTemplateId)
    {
    	$stmt = $this->_getReadAdapter()->select()
    	   ->from($this->getTable('aitoptionstemplate/aitoption2tpl')." AS o2t", array('option_id'))
    	   ->joinInner($this->getTable('catalog/product_option')." AS op"," o2t.option_id = op.option_id")
    	   ->where('o2t.template_id = ?', $iTemplateId)
    	   ->where('op.is_require = ?', 1);
    	   
    	$aOptionHash = $this->_getReadAdapter()->fetchCol($stmt);   

    	if ($aOptionHash)
    	{
    	    return true;
    	}
    	else 
    	{
    	    return false;
    	}
    }
    
    public function checkProductHasRequiredOptions($productId)
    {
    	$stmt = $this->_getReadAdapter()->select()
    	   ->from($this->getTable('catalog/product_option')." AS op", array('option_id'))
    	   ->where('op.product_id = ?', $productId)
    	   ->where('op.is_require = ?', 1);
    	$result = $this->_getReadAdapter()->fetchCol($stmt);
    	return empty($result) ? false : true;        
    }
    
    public function checkProductHasOptions($productId)
    {
    	$stmt = $this->_getReadAdapter()->select()
    	   ->from($this->getTable('catalog/product_option')." AS op", array('option_id'))
    	   ->where('op.product_id = ?', $productId);
    	$result = $this->_getReadAdapter()->fetchCol($stmt);
    	return empty($result) ? false : true;        
    }
    
}