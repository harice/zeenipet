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
class Aitoc_Aitoptionstemplate_Model_Aittemplate extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix='aitoptionstemplate_model_aittemplate'; 
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/aittemplate');
    }

    public function updateCatalogProduct()
    {
        $templaeId = $this->getTemplateId();
        if(!empty($templaeId))
        {
            $this->getResource()->updateCatalogProduct((intval($templaeId)));
        }
        return $this;    
    }
    
    protected function _afterSave()
    {
        $this->updateCatalogProduct();
        parent::_afterSave();
    }


/*
 * @refactor
 * too long method
 */

    /**
     * Create duplicate
     *
     * @return Mage_Catalog_Model_Product
     */
    public function duplicate()
    {
        $oNewTemplate = Mage::getModel('aitoptionstemplate/aittemplate')->setData($this->getData())        
            ->setIsActive(0)
            ->setId(null);
            
        $oNewTemplate->save();
        
        // duplicate tpl options

        $defaultProductId = Mage::helper('aitoptionstemplate')->getDefaultProductId();
        $oOptionInstance = Mage::getSingleton('catalog/product_option');
        
        $product2tpl = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
        
        $write  = $product2tpl->getWriteAdapter();
        $read   = $product2tpl->getReadAdapter();

        $optionsCond = array();
        $optionsData = array();

        // get tpl otptions
        
    	$option2tpl = Mage::getResourceModel('aitoptionstemplate/aitoption2tpl');
    	$aOptionHash = $option2tpl->getTemplateOptions($this->getId());

        //dependable config for current template
        $dependable = Mage::getModel('aitoptionstemplate/product_option_dependable'); //model to save new options
        $dependableItems = $dependable->getCollection()->loadByTemplateId( $this->getId() )->getOptionArray();

        // read and prepare original tpl options
        $select = $read->select()
            ->from($product2tpl->getTable('catalog/product_option'))
            ->where('option_id in (' . implode(',', $aOptionHash) . ')');
        $query = $read->query($select);
        while ($row = $query->fetch()) {
            $optionsData[$row['option_id']] = $row;
            $optionsData[$row['option_id']]['product_id'] = $defaultProductId;
            unset($optionsData[$row['option_id']]['option_id']);
        }

/*
 * @refactor
 * move to separate method
 */
        // insert options to duplicated tpl
        foreach ($optionsData as $oId => $data) {
            $write->insert($product2tpl->getTable('catalog/product_option'), $data);
            
            $newOptionId = $write->lastInsertId();
            
            $optionsCond[$oId] = $newOptionId;
            
            $option2tpl->addRelationship($oNewTemplate->getId(), $newOptionId);

            if(isset($dependableItems[$oId], $dependableItems[$oId][0])) {//compatibility with old templates that may not have any values
                $dependable->reset()
                    ->setTemplateFlag(true)
                    ->setProductId( $defaultProductId ) //for all templates we are using one product
                    ->setOptionId( $newOptionId )
                    ->setOptionTypeId( 0 ) //different type ids will be filled lower
                    ->setOptionValueId( $dependableItems[$oId][0]->getOptionValueId() ) // copying value from previous option
                    ->save();
            }
        }

/*
 * @refactor
 * move to separate method
 */
        // copy options prefs
        foreach ($optionsCond as $oldOptionId => $newOptionId) {
            // title
            $table = $product2tpl->getTable('catalog/product_option_title');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newOptionId . ', `store_id`, `title`'
                . 'FROM `' . $table . '` WHERE `option_id`=' . $oldOptionId;
            $write->query($sql);

            // price
            $table = $product2tpl->getTable('catalog/product_option_price');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newOptionId . ', `store_id`, `price`, `price_type`'
                . 'FROM `' . $table . '` WHERE `option_id`=' . $oldOptionId;
            $write->query($sql);
/************** AITOC SALES PROFIT COMPATIBILITY: START ********************/ 
            $val = Mage::getConfig()->getNode('modules/Aitoc_Aitsalesprofit/active');
            if($val)
            {
                // cost
                $table = $product2tpl->getTable('aitsalesprofit/option_cost');
                $sql = 'REPLACE INTO `' . $table . '` '
                    . 'SELECT NULL, ' . $newOptionId . ', `store_id`, `cost`'
                    . 'FROM `' . $table . '` WHERE `option_id`=' . $oldOptionId;
                $write->query($sql);
            }
            /************** AITOC SALES PROFIT COMPATIBILITY: END ********************/ 
###            $object->getValueInstance()->duplicate($oldOptionId, $newOptionId);
            $a = $oOptionInstance->getValueInstance();
            $oOptionInstance->getValueInstance()->duplicate($oldOptionId, $newOptionId);
        }

        //duplicating dependable options links
        $collection = $oOptionInstance->getValueInstance()->getCollection()
            ->addFieldToFilter('option_id', $optionsCond)
            ->addTitleToResult(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
        $oldCollection = $oOptionInstance->getValueInstance()->getCollection()
            ->addFieldToFilter('option_id', array_keys($optionsCond))
            ->addTitleToResult(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);

/*
 * @refactor
 * move to separate method
 */
        foreach($collection as $newValue)
        {
            $old_option_id = array_search($newValue->getOptionId(), $optionsCond);
            if(!$old_option_id || !isset($dependableItems[$old_option_id])) continue;

            foreach($oldCollection as $oldValue)
            {
                if($oldValue->getTitle() == $newValue->getTitle()) {
                    $old_option_type_id = $oldValue->getOptionTypeId();
                    if(isset($dependableItems[$old_option_id][$old_option_type_id])) {//compatibility with old templates that may not have any values
                        $dependable->reset()
                            ->setTemplateFlag(true)
                            ->setProductId( $defaultProductId ) //for all templates we are using one product
                            ->setOptionId( $newValue->getOptionId() )
                            ->setOptionTypeId( $newValue->getOptionTypeId() )
                            ->setNewChildren( $dependableItems[$old_option_id][$old_option_type_id]->getData(Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable::CHILDREN_ALIAS) )
                            ->setOptionValueId( $dependableItems[$old_option_id][$old_option_type_id]->getOptionValueId() ) // copying value from previous option
                            ->save();
                    }
                    break; //returning back to first cycle
                }
            }
        }

        return $oNewTemplate;
    }
}