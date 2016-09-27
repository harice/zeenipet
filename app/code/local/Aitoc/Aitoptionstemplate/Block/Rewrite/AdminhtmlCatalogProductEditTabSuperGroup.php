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

class Aitoc_Aitoptionstemplate_Block_Rewrite_AdminhtmlCatalogProductEditTabSuperGroup extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group
{
    protected function _prepareCollection()
    {
        $allowProductTypes = array();
        foreach (Mage::getConfig()->getNode('global/catalog/product/type/grouped/allow_product_types')->children() as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $collection = Mage::getModel('catalog/product_link')->useGroupedLinks()
            ->getProductCollection()
            ->setProduct($this->_getProduct())
            ->addAttributeToSelect('*')
            ->addFilterByRequiredOptions()
            ->addAttributeToFilter('type_id', $allowProductTypes);
            
// START AITOC OPTIONS TEMPLATE  
        
        $product2required = Mage::getResourceModel('aitoptionstemplate/aitproduct2required');
        
        $collection->getSelect()->joinLeft($product2required->getTable('aitoptionstemplate/aitproduct2required')." AS p2t"," p2t.product_id = e.entity_id");
                    
        $collection->getSelect()->where('p2t.required_options IS NULL OR p2t.required_options != 1');
        
// FINISH AITOC OPTIONS TEMPLATE            

        $this->setCollection($collection);
        
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

}