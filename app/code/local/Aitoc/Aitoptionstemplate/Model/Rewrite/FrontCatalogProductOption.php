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
class Aitoc_Aitoptionstemplate_Model_Rewrite_FrontCatalogProductOption extends Mage_Catalog_Model_Product_Option
{

    protected $_eventPrefix='catalog_product_option';
    
    /**
     * Override parent
     * 
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if(!parent::getProduct()) {
            $product = Mage::getModel('catalog/product')->load( Mage::helper('aitoptionstemplate')->getDefaultProductId() );
            $this->setProduct($product);
        }
        return parent::getProduct();
    }     
    
    public function saveTemplateOptions($options)
    {

        foreach ($options as $option) {
            $this->addOption($option);
        }
        
        $aOptionIds = array();
        
        $iStoreId = Mage::app()->getFrontController()->getRequest()->get('store');
        
        if (!$iStoreId)
        {
            $iStoreId = 0;
        }
        $defaultProductId = $this->getProduct()->getId();
        
        foreach ($this->getOptions() as $option) {
            $this->setData($option)
                ->setData('product_id', $defaultProductId )
                ->setData('store_id', $iStoreId);

            if ($this->getData('option_id') == '0') {
                $this->unsetData('option_id');
            } else {
                $this->setId($this->getData('option_id'));
            }
            $isEdit = (bool)$this->getId()? true:false;

            if ($this->getData('is_delete') == '1') {
                if ($isEdit) {
                    $this->getValueInstance()->deleteValue($this->getId());
                    $this->deletePrices($this->getId());
                    $this->deleteTitles($this->getId());
                    $this->delete();
                }
            } else {
                if ($this->getData('previous_type') != '') {
                    $previousType = $this->getData('previous_type');
                    //if previous option has dfferent group from one is came now need to remove all data of previous group
                    if ($this->getGroupByType($previousType) != $this->getGroupByType($this->getData('type'))) {

                        switch ($this->getGroupByType($previousType)) {
                            case self::OPTION_GROUP_SELECT:
                                $this->unsetData('values');
                                if ($isEdit) {
                                    $this->getValueInstance()->deleteValue($this->getId());
                                }
                                break;
                            case self::OPTION_GROUP_FILE:
                                $this->setData('file_extension', '');
                                $this->setData('image_size_x', '0');
                                $this->setData('image_size_y', '0');
                                break;
                            case self::OPTION_GROUP_TEXT:
                                $this->setData('max_characters', '0');
                                break;
                            case self::OPTION_GROUP_DATE:
                                break;
                        }
                        if ($this->getGroupByType($this->getData('type')) == self::OPTION_GROUP_SELECT) {
                            $this->setData('sku', '');
                            $this->unsetData('price');
                            $this->unsetData('price_type');
                            if ($isEdit) {
                                $this->deletePrices($this->getId());
                            }
                        }
                    }
                }
                $this->save();           
            }
            
            if ($this->getId())
            {
                $aOptionIds[] = $this->getId();
            }
            
        }//eof foreach()
        return $aOptionIds;
    }

    /**
     * Save options.
     *
     * @return Mage_Catalog_Model_Product_Option
     */
    public function saveOptions()
    {
        /////////////////// START AITOC OPTION TEMPLATES
        
        // exclude bundle with dynamic price
        
        if ($this->getProduct()->getData('type_id') == 'bundle' AND $this->getProduct()->getData('price_type') == 0)
        {
            return $this;
        }
        
        $product2tpl = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
        
        $data = Mage::app()->getFrontController()->getRequest()->get('product');      
        /* {#AITOC_COMMENT_END#}
        $bIsAllowAssignTemplates = Aitoc_Aitsys_Abstract_Service::get()->getRuler('Aitoc_Aitoptionstemplate')->checkAssignTemplateAllow(0, $this->getProduct()->getId());
        {#AITOC_COMMENT_START#} */      
        if ($data AND isset($data['options']) AND is_array($data['options']) and !empty($data['options']))
        {
            $product2tpl->clearProductTemplates($this->getProduct()->getId());
            
            if ($this->_options)
            {
                $options = $data['options'];
                
                foreach ($this->_options as $key => $option)
                {
                    if (!$options[$option["id"]]["is_delete"] AND $option["is_delete"])
                    {
                        $this->_options[$key]["is_delete"] = 0;
                    }
                }
                
            }
        }
        
        foreach ($this->getOptions() as $iKey => $aOption) 
        {
            // prepare data for templates
            if (isset($aOption['option_id']) AND strpos($aOption['option_id'], 'aitoctpl') === 0) // template to assign
            {
                if (!$aOption['is_delete'])
                {
                    $product2tpl->addRelationship($this->getProduct()->getId(), $aOption);
                }
                
                unset($this->_options[$iKey]);
            }
            
            // prepare data for options
            if (isset($aOption['option_id']) AND strpos($aOption['option_id'], 'aitocoption') === 0) // option from template
            {
                $this->_options[$iKey]['option_id'] = 0;
                $this->_options[$iKey]['id'] = 0;
                $this->_options[$iKey]['previous_type'] = '';
                
                if (isset($aOption['values']) AND $aOption['values'])
                {
                    foreach ($aOption['values'] as $iValKey => $aValue)
                    {
                        $this->_options[$iKey]['values'][$iValKey]['option_type_id'] = -1;
                    }
                }
            }
        }
        /* {#AITOC_COMMENT_END#}
        if (!$bIsAllowAssignTemplates && $bShowSegmentationError) {
            foreach (Aitoc_Aitsys_Abstract_Service::get()->getRuler('Aitoc_Aitoptionstemplate')->getErrors() as $error) {
                Mage::getSingleton('adminhtml/session')->addWarning($error);
            }
        }
        {#AITOC_COMMENT_START#} */        
        $product2required = Mage::getResourceModel('aitoptionstemplate/aitproduct2required');
        
        $product2required->setProductHasRequiredOptions($this->getProduct()->getId());   
                     
        
        /////////////////// FINISH AITOC OPTION TEMPLATES
        
        foreach ($this->_options as $option) {
            $this->setData($option)
                ->setData('product_id', $this->getProduct()->getId())
                ->setData('store_id', $this->getProduct()->getStoreId());

            if ($this->getData('option_id') == '0') {
                $this->unsetData('option_id');
            } else {
                $this->setId($this->getData('option_id'));
            }
            $isEdit = (bool)$this->getId()? true:false;

            if ($this->getData('is_delete') == '1') {
                if ($isEdit) {
                    $this->getValueInstance()->deleteValue($this->getId());
                    $this->deletePrices($this->getId());
                    $this->deleteTitles($this->getId());
                    $this->delete();
                }
            } else {
                if ($this->getData('previous_type') != '') {
                    $previousType = $this->getData('previous_type');
                    //if previous option has dfferent group from one is came now need to remove all data of previous group
                    if ($this->getGroupByType($previousType) != $this->getGroupByType($this->getData('type'))) {

                        switch ($this->getGroupByType($previousType)) {
                            case self::OPTION_GROUP_SELECT:
                                $this->unsetData('values');
                                if ($isEdit) {
                                    $this->getValueInstance()->deleteValue($this->getId());
                                }
                                break;
                            case self::OPTION_GROUP_FILE:
                                $this->setData('file_extension', '');
                                $this->setData('image_size_x', '0');
                                $this->setData('image_size_y', '0');
                                break;
                            case self::OPTION_GROUP_TEXT:
                                $this->setData('max_characters', '0');
                                break;
                            case self::OPTION_GROUP_DATE:
                                break;
                        }
                        if ($this->getGroupByType($this->getData('type')) == self::OPTION_GROUP_SELECT) {
                            $this->setData('sku', '');
                            $this->unsetData('price');
                            $this->unsetData('price_type');
                            if ($isEdit) {
                                $this->deletePrices($this->getId());
                            }
                        }
                    }
                }
                $this->save();            }
        }//eof foreach()
        return $this;
    }
}