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

class Aitoc_Aitoptionstemplate_Model_Rewrite_FrontCatalogProductTypeConfigurable extends Mage_Catalog_Model_Product_Type_Configurable
{

    /**
     * Retrieve array of "subproducts"
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getUsedProducts($requiredAttributeIds = null, $product = null)
    {
        Varien_Profiler::start('CONFIGURABLE:'.__METHOD__);
        if (!$this->getProduct($product)->hasData($this->_usedProducts)) {
            if (is_null($requiredAttributeIds)
                and is_null($this->getProduct($product)->getData($this->_configurableAttributes))) {
                // If used products load before attributes, we will load attributes.
                $this->getConfigurableAttributes($product);
                // After attributes loading products loaded too.
                Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
                return $this->getProduct($product)->getData($this->_usedProducts);
            }

            $usedProducts = array();
            $collection = $this->getUsedProductCollection($product)
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions();

            if (is_array($requiredAttributeIds)) {
                foreach ($requiredAttributeIds as $attributeId) {
                    $attribute = $this->getAttributeById($attributeId, $product);
                    if (!is_null($attribute))
                        $collection->addAttributeToFilter($attribute->getAttributeCode(), array('notnull'=>1));
                }
            }

// START AITOC OPTIONS TEMPLATE  

            $product2required = Mage::getResourceModel('aitoptionstemplate/aitproduct2required');
            
            $collection->getSelect()->joinLeft($product2required->getTable('aitoptionstemplate/aitproduct2required')." AS p2t"," p2t.product_id = e.entity_id");
                        
            $collection->getSelect()->where('p2t.required_options IS NULL OR p2t.required_options != 1');

// FINISH AITOC OPTIONS TEMPLATE

            foreach ($collection as $item) {
                $usedProducts[] = $item;
            }
            
            $this->getProduct($product)->setData($this->_usedProducts, $usedProducts);
        }
        Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
        return $this->getProduct($product)->getData($this->_usedProducts);
    }

}