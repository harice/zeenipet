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
class Aitoc_Aitoptionstemplate_Model_Reserve_Catalog_Product_Option_Dependable extends Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/reserve_catalog_product_option_dependable');
    }
    
    protected function _getChildTable()
    {
        return Mage::getSingleton('core/resource')->getTableName('aitoptionstemplate/reserve_product_option_dependable_child');
    }

    public function _beforeDelete()
    {
        $this->getResource()->getReadConnection()->delete(
           $this->_getChildTable(),//$this->getTable('aitoptionstemplate/reserve_product_option_dependable'),
           'row_id = "' . (int)$this->getRowId() . '" '
        );

    }
}