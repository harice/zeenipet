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

class Aitoc_Aitoptionstemplate_Block_Rewrite_AdminhtmlCatalogProductEditTabSuperConfigGrid extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid
{
    protected function _prepareCollection()
    {
        $allowProductTypes = array();
        foreach (Mage::getConfig()->getNode('global/catalog/product/type/configurable/allow_product_types')->children() as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $product = $this->_getProduct();
        $collection = $product->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('price')
            ->addFieldToFilter('attribute_set_id',$product->getAttributeSetId())
            ->addFieldToFilter('type_id', $allowProductTypes)
            ->addFilterByRequiredOptions();

// START AITOC OPTIONS TEMPLATE  
        $product2required = Mage::getResourceModel('aitoptionstemplate/aitproduct2required');
        
        $collection->getSelect()->joinLeft($product2required->getTable('aitoptionstemplate/aitproduct2required')." AS p2t"," p2t.product_id = e.entity_id");
                    
        $collection->getSelect()->where('p2t.required_options IS NULL OR p2t.required_options != 1');
#d($collection->getSelect()->__toString(), 1);        
// FINISH AITOC OPTIONS TEMPLATE                
            
        Mage::getModel('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($collection);

        foreach ($product->getTypeInstance(true)->getUsedProductAttributes($product) as $attribute) {
            $collection->addAttributeToSelect($attribute->getAttributeCode());
            $collection->addAttributeToFilter($attribute->getAttributeCode(), array('nin'=>array(null)));
        }

        $this->setCollection($collection);

// START AITOC OPTIONS TEMPLATE  

        $bIsNewVersion = true;
    
        $sVersion = Mage::getVersion();
    
        $aVersionParts = explode('.', $sVersion);
        
        if ($aVersionParts[0] < 1)
        {
            $bIsNewVersion = false;
        }
        else 
        {
            if ($aVersionParts[1] < 3)
            {
                $bIsNewVersion = false;
            }
            else 
            {
                if ($aVersionParts[2] < 2)
                {
                    $bIsNewVersion = false;
                }
            }
        }        
        
        if ($bIsNewVersion)
        {
            if ($this->isReadonly()) {
                $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
            }
        }
// FINISH AITOC OPTIONS TEMPLATE             

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
}