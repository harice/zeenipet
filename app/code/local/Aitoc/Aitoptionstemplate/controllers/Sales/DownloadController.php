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
require_once Mage::getModuleDir('controllers', 'Mage_Sales').DS.'DownloadController.php';

class Aitoc_Aitoptionstemplate_Sales_DownloadController extends Mage_Sales_DownloadController
{
    /**
     * Custom options download action
     */
    public function downloadCustomOptionAction()
    {
        $versionInfo = Mage::getVersionInfo();
        if (version_compare(Mage::getVersion(),'1.6.1.0','<')) 
        {
            return parent::downloadCustomOptionAction();
        }
        
        $quoteItemOptionId = $this->getRequest()->getParam('id');
        /** @var $option Mage_Sales_Model_Quote_Item_Option */
        $option = Mage::getModel('sales/quote_item_option')->load($quoteItemOptionId);

        if (!$option->getId()) {
            $this->_forward('noRoute');
            return;
        }

        $optionId = null;
        if (strpos($option->getCode(), Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX) === 0) {
            $optionId = str_replace(Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX, '', $option->getCode());
            if ((int)$optionId != $optionId) {
                $optionId = null;
            }
        }
        $productOption = null;
        if ($optionId) {
            /** @var $productOption Mage_Catalog_Model_Product_Option */
            $productOption = Mage::getModel('catalog/product_option')->load($optionId);
        }
        if (!$productOption || !$productOption->getId()
            || $productOption->getType() != 'file'
        ) {
            $this->_forward('noRoute');
            return;
        }
        
        if ($productOption->getProductId() != $option->getProductId()) 
        {
            if($productOption->getProductId() != Mage::getStoreConfig('general/aitoptionstemplate/default_product_id'))
            {
            $this->_forward('noRoute');
            return;
            }
        }

        try {
            $info = unserialize($option->getValue());
            $this->_downloadFileAction($info);
        } catch (Exception $e) {
            $this->_forward('noRoute');
        }
        exit(0);
    }
}