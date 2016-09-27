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
class Aitoc_Aitoptionstemplate_Model_Mysql4_Reserve_Catalog_Product_Option_Dependable extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Use is object new method for save of object
     *
     * @var boolean
     */
    protected $_useIsObjectNew = true;
    
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/reserve_product_option_dependable','reserve_id');
    }
    
    public function reserveTemplateDependableOptions( $template_id )
    {
        $collection = Mage::getModel('aitoptionstemplate/reserve_catalog_product_option_dependable')
                ->getCollection()->loadByTemplateId( $template_id );
        foreach($collection as $oldModel) {
            $oldModel->delete();
        }

        $collection = Mage::getModel('aitoptionstemplate/product_option_dependable')->getCollection()->loadByTemplateId( $template_id );
        $model = Mage::getModel('aitoptionstemplate/reserve_catalog_product_option_dependable');
        foreach($collection as $option)
        {
            $data = $option->getData();
            $child = $data[Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable::CHILDREN_ALIAS];
            $data[Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable::CHILDREN_ALIAS] = '';
            $model->reset()
                ->setData( $data )
                ->setTeplateId( $template_id )
                ->setNewChildren($child);
            $model->save();
        }
    }
}