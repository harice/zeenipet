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
  class Aitoc_Aitoptionstemplate_Model_Mysql4_Reserve_Catalog_Product_Option extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/reserve_catalog_product_option','reserve_id');
    }
    
    public function reserveTemplateOptions($templateId = null)
    {
        if(empty($templateId))
        {
            return ;
        }
        $option2tplModel = Mage::getResourceModel('aitoptionstemplate/aitoption2tpl');
        $option2tpl = $option2tplModel->getTemplateOptions($templateId); 
        if(!empty($option2tpl))
        {
            foreach($option2tpl as $option)
            {
                $arr = array();
                $reserveOption2tpl = Mage::getModel('aitoptionstemplate/reserve_catalog_product_option2template')->load($option,'option_id');
                #$reserveOption2tpl->delete();
                $arr['reserve_id'] = $reserveOption2tpl->getId();
                $arr['option_id'] = $option;
                $arr['template_id'] = $templateId;
                //print_r($arr);
                #$reserveOption2tpl=Mage::getModel('aitoptionstemplate/reserve_catalog_product_option2template')->load();
                $reserveOption2tpl->setData($arr);
                $reserveOption2tpl->save();
                //echo "<pre>".print_r($reserveOption2tpl,1),"</pre>";
            }             
        }   
    }
    
}