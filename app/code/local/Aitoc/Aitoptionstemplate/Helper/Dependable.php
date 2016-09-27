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
class Aitoc_Aitoptionstemplate_Helper_Dependable extends Mage_Core_Helper_Abstract
{
    protected $_product = null;
    protected $_product_id = null;
    protected $_template_id = 0;
    protected $_optionInstance = null;
    protected $_requestOptions = null;
    protected $_depandableOptions = null;
    protected $_defaultDependableModel = null;
    /** @var $_defaultDependableModel Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable */
    
    public function setProduct($product)
    {
        $this->_product = $product;
        $this->setProductId( $product->getId() );
    }

    public function getProduct()
    {
        return $this->_product;
    }
    
    public function setProductId($product_id)
    {
        $this->_product_id = $product_id;
    }

    public function getProductId()
    {
        return $this->_product_id;
    }

    /**
     * Retrieve option instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Collection
     */
    public function getProductOptionCollection( $store_id, $option_ids = false )
    {
        if (!$this->_optionInstance) {
            $this->_optionInstance = Mage::getSingleton('catalog/product_option')->getCollection();
            if(is_array($option_ids)) {
                $this->_optionInstance->addFieldToFilter( 'main_table.option_id', array('in'=> $option_ids) );
            } else {
                $this->_optionInstance->addFieldToFilter( 'product_id', $this->getProductId() );
            }
            $this->_optionInstance                
                ->addTitleToResult($store_id)
                //->addPriceToResult($store_id)
                //->setOrder('sort_order', 'asc')
                //->setOrder('title', 'asc')
                ->addValuesToResult($store_id);
        }
        return $this->_optionInstance;
    }

    /**
     *
     * @param array $options
     */
    public function applyOptionsToTemplate( $option_ids, $template_id )
    {
        if(!is_array($option_ids) || sizeof($option_ids) == 0) {
            return false;
        }
        $this->setProductId( Mage::helper('aitoptionstemplate')->getDefaultProductId() );
        $this->_template_id = $template_id;
        $this->getProductOptionCollection( Mage::app()->getStore()->getId(), $option_ids );
        $this->_apply();
    }

    public function applyOptionsByProduct( Mage_Catalog_Model_Product $product )
    {
        if(!$product->getId()) {
            return false;
        }
        $this->setProduct($product);
        $this->getProductOptionCollection( $product->getStoreId() );
        $this->_apply();
    }

    private function _apply()
    {
        if(!$this->_loadRequestOptions()) {
            //POST don't have any options in product, skipping
            return false;
        }
        foreach($this->_optionInstance as $option_id => $option)
        {
            $requestOption = $this->_getRequestOption($option);
            $model = $this->_getDependableModel($option_id, 0);

            $model->setOptionValueId($requestOption['row_id'])
                    ->setProductId($this->getProductId());
            $model->save();

            if(sizeof($option->getValues())>0 && sizeof($requestOption['values']) > 0)
            {
                foreach($option->getValues() as $value_id => $optionValue)
                {
                    $requestOptionValue = $this->_getRequestOptionValue($optionValue, $requestOption['values']);
                    if($requestOptionValue['option_type_id']== -1 && $requestOptionValue['title'] != $optionValue->getTitle()) {
                        //sometimes magento will add new option to JS with ID of previous existence option, but option_type_id will be -1 (new option flag)
                        //magento will save this option as new, so we need to check if data is equal to this option, instead create new one and don't save current!
                        continue;
                    }
                    $model = $this->_getDependableModel($option_id, $value_id);
                    $model->setOptionValueId($requestOptionValue['row_id'])
                            ->setProductId($this->getProductId())
                            ->setNewChildren($requestOptionValue['children']);
                    $model->save(); //childen values are saved on _afterSave of model
                }
            }
        }
        return true;
    }

    /**
     * Return model from dependable options collection if exists, clean othervise
     * @param int $option_id
     * @param int $type_id
     * @return Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable
     */
    protected function _getDependableModel( $option_id, $type_id )
    {
        $this->_loadDependableOptions();
        if(isset($this->_depandableOptions[$option_id], $this->_depandableOptions[$option_id][$type_id]))
        {
            $model = $this->_depandableOptions[$option_id][$type_id]; //using old saved model
        } else
        {
            $model = $this->_defaultDependableModel //using new model
                ->reset()                             //resetting it, because it could be used on other new option
                ->setTemplateFlag($this->_template_id)
                ->setProductId($this->getProductId()) //and setting some default values
                ->setOptionId($option_id)
                ->setOptionTypeId($type_id);
        }
        return $model;
    }

    protected function _loadDependableOptions( )
    {
        if(!is_null($this->_depandableOptions)) {
            return true;
        }
        if(!$this->getProductId()) {
            return false;
        }
        $dependableCollection = Mage::getModel('aitoptionstemplate/product_option_dependable')->getCollection();
        /** @var $collection Aitoc_Aitoptionstemplate_Model_Mysql4_Product_Option_Dependable_Collection */
        if($this->_template_id) {
            $dependableCollection->loadByTemplateId($this->_template_id);
        } else {
            $dependableCollection->loadByProduct($this->getProductId());
        }
        $this->_depandableOptions = $dependableCollection->getOptionArray();

        $this->_defaultDependableModel = Mage::getModel('aitoptionstemplate/product_option_dependable');
    }

    /**
     *  Load and product/options array from POST request and return true if it exists
     * @return boolean
     */
    protected function _loadRequestOptions()
    {
        if(is_array($this->_requestOptions)) {
            return true;
        }
        $request = Mage::app()->getRequest()->getParam('product');
        if(!isset($request['options']) || sizeof($request['options'])==0) {
            return false;
        }
        $this->_requestOptions = $request['options'];
        return true;
    }

    /**
     * Map $_POST request options to product saved options
     * @param Mage_Catalog_Model_Product_Option $option product option model
     * @return array
     */
    protected function _getRequestOption($option)
    {
        $option_id = $option->getOptionId();
        if(!isset($this->_requestOptions[$option_id]) || $this->_requestOptions[$option_id]['option_id']!=$option_id)
        {
            //probably it's new option and it's have some javascript id so we need to find it inside array
            foreach($this->_requestOptions as $requestOption) {
                //we are comparing type and name of optins, most probably they will be unique. But if in any case there is several equal options - we use flag to be able to find second one for second option
                if(isset($requestOption['type']) && $requestOption['type'] == $option->getType() && $requestOption['title'] == $option->getTitle() && !isset($requestOption['used'])) {
                    $option_id = $requestOption['id'];
                    break;
                }
            }
        }
        $this->_requestOptions[$option_id]['used'] = true;
        return $this->_requestOptions[$option_id];
    }

    /**
     * Save as function above, just for options values for 'select' option types
     * @param Mage_Catalog_Model_Product_Option_Value $optionValue
     * @param type $requestValues
     * @return array
     */
    protected function _getRequestOptionValue($optionValue, $requestValues)
    {
    	//investigate and refactor this function
        $option_type_id = $optionValue->getOptionTypeId();
        if(!isset($requestValues[$option_type_id]) || $requestValues[$option_type_id]['option_type_id']!=$option_type_id)
        {
            foreach($requestValues as $id=>$requestValue) {
                if(isset($requestValue['title']) && $requestValue['title'] == $optionValue->getTitle()) {
                    $option_type_id = $id;
                    break;
                }
            }
        }
        return isset($requestValues[$option_type_id]) ? $requestValues[$option_type_id] : array('option_type_id' => -1, 'title' => '');
    }
    
    public function applyDependableIdsToProduct( $product )
    {
        if(!$product->getAitDependableOptionsCollection()) {
            $collection = Mage::getModel('aitoptionstemplate/product_option_dependable')->getCollection();
            /** @var $collection Aitoc_Aitoptionstemplate_Model_Mysql4_Product_Option_Dependable_Collection */
            $_helper = Mage::helper('aitoptionstemplate');
            $collection->loadByProductOptions($product);
            $product->setAitDependableOptionsCollection( $collection );
        }
        return $product;
    }
    
    /**
    * Load all dependable options for products and check, if any options is dependable and required. If found any - set SkipCheckRequiredOption flag for products
    * Problem is because some options can be hinned on frontend and never show up, but if they are reqired - product will not be added to cart. This script will allow add products if some required options are hidden
    * 
    * @param mixed $product
    */
    public function checkIfRequiredStrictMode( $product )
    {
        $this->applyDependableIdsToProduct($product);
        $collection = $product->getAitDependableOptionsCollection();
        $optionArray = array();
        foreach($collection as $optionType)
        {
            $optionArray[ $optionType->getOptionValueId() ] = $optionType->getOptionId();
        }
        foreach($collection as $optionType)
        {
            $dependableArray = $optionType->getDefaultChildren();
            foreach($dependableArray as $optionValueId) {
                $option_id = $optionArray[ $optionValueId ];
                $option = $product->getOptionById($option_id);
                if($option && $option->getIsRequire()) {
                    $product->setSkipCheckRequiredOption( true );
                    break(2);
                }
            }
        }
        return $product;
    }    
}