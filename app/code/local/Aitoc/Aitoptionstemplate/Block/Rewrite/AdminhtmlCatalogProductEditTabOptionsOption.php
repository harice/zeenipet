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

class Aitoc_Aitoptionstemplate_Block_Rewrite_AdminhtmlCatalogProductEditTabOptionsOption extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitcommonfiles/design--adminhtml--default--default--template--catalog--product--edit--options--option.phtml');
    }
    
    public function getDeleteTplButtonHtml()
    {
        return $this->getChildHtml('delete_tpl_button');
    }
    
    protected function _prepareLayout()
    {
        $oResult = parent::_prepareLayout();
        
        $this->setChild('delete_tpl_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Delete Template'),
                    'class' => 'delete delete-product-option',
                ))
        );

        return $oResult;
    }
    
    public function getOptionValues()
    {
        $option_product_id=$this->getRequest()->getParam('optproduct_id');
        if (!Mage::helper('aitoptionstemplate')->getRequestAitflag())
        {
            return parent::getOptionValues();
        }
        
        $optionInstance = Mage::getSingleton('catalog/product_option');
        
        
        
        $option2tpl = Mage::getResourceModel('aitoptionstemplate/aitoption2tpl');
        $aOptionIds = $option2tpl->getTemplateOptions(Mage::app()->getFrontController()->getRequest()->get('template_id'));
        if(($option_product_id!=null)&&(isset($option_product_id)))
        {
            $product=Mage::getModel('catalog/product')->load($option_product_id);
            $collection=$product->getProductOptionsCollection();  
            $session=Mage::getSingleton('adminhtml/session');
            $session->setNewOptions($option_product_id);
        }
        else
        {
        $collection = $optionInstance->getCollection()
            ->addFieldToFilter('product_id', Mage::getStoreConfig('general/aitoptionstemplate/default_product_id'))
            ->addFieldToFilter('main_table.option_id', array('in' => $aOptionIds))
            ->addTitleToResult($this->getProduct()->getStoreId())
            ->addPriceToResult($this->getProduct()->getStoreId())
            ->setOrder('sort_order', 'asc')
            ->setOrder('title', 'asc')
            ->addValuesToResult($this->getProduct()->getStoreId());
        $collection->load();
        }
             
        $aOptionHash = array();            

        foreach ($collection as $option)
        {
            $aOptionHash[$option->getId()] = $option;
        }


        $collection->reset();
        
        $aOptionsTmp = $this->getProduct()->getOptions();
        $this->getProduct()->setOptions($aOptionHash);
        $values = parent::getOptionValues();
        $this->getProduct()->setOptions($aOptionsTmp);
        return $values;
    }
    
    
    public function getTemplatesOptionValues($aTemplateIds = array())
    {
        if (!$aTemplateIds) return false;
        
        $optionInstance = Mage::getSingleton('catalog/product_option');
        
        $option2tpl = Mage::getResourceModel('aitoptionstemplate/aitoption2tpl');
        $aOptionTemplateHash = $option2tpl->getOptionTemplateHash($aTemplateIds);
        
        $optionInstance = Mage::getSingleton('catalog/product_option');

        $collection = $optionInstance->getCollection()
            ->addFieldToFilter('product_id', Mage::getStoreConfig('general/aitoptionstemplate/default_product_id'))
            ->addFieldToFilter('main_table.option_id', array('in' => array_keys($aOptionTemplateHash)))
            ->addTitleToResult($this->getProduct()->getStoreId())
            ->addPriceToResult($this->getProduct()->getStoreId())
            ->setOrder('sort_order', 'desc')
            ->setOrder('title', 'asc')
            ->addValuesToResult($this->getProduct()->getStoreId());
        
        $aOptionHash = array();            
        
        foreach ($collection->load() as $option)
        {
            $aOptionHash[$option->getId()] = $option;
        }
        
        $optionsArr = array_reverse($aOptionHash, true);
        
        $values = array();
        
        if ($optionsArr) {
            $scope = (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);
            foreach ($optionsArr as $option) {
                /* @var $option Mage_Catalog_Model_Product_Option */

                $this->setItemCount($option->getOptionId());

                $value = array();

                $value['id'] = 'aitocoption' . $option->getOptionId(); // to prevent option save as old 
                $value['item_count'] = $this->getItemCount();
                $value['option_id'] = 'aitocoption' . $option->getOptionId(); // to prevent option save as old
                $value['title'] = $this->htmlEscape($option->getTitle());
                $value['type'] = $option->getType();
                $value['is_require'] = $option->getIsRequire();
                $value['sort_order'] = $option->getSortOrder();
                $value['template_id'] = $aOptionTemplateHash[$option->getOptionId()];

                if (1 == 2 AND $this->getProduct()->getStoreId() != '0') {
                    $value['checkboxScopeTitle'] = $this->getCheckboxScopeHtml('aitocoption' . $option->getOptionId(), 'title', is_null($option->getStoreTitle()));
                    $value['scopeTitleDisabled'] = is_null($option->getStoreTitle())?'disabled':null;
                }

                if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {

//                    $valuesArr = array_reverse($option->getValues(), true);

                    $i = 0;
                    $itemCount = 0;
                    foreach ($option->getValues() as $_value) {
                        
                        /* @var $_value Mage_Catalog_Model_Product_Option_Value */
                        $value['optionValues'][$i] = array(
                            'item_count' => max($itemCount, $_value->getOptionTypeId()),
                            'option_id' => 'aitocoption' . $_value->getOptionId(),
//                            'option_type_id' => -1,
                            'option_type_id' => $_value->getOptionTypeId(),
                            'title' => $this->htmlEscape($_value->getTitle()),
                            'price' => $this->getPriceValue($_value->getPrice(), $_value->getPriceType()),
                            'price_type' => $_value->getPriceType(),
                            'sku' => $this->htmlEscape($_value->getSku()),
                            'sort_order' => $_value->getSortOrder(),
                        );

                        if (1 == 2 AND $this->getProduct()->getStoreId() != '0') {
                            $value['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml('aitocoption' . $_value->getOptionId(), 'title', is_null($_value->getStoreTitle()), $_value->getOptionTypeId());
                            $value['optionValues'][$i]['scopeTitleDisabled'] = is_null($_value->getStoreTitle())?'disabled':null;
                            if ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                                $value['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml('aitocoption' . $_value->getOptionId(), 'price', is_null($_value->getstorePrice()), $_value->getOptionTypeId());
                                $value['optionValues'][$i]['scopePriceDisabled'] = is_null($_value->getStorePrice())?'disabled':null;
                            }
                        }
                        $i++;
                    }
                } else {
                    $value['price'] = $this->getPriceValue($option->getPrice(), $option->getPriceType());
                    $value['price_type'] = $option->getPriceType();
                    $value['sku'] = $this->htmlEscape($option->getSku());
                    $value['max_characters'] = $option->getMaxCharacters();
                    $value['file_extension'] = $option->getFileExtension();
                    $value['image_size_x'] = $option->getImageSizeX();
                    $value['image_size_y'] = $option->getImageSizeY();
                    if (1 == 2 AND $this->getProduct()->getStoreId() != '0' && $scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                        $value['checkboxScopePrice'] = $this->getCheckboxScopeHtml('aitocoption' . $option->getOptionId(), 'price', is_null($option->getStorePrice()));
                        $value['scopePriceDisabled'] = is_null($option->getStorePrice())?'disabled':null;
                    }
                }
                
                $values[] = new Varien_Object($value);
            }
        }
        
        return $values;
    }    
    
    public function getTemplateHash()
    {
        return Mage::helper('aitoptionstemplate')->getTemplateHash();
    }
    
    public function getProductTemplateList()
    {
        $product2tpl = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
        $aTemplateList = $product2tpl->getProductTemplates($this->getProduct()->getId());
        
        return $aTemplateList;
    }
    
    public function checkTemplateAllowed()
    {
        if ( Mage::helper('aitoptionstemplate')->getRequestAitflag() )
        {
            return false;
        }
        else 
        {
            return true;
        }
    }
    
}