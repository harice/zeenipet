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
 * @copyright  Copyright (c) 2010 AITOC, Inc. 
 */


class Aitoc_Aitoptionstemplate_Model_Mysql4_Rewrite_Eav_Mysql4_Product_Option extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option
{
    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
    
        if(version_compare(Mage::getVersion(), '1.6.0.0', '>='))
            return parent::_afterSave($object);
        
        $priceTable = $this->getTable('catalog/product_option_price');
        $titleTable = $this->getTable('catalog/product_option_title');

        //better to check param 'price' and 'price_type' for saving. If there is not price skip saving price
        if ($object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_AREA
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FILE
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE_TIME
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_TIME
        ) {

            //save for store_id = 0
            if (!$object->getData('scope', 'price')) {
                $statement = $this->_getReadAdapter()->select()
                    ->from($priceTable)
                    ->where('option_id = '.$object->getId().' AND store_id = ?', 0);
                if ($this->_getReadAdapter()->fetchOne($statement)) {
                    if ($object->getStoreId() == '0') {
                        $this->_getWriteAdapter()->update(
                            $priceTable,
                            array(
                                'price' => $object->getPrice(),
                                'price_type' => $object->getPriceType()
                            ),
                            $this->_getWriteAdapter()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', 0)
                        );
                    }
                } else {
                    $this->_getWriteAdapter()->insert(
                        $priceTable,
                        array(
                            'option_id' => $object->getId(),
                            'store_id' => 0,
                            'price' => $object->getPrice(),
                            'price_type' => $object->getPriceType()
                        )
                    );
                }
            }
            $scope = (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);

            if ($object->getStoreId() != '0' && $scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE
                && !$object->getData('scope', 'price')) {

                $baseCurrency = Mage::app()->getBaseCurrencyCode();
                
                if ($product = $object->getProduct())
                {
                    $storeIds = $object->getProduct()->getStoreIds();
                }
                else
                {
                    $storeIds = array(Mage::app()->getRequest()->getParam('store'));
                }
                if (is_array($storeIds)) {
                    foreach ($storeIds as $storeId) {
                        if ($object->getPriceType() == 'fixed') {
                            $storeCurrency = Mage::app()->getStore($storeId)->getBaseCurrencyCode();
                            $rate = Mage::getModel('directory/currency')->load($baseCurrency)->getRate($storeCurrency);
                            if (!$rate) {
                                $rate=1;
                            }
                            $newPrice = $object->getPrice() * $rate;
                        } else {
                            $newPrice = $object->getPrice();
                        }
                        $statement = $this->_getReadAdapter()->select()
                            ->from($priceTable)
                            ->where('option_id = '.$object->getId().' AND store_id = ?', $storeId);

                        if ($this->_getReadAdapter()->fetchOne($statement)) {
                            $this->_getWriteAdapter()->update(
                                $priceTable,
                                array(
                                    'price' => $newPrice,
                                    'price_type' => $object->getPriceType()
                                ),
                                $this->_getWriteAdapter()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $storeId)
                            );
                        } else {
                            $this->_getWriteAdapter()->insert(
                                $priceTable,
                                array(
                                    'option_id' => $object->getId(),
                                    'store_id' => $storeId,
                                    'price' => $newPrice,
                                    'price_type' => $object->getPriceType()
                                )
                            );
                        }
                    }// end foreach()
                }
            } elseif ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE && $object->getData('scope', 'price')) {
                $this->_getWriteAdapter()->delete(
                    $priceTable,
                    $this->_getWriteAdapter()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
                );
            }
        }

        //title
        if (!$object->getData('scope', 'title')) {
            $statement = $this->_getReadAdapter()->select()
                ->from($titleTable)
                ->where('option_id = '.$object->getId().' and store_id = ?', 0);

            if ($this->_getReadAdapter()->fetchOne($statement)) {
                if ($object->getStoreId() == '0') {
                    $this->_getWriteAdapter()->update(
                        $titleTable,
                            array('title' => $object->getTitle()),
                            $this->_getWriteAdapter()->quoteInto('option_id='.$object->getId().' AND store_id=?', 0)
                    );
                }
            } else {
                $this->_getWriteAdapter()->insert(
                    $titleTable,
                        array(
                            'option_id' => $object->getId(),
                            'store_id' => 0,
                            'title' => $object->getTitle()
                ));
            }
        }

        if ($object->getStoreId() != '0' && !$object->getData('scope', 'title')) {
            $statement = $this->_getReadAdapter()->select()
                ->from($titleTable)
                ->where('option_id = '.$object->getId().' and store_id = ?', $object->getStoreId());

            if ($this->_getReadAdapter()->fetchOne($statement)) {
                $this->_getWriteAdapter()->update(
                    $titleTable,
                        array('title' => $object->getTitle()),
                        $this->_getWriteAdapter()
                            ->quoteInto('option_id='.$object->getId().' AND store_id=?', $object->getStoreId()));
            } else {
                $this->_getWriteAdapter()->insert(
                    $titleTable,
                        array(
                            'option_id' => $object->getId(),
                            'store_id' => $object->getStoreId(),
                            'title' => $object->getTitle()
                ));
            }
        } elseif ($object->getData('scope', 'title')) {
            $this->_getWriteAdapter()->delete(
                $titleTable,
                $this->_getWriteAdapter()->quoteInto('option_id = '.$object->getId().' AND store_id = ?', $object->getStoreId())
            );
        }
        #echo "AFTER SAVE<pre>".print_r($object,1)."</pre>";
        return Mage_Core_Model_Mysql4_Abstract::_afterSave($object);
            
    }
    
    
}