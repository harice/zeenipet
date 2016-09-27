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
class Aitoc_Aitoptionstemplate_Model_Observer
{
    protected $_allowLoadingDependableCollection = false;
    
    public function onProductCollectionLoadBefore($observer)
    {
        $model = $observer->getCollection();
        
        $model->getSelect()
                ->where('e.entity_id !=?', Mage::getStoreConfig('general/aitoptionstemplate/default_product_id'));

    }
    
    public function onSalesQuoteItemCollectionProductsAfterLoad($observer)
    {
        $model = $observer->getProductCollection();
        
        $helper = Mage::helper('aitoptionstemplate/dependable');
        foreach ($model as $aItem)
        {
            $aItem->setTemplateOptions();
            $helper->checkIfRequiredStrictMode( $aItem );
        }
    }

    public function onSaveProductReserveRelations($observer)
    {
        $product = $observer->getData('data_object');
        $sku = $product->getSku();
        $model = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
        $templateList = $model->getProductTemplates($product->getId());
        $baseCollection = Mage::getModel('aitoptionstemplate/aitproduct2required')->getCollection(); 
        $reserveModel = Mage::getModel('aitoptionstemplate/reserve_catalog_product_required');
        $reserveResource = $reserveModel->getResource();
        $reserveResource->getReadConnection()->truncate($reserveResource->getMainTable());
        foreach($baseCollection as $base)
        {
            $reserveRequired = Mage::getModel('aitoptionstemplate/reserve_catalog_product_required');
            $reserveRequired->setData($base->getData());    
            $reserveRequired->save();          
        }
        if (!empty($templateList))
        {
            foreach($templateList as $template)
            {
                Mage::getResourceModel('aitoptionstemplate/aittemplate')->updateCatalogProduct(intval($template['template_id']));
                $arr=array();
                $modelReserve=Mage::getModel('aitoptionstemplate/reserve_catalog_product_template');
                $collection=$modelReserve->getCollection()
                    ->addFieldToFilter('product_sku', $sku)
                    ->addFieldToFilter('template_id', $template['template_id'])
                    ->load();

                $data=$collection->getData();
                if(empty($data))
                {
                    $arr['product_sku']=$sku;
                    $arr['sort_order'] = $template['sort_order'];
                    $arr['template_id']=$template['template_id'];
                    $modelReserve->addData($arr);
                    $modelReserve->save();
                }
                else
                {                    
                    foreach ($data as $reserveTemplate)
                    {
                        $modelReserve=Mage::getModel('aitoptionstemplate/reserve_catalog_product_template')->load($reserveTemplate['reserve_id']);
                        if ($modelReserve->getId() && ($sku == $modelReserve->getProductSku()) && ($template['template_id'] == $modelReserve->getTemplateId()))
                        {
                            $modelReserve->setData('sort_order', $template['sort_order']);
                            $modelReserve->save();
                        }
                    }
                }
            }             
        }
        else
        {
            $modelResourceReserve=Mage::getResourceModel('aitoptionstemplate/reserve_catalog_product_template');
            $collection=$modelResourceReserve->getProductTemplates($sku);
            if(!empty($collection))
            {
                foreach($collection as $reserve)
                {
                    $modelReserve=Mage::getModel('aitoptionstemplate/reserve_catalog_product_template')->load($reserve['reserve_id']);
                    $modelReserve->delete();
                }
            }
        }
          
    }
    
    public function onDeleteDoReserveTemplate($observer)
    {
        $model=$observer->getData('object');
        $id=$model->getId();
        $reserveTemplate=Mage::getModel('aitoptionstemplate/reserve_catalog_product_optiontemplate')->load($id,'template_id');
        $reserveTemplate->delete();
    }
    
    public function onSaveDoReserveTemplate($observer)
    {
        $object=$observer->getEvent()->getData('object');
        $template=Mage::getModel('aitoptionstemplate/aittemplate')->load($object->getId());  
        $reserveTemplate=Mage::getModel('aitoptionstemplate/reserve_catalog_product_optiontemplate')->load($template->getId(),'template_id');
        $reserveTemplateData=$reserveTemplate->getData();
        if(empty($reserveTemplateData))
        {
            $reserveTemplate->setData($template->getData());
        }
        else
        {
            $reserveTemplate->addData($template->getData());    
        }
        $reserveTemplate->save();

    }
    
    public function onSaveDoReserveOption($observer)
    {
        $observerOption=$observer->getData('data_object');
        
        $option=Mage::getModel('catalog/product_option')->load($observerOption->getId());
        $config = Mage::getConfig()->getStoresConfigByPath('general/aitoptionstemplate/default_product_id');
        
        if($option->getData('product_id')==$config[0])
        {
            $optionId=$option->getId(); 
            $reserveOption=Mage::getModel('aitoptionstemplate/reserve_catalog_product_option')->load($optionId,'option_id');
            $title=Mage::getModel('aitoptionstemplate/product_option_title')->load($optionId,'option_id');
            $price=Mage::getModel('aitoptionstemplate/product_option_price')->load($optionId,'option_id');

            $data=$reserveOption->getData();
            if(empty($data))
            {
                $reserveOption->setData($option->getData());
            }
            else
            {
                $reserveOption->addData($option->getData());
            }
            
            $reserveTitle=Mage::getModel('aitoptionstemplate/reserve_catalog_product_optiontitle')->load($title->getId(),'option_title_id');
            $titleReserveData=$reserveTitle->getData();
            if(empty($titleReserveData))
            {
                $reserveTitle->setData($title->getData());
            }
            else
            {
                $reserveTitle->addData($title->getData());
            }
            
            $reservePrice=Mage::getModel('aitoptionstemplate/reserve_catalog_product_optionprice')->load($price->getId().'option_price_id');
            $priceReserveData=$reservePrice->getData();
            if(empty($priceReserveData))
            {
                $reservePrice->setData($price->getData());
            }
            else
            {
                $reservePrice->addData($price->getData());
            }   
            $priceValue=$reservePrice->getData('price');
            if(!empty($priceValue))
            {
                $reservePrice->save();    
            } 
            $reserveTitle->save();
            $reserveOption->save();            
        }
        
        
    }
    
    public function  onDeleteDoReserveOption($observer)
    {
        $model=$observer->getData('object');
        $id=$model->getData('id');
        $option=Mage::getModel('aitoptionstemplate/reserve_catalog_product_option')->load($id,'option_id');
        $option->delete();
        $optionTitle=Mage::getModel('aitoptionstemplate/reserve_catalog_product_optiontitle')->load($id,'option_id');
        $optionTitle->delete();
        $optionPrice=Mage::getModel('aitoptionstemplate/reserve_catalog_product_optionprice')->load($id,'option_id');
        $optionPrice->delete();       
        $option2Tpl=Mage::getModel('aitoptionstemplate/reserve_catalog_product_option2template')->load($id,'option_id');
        $option2Tpl->delete();    
    }
    
    
    public function onSaveDoReserveValues($observer)
    {
        $object=$observer->getData('object');
        $config = Mage::getConfig()->getStoresConfigByPath('general/aitoptionstemplate/default_product_id');
        if($object->getResourceName()=='catalog/product_option_value')
        {
            $value=Mage::getModel('catalog/product_option_value')->load($object->getId());
            $option=Mage::getModel('catalog/product_option')->load($value->getData('option_id'));
            if($option->getData('product_id')==$config[0])
            {
                $valueId=$value->getId();
                $valueTitle=Mage::getModel('aitoptionstemplate/product_option_value_title')->load($valueId,'option_type_id');
                $valuePrice=Mage::getModel('aitoptionstemplate/product_option_value_price')->load($valueId,'option_type_id');            
                $reserveValue=Mage::getModel('aitoptionstemplate/reserve_catalog_product_option_typevalue')->load($valueId,'option_type_id');
                
                $data=$reserveValue->getData();
                if(empty($data))
                {
                    $reserveValue->setData($value->getData());
                }
                else
                {
                    $reserveValue->addData($value->getData());
                }
                
                $reserveValueTitle=Mage::getModel('aitoptionstemplate/reserve_catalog_product_option_typetitle')->load($valueId,'option_type_id');
                
                $data=$reserveValueTitle->getData();
                if(empty($data))
                {
                    $reserveValueTitle->setData($valueTitle->getData());
                }
                else
                {
                    $reserveValueTitle->addData($valueTitle->getData());
                }
                $reserveValuePrice=Mage::getModel('aitoptionstemplate/reserve_catalog_product_option_typeprice')->load($valueId,'option_type_id');
                $data=$reserveValuePrice->getData();
                if(empty($data))
                {
                    $reserveValuePrice->setData($valuePrice->getData());
                }
                else
                {
                    $reserveValuePrice->addData($valuePrice->getData());
                }   
                $reserveValue->save();
                $reserveValueTitle->save();
                $reserveValuePrice->save();                               
            }   
           
        }
        
    }
    
    public function onDeleteDoReserveValues($observer)
    {
        $object=$observer->getData('object');
        if($object->getResourceName()=='catalog/product_option_value')
        {
            $valueId=$object->getId();         
            $reserveValue=Mage::getModel('aitoptionstemplate/reserve_catalog_product_option_typevalue')->load($valueId,'option_type_id');
            $reserveTitle=Mage::getModel('aitoptionstemplate/reserve_catalog_product_option_typetitle')->load($valueId,'option_type_id');      
            $reservePrice=Mage::getModel('aitoptionstemplate/reserve_catalog_product_option_typeprice')->load($valueId,'option_type_id');   
            $reserveValue->delete();
            $reservePrice->delete();
            $reserveTitle->delete();
        }    
    }
    
    public function onModuleDisable($observer) 
    {
        //Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_flat')
          //  ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX)
            //->reindexAll();    
               
        if ($observer->getData('aitocmodulename') == 'Aitoc_Aitoptionstemplate')
        {
	        $requiredOptionsModel = Mage::getModel('aitoptionstemplate/aitproduct2required');
	        $products = $requiredOptionsModel->getCollection();
	        foreach($products as $value)
	        {
	            $product = Mage::getModel('catalog/product');
	            $product->load($value->getProductId());
	            
	            $option = Mage::getModel('catalog/product_option');
	            $options = $option->getProductOptionCollection($product);
	            $requiredOptionsSelf = false;
	            foreach($options as $optValue)
	            {
	                if($optValue->getIsRequire() == 1)
	                {
	                    $requiredOptionsSelf = true;
	                }
	            }
	            if(!$requiredOptionsSelf)
	            {
	            	$storeIds = $product->getStoreIds();
	                $requiredOptionsModel->updateProductRequiredOption($product->getId(), $storeIds);
	            }
	        }
        }
    }
    
    public function onCatalogProductSaveAfter($observer)
    {
        Mage::helper('aitoptionstemplate/dependable')->applyOptionsByProduct($observer->getProduct());
    }

    public function onBlockAbstractToHtmlAfter($observer)
    {
        $block = $observer->getBlock();
        if($block instanceof Mage_Catalog_Block_Product_View_Options_Abstract) {
            $transport = $observer->getTransport();
            $transport->setHtml( '<div id="aitoption_wrapper_'.$block->getOption()->getId().'">'.$transport->getHtml().'</div>' );
            return;
        }
    }

    public function allowLoadingDependableCollectionOnProductLoad($observer)
    {
        $this->_allowLoadingDependableCollection = true;
    }

    public function isLoadingDependableCollectionAllowed()
    {
        return $this->_allowLoadingDependableCollection;
    }
    
    public function onBlockHtmlBefore($observer)
    {
        $block = $observer->getBlock();
        if($block->getId() == 'product_edit')
        {
			if($observer->getBlock()->getRequest()->getControllerModule() !== 'Aitoc_Aiteasyedit')
            {
				$block->getChild('save_button')->setData('onclick', 'saveWithIndexRebuild()');
            
				if ($block->getChild('save_and_edit_button'))
				{
					$saveAction = $block->getChild('save_and_edit_button')->getData('onclick');
					$block->getChild('save_and_edit_button')->setData('onclick', 'rebuild' . ucfirst($saveAction));
				}
			}
        }
        
    }
    
}