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
class Aitoc_Aitoptionstemplate_Block_Product_Option_Dependable extends Mage_Core_Block_Template
{
    
    protected $_productInstance;
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
            }
        }
        return $this->_productInstance;
    }
    
    function getJsValues()
    {
        $product = $this->getProduct();
        if(!$product->getId()) {
            return '{}';
        }
        //$product will have all options attached here, even the one from templates
        if(!$collection = $product->getAitDependableOptionsCollection()) {
            $collection = Mage::getModel('aitoptionstemplate/product_option_dependable')->getCollection();
            /** @var $collection Aitoc_Aitoptionstemplate_Model_Mysql4_Product_Option_Dependable_Collection */
            $_helper = Mage::helper('aitoptionstemplate');
            $collection->joinTemplates()->loadByProductOptions($product);
        }
        foreach($collection as $item) {
            $option = $product->getOptionById($item->getOptionId());
            $this->_addItems($item, $option);
        }
        $return = new Varien_Object(array('data'=>$this->_return));
        return $return->toJson();
    }
    
    protected function _addItems($item, $option)
    {
        if(!isset($this->_return[$item->getOptionId()])) $this->_return[$item->getOptionId()] = array();
        $row_id = $item->getOptionValueId();
        $children = $item->getDefaultChildren();
        if($item->getTemplateId()) {
            //if product have more than one template assigned template rows can be mixed, because of that we add template id to rows to make them unique
            $row_id = $item->getTemplateId() . $row_id;
            foreach($children as $id => $child_id) {
                $children[$id] = $item->getTemplateId() . $child_id;
            }
        }
        $array = array(
            //on Frontend negative values are used for templates, positive - for default options
            'row_id' => $row_id,
            'child_rows' => implode(',', $children),
        );
        if(!$item->getOptionTypeId()) {
            $array['option_type'] = $option->getType();
        }
        $this->_return[$item->getOptionId()][$item->getOptionTypeId()] = $array;
    }
}