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


class Aitoc_Aitoptionstemplate_Model_Mysql4_Rewrite_Product_Option extends Mage_Catalog_Model_Resource_Product_Option
{
    
    protected function _saveValuePrices(Mage_Core_Model_Abstract $object)
    {
        if(version_compare(Mage::getVersion(), '1.6.0.0', '<'))
            return parent::_saveValuePrices($object);
            
        $priceTable   = $this->getTable('catalog/product_option_price');
        $readAdapter  = $this->_getReadAdapter();
        $writeAdapter = $this->_getWriteAdapter();

        /*
         * Better to check param 'price' and 'price_type' for saving.
         * If there is not price skip saving price
         */

        if ($object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_AREA
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FILE
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE_TIME
            || $object->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_TIME
        ) {
            //save for store_id = 0
            if (!$object->getData('scope', 'price')) {
                $statement = $readAdapter->select()
                    ->from($priceTable, 'option_id')
                    ->where('option_id = ?', $object->getId())
                    ->where('store_id = ?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
                $optionId = $readAdapter->fetchOne($statement);

                if ($optionId) {
                    if ($object->getStoreId() == '0') {
                        $data = $this->_prepareDataForTable(
                            new Varien_Object(
                                array(
                                    'price'      => $object->getPrice(),
                                    'price_type' => $object->getPriceType())
                            ),
                            $priceTable
                        );

                        $writeAdapter->update(
                            $priceTable,
                            $data,
                            array(
                                'option_id = ?' => $object->getId(),
                                'store_id  = ?' => Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                            )
                        );
                    }
                } else {
                    $data = $this->_prepareDataForTable(
                         new Varien_Object(
                            array(
                                'option_id'  => $object->getId(),
                                'store_id'   => Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID,
                                'price'      => $object->getPrice(),
                                'price_type' => $object->getPriceType()
                            )
                        ),
                        $priceTable
                    );
                    $writeAdapter->insert($priceTable, $data);
                }
            }

            $scope = (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);

            if ($object->getStoreId() != '0' && $scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE
                && !$object->getData('scope', 'price')) {

                $baseCurrency = Mage::app()->getBaseCurrencyCode();

                //start Aitoc code
                //$storeIds = Mage::app()->getStore($object->getStoreId())->getWebsite()->getStoreIds();
                if ($product = $object->getProduct())
                {
                    $storeIds = $object->getProduct()->getStoreIds();
                }
                else
                {
                    $storeIds = array(Mage::app()->getRequest()->getParam('store'));
                }
                //end Aitoc code

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

                        $statement = $readAdapter->select()
                            ->from($priceTable)
                            ->where('option_id = ?', $object->getId())
                            ->where('store_id  = ?', $storeId);

                        if ($readAdapter->fetchOne($statement)) {
                            $data = $this->_prepareDataForTable(
                                new Varien_Object(
                                    array(
                                        'price'      => $newPrice,
                                        'price_type' => $object->getPriceType()
                                    )
                                ),
                                $priceTable
                            );

                            $writeAdapter->update(
                                $priceTable,
                                $data,
                                array(
                                    'option_id = ?' => $object->getId(),
                                    'store_id  = ?' => $storeId
                                )
                            );
                        } else {
                            $data = $this->_prepareDataForTable(
                                new Varien_Object(
                                    array(
                                        'option_id'  => $object->getId(),
                                        'store_id'   => $storeId,
                                        'price'      => $newPrice,
                                        'price_type' => $object->getPriceType()
                                    )
                                ),
                                $priceTable
                            );
                            $writeAdapter->insert($priceTable, $data);
                        }
                    }// end foreach()
                }
            } elseif ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE && $object->getData('scope', 'price')) {
                $writeAdapter->delete(
                    $priceTable,
                    array(
                        'option_id = ?' => $object->getId(),
                        'store_id  = ?' => $object->getStoreId()
                    )
                );
            }
        }

        return $this;
    }
    
}