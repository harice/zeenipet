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
class Aitoc_Aitoptionstemplate_Model_Mysql4_Product_Option_Dependable_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_template_used = 0;

    protected function _construct()
    {
        $this->_init('aitoptionstemplate/product_option_dependable');
    }
    
    protected function _getChildTable()
    {
        return $this->getTable('aitoptionstemplate/product_option_dependable_child');
    }
    
    public function loadByTemplateId( $template_id )
    {
        $this->_template_used = $template_id;
        $select = $this->getSelect();
        $select
            ->joinRight(
                array('option2template' => $this->getTable('aitoptionstemplate/aitoption2tpl')),
                'option2template.option_id = main_table.option_id',
                array('template_id')
            )
            ->where('option2template.template_id =?', $template_id);        
        return $this->_load();
    }

    public function loadByProduct( $product )
    {
        if(is_object($product)) {
            $id = $product->getId();
        } else {
            $id = (int)$product;
        }
        $select = $this->getSelect();
        $select->where('product_id =?', $id);
        
        return $this->_load();
    }
    
    public function loadByProductOptions( $product )
    {
        $options = array();
        foreach($product->getOptions() as $id => $option) {
            $options[] = $id;
        }
        $this->addFieldToFilter('main_table.option_id', array(
            'in' => $options,
        ));    
        return $this->_load();        
    }

    public function joinTemplates()
    {
        $this->getSelect()
            ->joinLeft(
                array('option2tpl'=>$this->getTable('aitoptionstemplate/aitoption2tpl')),
                'main_table.option_id = option2tpl.option_id',
                array('template_id')
            );
        return $this;
    }
    
    protected function _load()
    {
        if(method_exists($this, 'setResetItemsDataChanged')) { //method appeared at 1.4.2.0
            //all items by default will have '_hasDataChanges = false' flag        
            $this->setResetItemsDataChanged(true);
        }
        $this->getSelect()
            ->joinLeft( 
                array('child'=>$this->_getChildTable()), 
                'main_table.row_id = child.row_id', 
                array(Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable::CHILDREN_ALIAS=>'GROUP_CONCAT(child_value_id)') 
            )
            ->group('main_table.row_id');
        return $this->load();
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            if ($this->_template_used || $item->getOptionValueId() < 0) {
                $item->setTemplateFlag(true);
            }
        }
    }
    
    public function getOptionArray()
    {
        $return = array();
        foreach($this as $option) {
            if(!isset($return[$option->getOptionId()])) $return[$option->getOptionId()] = array();
            $return[$option->getOptionId()][$option->getOptionTypeId()] = $option;
        }
        return $return;
    }
}