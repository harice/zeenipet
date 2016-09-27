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
class Aitoc_Aitoptionstemplate_Block_Adminhtml_Product_Option_Dependable extends Mage_Core_Block_Template
{
    
    protected $_productInstance;
    protected $_max_row_id = 0;
    protected $_return = array();
    /**
     * Get Product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_productInstance) {
            if ($product = Mage::registry('product')) {
                $this->_productInstance = $product;
            } else {
                $this->_productInstance = Mage::getSingleton('catalog/product');
            }
        }
        return $this->_productInstance;
    }
    
    function getJsValues()
    {
        $product = $this->getProduct();
        $_helper = Mage::helper('aitoptionstemplate');
        if($product->getId() || Mage::registry('current_aitoptionstemplate_template')) {
            //loading templates for current opened product or template
            $collection = Mage::getModel('aitoptionstemplate/product_option_dependable')->getCollection();
            /** @var $collection Aitoc_Aitoptionstemplate_Model_Mysql4_Product_Option_Dependable_Collection */
            if($template_id = $_helper->getRequestTemplateId() ) {
                //editing  template
                $collection->loadByTemplateId( $template_id );
            } elseif($product_id = $_helper->getRequestProductId()) {
                //saving product options as template
                $collection->loadByProduct($product_id);
            } else {
                //editing product
                $collection->loadByProduct($product);
            }
            foreach($collection as $item) {
                $this->_addItems($item);
                $this->_max_row_id  = max( $this->_max_row_id, abs( $item->getOptionValueId()) );
            }
        }
        if(!Mage::registry('current_aitoptionstemplate_template')) {
            //check that we are not on template page and load all templates dependable options for custom options on product page, because all templates are loaded there
            //also we load this for new product page, where product id is not defined
            $collection = Mage::getModel('aitoptionstemplate/product_option_dependable')->getCollection();
            $collection->loadByProduct( $_helper->getDefaultProductId() );
            foreach($collection as $item) {
                $item->setOptionId( 'aitocoption'.$item->getOptionId() );
                $this->_addItems($item);
            }
        }
        $return = new Varien_Object(array('data'=>$this->_return,'last_row'=>$this->_max_row_id));
        return $return->toJson();
    }
    
    protected function _addItems($item)
    {
        if(!$item->getOptionId()) return false; //removing some incorrect templates
        if(!isset($this->_return[$item->getOptionId()])) $this->_return[$item->getOptionId()] = array();
        $this->_return[$item->getOptionId()][$item->getOptionTypeId()] = array(
            'row_id' => abs($item->getOptionValueId()),
            'child_rows' => implode(',', $item->getChildren()),
        );
    }

}