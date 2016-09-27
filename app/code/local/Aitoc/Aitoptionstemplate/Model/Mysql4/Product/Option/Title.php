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
  class Aitoc_Aitoptionstemplate_Model_Mysql4_Product_Option_Title extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {
        $this->_init('catalog/product_option_title','option_title_id');
        /*$resourceModel = (string) Mage::getConfig()->getNode()->global->models->{'catalog'}->resourceModel;
        //$entityConfig = $this->getEntity($resourceModel, $entity);
        $table=Mage::getConfig()->getNode()->global->models->{$resourceModel}->entities->{'product_option_title'};
        echo 'entity:::'.$resourceModel.' table---<pre>'.print_r($table,1),'</pre>';exit;
        $model=Mage::getResourceModel('catalog/product_option');
        $table=$model->getTable('catalog/product_option_title');
        //$this->_setMainTable($table,'option_title_id');
        */
    }

}