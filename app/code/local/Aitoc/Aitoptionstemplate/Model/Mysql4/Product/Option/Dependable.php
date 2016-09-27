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
class Aitoc_Aitoptionstemplate_Model_Mysql4_Product_Option_Dependable extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/product_option_dependable','row_id');
    }
    
}