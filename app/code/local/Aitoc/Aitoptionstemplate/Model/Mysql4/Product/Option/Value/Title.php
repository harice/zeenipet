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
  class Aitoc_Aitoptionstemplate_Model_Mysql4_Product_Option_Value_Title extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_option_type_title','option_type_title_id');
        /*$model=Mage::getResourceModel('catalog/product_option');
        $table=$model->getTable('catalog/product_option_price');
        $this->_setMainTable($table,'option_price_id');*/
        
    }
}