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
class Aitoc_Aitoptionstemplate_Model_Mysql4_Reserve_Catalog_Product_Option_Dependable_Collection extends Aitoc_Aitoptionstemplate_Model_Mysql4_Product_Option_Dependable_Collection
{
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/reserve_catalog_product_option_dependable');
    }
    
    protected function _getChildTable()
    {
        return $this->getTable('aitoptionstemplate/reserve_product_option_dependable_child');
    }
    
        
}