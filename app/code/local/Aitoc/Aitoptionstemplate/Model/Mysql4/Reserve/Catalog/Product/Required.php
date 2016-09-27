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
 * @copyright  Copyright (c) 2010 AITOC, Inc. 
 */
  class Aitoc_Aitoptionstemplate_Model_Mysql4_Reserve_Catalog_Product_Required extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/reserve_catalog_product_required','reserve_id');
    }
    
    protected function _getReserveData()
    {
        $sql = $this->_getReadAdapter()->select()
                ->from($this->getTable('aitoptionstemplate/aitproduct2required')." AS req"); 
                
        return $this->_getReadAdapter()->fetchAll($sql);          
    }
    
    protected function _clearReserveData()
    {
        return $this->_getWriteAdapter()->delete($this->getMainTable());
    }
    
    public function reserveRequiredProducts($templateId)
    {
        $this->_clearReserveData();
        $data = $this->_getReserveData();
        if(empty($data))
        {
            return false;
        }
        return $this->_getWriteAdapter()->insertArray($this->getMainTable(),array('product_id' , 'required_options') , $data);      
    }
}