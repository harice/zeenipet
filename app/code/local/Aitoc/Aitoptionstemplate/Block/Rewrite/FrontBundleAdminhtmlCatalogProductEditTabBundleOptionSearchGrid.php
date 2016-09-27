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

class Aitoc_Aitoptionstemplate_Block_Rewrite_FrontBundleAdminhtmlCatalogProductEditTabBundleOptionSearchGrid
extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid
{
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStore($this->getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToFilter('type_id', array('in' => $this->getAllowedSelectionTypes()))
            ->addFilterByRequiredOptions()
            ->addStoreFilter();

// START AITOC OPTIONS TEMPLATE  
        $product2required = Mage::getResourceModel('aitoptionstemplate/aitproduct2required');
        
        $collection->getSelect()->joinLeft($product2required->getTable('aitoptionstemplate/aitproduct2required')." AS p2t"," p2t.product_id = e.entity_id", array('required_options'));
                    
        $collection->getSelect()->where('p2t.required_options IS NULL OR p2t.required_options != 1');
#d($collection->getSelect()->__toString(), 1);                
// FINISH AITOC OPTIONS TEMPLATE                
            
        if ($products = $this->_getProducts()) {
            $collection->addIdFilter($this->_getProducts(), true);
        }

        if ($this->getFirstShow()) {
            $collection->addIdFilter('-1');
            $this->setEmptyText($this->__('Please enter search conditions to view products.'));
        }

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
}

?>