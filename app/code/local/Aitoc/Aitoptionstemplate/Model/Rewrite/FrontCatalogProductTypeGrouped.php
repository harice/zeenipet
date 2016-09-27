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
class Aitoc_Aitoptionstemplate_Model_Rewrite_FrontCatalogProductTypeGrouped extends Mage_Catalog_Model_Product_Type_Grouped
{
    public function getAssociatedProducts($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyAssociatedProducts)) {
            $associatedProducts = array();

            if (!Mage::app()->getStore()->isAdmin()) {
                $this->setSaleableStatus($product);
            }

            $collection = $this->getAssociatedProductCollection($product)
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions()
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($product))
                ->addAttributeToFilter('status', array('in' => $this->getStatusFilters($product)));
                
// START AITOC OPTIONS TEMPLATE  

            $product2required = Mage::getResourceModel('aitoptionstemplate/aitproduct2required');
            
            $collection->getSelect()->joinLeft($product2required->getTable('aitoptionstemplate/aitproduct2required')." AS p2t"," p2t.product_id = e.entity_id");
                        
            $collection->getSelect()->where('p2t.required_options IS NULL OR p2t.required_options != 1');
                
// FINISH AITOC OPTIONS TEMPLATE                
                
            foreach ($collection as $product) {
                $associatedProducts[] = $product;
            }

            $this->getProduct($product)->setData($this->_keyAssociatedProducts, $associatedProducts);
        }
        return $this->getProduct($product)->getData($this->_keyAssociatedProducts);
    }

}