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
class Aitoc_Aitoptionstemplate_Model_Rewrite_FrontCatalogProduct extends Mage_Catalog_Model_Product
{
    protected $_tpluploaded = false;
    
    /**
     * Load product options if they exists
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _afterLoad()
    {        
        $this->setTemplateOptions();
        parent::_afterLoad();
        if(Mage::getSingleton('Aitoc_Aitoptionstemplate_Model_Observer')->isLoadingDependableCollectionAllowed()) {
            Mage::helper('aitoptionstemplate/dependable')->checkIfRequiredStrictMode( $this );
        }
        $this->sortOptionsByOrder();
        return $this;
    }    
    
    protected function _afterSaveCommit()
    {
        Mage::getResourceModel('aitoptionstemplate/aittemplate')->checkProduct($this->getId());
        parent::_afterSaveCommit();
    }
    
    public function setTemplateOptions()
    {
        $oReq = Mage::app()->getFrontController()->getRequest();
        
        if (in_array($oReq->getRouteName(),array('adminhtml','aiteasyedit')) AND $oReq->getControllerName() == 'catalog_product' AND $oReq->getActionName() == 'options') // edit product page
        {
            $bIsEditPage = true;
        }
        else 
        {
            $bIsEditPage = false;
        }
        if (!$this->_tpluploaded AND !$bIsEditPage)
        {
            $aTotalOptionList = array();
            $hasOptions = false; 
            
            $product2tpl = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
            $aProductTemplates = $product2tpl->getProductTemplates($this->getId());
    
            if ($aProductTemplates)
            {
                $option2tpl = Mage::getResourceModel('aitoptionstemplate/aitoption2tpl');
                
                $aTplOptionIds = array();
                $aProductOptionIds = array();
                $aOptionTplHash = array();
                
                foreach ($aProductTemplates as $aItem)
                {
                    $aOptionHash = $option2tpl->getTemplateOptions($aItem['template_id']);
                    
                    if ($aOptionHash)
                    {
                        $aTplOptionIds[$aItem['template_id']] = $aOptionHash;
                        
                        foreach ($aOptionHash as $iOptionId)
                        {
                            $aOptionTplHash[$iOptionId] = $aItem['template_id'];
                        }
                    }
                }
                
                if ($aTplOptionIds)
                {
                    foreach ($aTplOptionIds as $aOptionHash)
                    {
                        if ($aOptionHash)
                        {
                            $aProductOptionIds = array_merge($aProductOptionIds, $aOptionHash);
                        }
                    }
                }
                
                $optionInstance = Mage::getSingleton('catalog/product_option');
    
                $collection = $optionInstance->getCollection()
                    ->addFieldToFilter('product_id', Mage::getStoreConfig('general/aitoptionstemplate/default_product_id'))
                    ->addFieldToFilter('main_table.option_id', array('in' => $aProductOptionIds))
                    ->addTitleToResult($this->getStoreId())
                    ->addPriceToResult($this->getStoreId())
                    ->setOrder('sort_order', 'asc')
                    ->setOrder('title', 'asc')
                    ->addValuesToResult($this->getStoreId());  
                          
                $aTplOptionsList = $collection->load();
                
                $aTplOptionsHash = array();
                
                if ($aTplOptionsList)
                {
                    $aTplOptionIdsList = array();
                    
                    foreach ($aTplOptionsList as $tploption)
                    {
                        $iOptionId = $tploption->getId();
                        
                        $aTplOptionsHash[$iOptionId] = $tploption;
                        
                        $aTplOptionIdsList[$aOptionTplHash[$iOptionId]][$iOptionId] = $iOptionId;
                    }
    
                    foreach (array_reverse($aTplOptionIds, true) as $iTplId => $aItem)
                    {
                        if (isset($aTplOptionIdsList[$iTplId]))
                        {
                            $aOptionHash = $aTplOptionIdsList[$iTplId];
                            
                            foreach ($aOptionHash as $iOptionId)
                            {
                                $aTotalOptionList[$iOptionId] = $aTplOptionsHash[$iOptionId];
                            }
                        }
                    }
                }
            }
            
            if ($aTotalOptionList)
            {
                foreach ($aTotalOptionList as $tploption)
                {
                    $tploption->setProduct($this);
                    $this->_options[$tploption->getId()] = $tploption;   
                    
                    $hasOptions = true;             
                }
            }
            
            if ($hasOptions)
            {
                $this->setHasOptions(true);
            }
            
            $this->_tpluploaded = true;
        }
    }
    
    /**
     * Add option to array of product options
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return Mage_Catalog_Model_Product
     */
    public function addOption(Mage_Catalog_Model_Product_Option $option)
    {
        $this->setTemplateOptions();
        
        $this->_options[$option->getId()] = $option;
        
        return $this;
    }
    
    public function duplicate()
    {
        $newProduct = parent::duplicate();
        
        $product2tpl = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
        $aProductTemplates = $product2tpl->getProductTemplates($this->getId(), false);

        if ($aProductTemplates)
        {
            /* {#AITOC_COMMENT_END#}
            if (!Aitoc_Aitsys_Abstract_Service::get()->getRuler('Aitoc_Aitoptionstemplate')->checkAssignTemplateAllow(0, $newProduct->getId())) {
                foreach (Aitoc_Aitsys_Abstract_Service::get()->getRuler('Aitoc_Aitoptionstemplate')->getErrors() as $error) {
                    Mage::getSingleton('adminhtml/session')->addError($error);
                }
                return $newProduct;
            }
            {#AITOC_COMMENT_START#} */
            foreach ($aProductTemplates as $aTemplate)
            {
                $product2tpl->addRelationship($newProduct->getId(), $aTemplate);
            }
        }
        
        Mage::getModel('aitoptionstemplate/product_option_dependable')->duplicate( $this, $newProduct );
        
        return $newProduct;
        
    }

    public function sortOptionsByOrder()
    {
        $product2tpl = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
        $idS = $product2tpl->getSortOrderIds($this);

        if ($idS && $this->_options)
        {
            $options = $this->_options;
            $this->_options = array();

            foreach ($idS as $optionId)
            {
                if (isset($options[$optionId]))
                {
                    $this->_options[$optionId] = $options[$optionId];
                }
            }

            foreach ($options as $option)
            {
                if (!isset($this->_options[$option->getId()]))
                {
                    $this->_options[$option->getId()] = $option;
                }
            }
        }
    }
    
    public function setOptions($options)
    {
        $this->_options = $options;
    }

}