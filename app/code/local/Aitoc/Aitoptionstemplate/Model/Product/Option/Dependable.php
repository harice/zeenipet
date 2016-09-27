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
class Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable extends Mage_Core_Model_Abstract
{
    const CHILDREN_ALIAS = 'child_value_id';
    const NEW_CHILDREN = 'children_row';
    
    protected $_template_used = false;
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/product_option_dependable');
    }
    
    protected function _getChildTable()
    {
        return Mage::getSingleton('core/resource')->getTableName('aitoptionstemplate/product_option_dependable_child');
    }
    
    public function setTemplateFlag($flag)
    {
        $this->_template_used = (bool)$flag;
        return $this;
    }
    
    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->saveChildenValues();
        return $this;
    }

    public function afterLoad()
    {
        parent::afterLoad();
        if($this->getOptionValueId() < 0) {
            $this->setTemplateFlag(true);
        }
        return $this;
    }

    /**
     * It's required to use THIS function insted of updating 'self::CHILDREN_ALIAS', because this value is treated as new and compared to current element to find if it's needed to update this values
     * @param string $data
     * @return Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable
     */
    public function setNewChildren($data)
    {
        $this->setData(self::NEW_CHILDREN, $data);
        return $this;
    }
    
    public function saveChildenValues()
    {
        if($this->getOptionTypeId() == 0) {
            //we store children values only for 'select' options values
            return false;
        }
        if($this->getData(self::NEW_CHILDREN) == $this->getData(self::CHILDREN_ALIAS)) {
            //NEW_CHILDREN contains new elements ( if not - update your code to use setNewChildren() ) and it's compared to CHILDREN_ALIAS to find if it's required to be updated
            //values wasn't changed, new variable is equal to old one
            return false;
        }
        $add = $delete = array();
        $new = $this->getChildren(self::NEW_CHILDREN);
        $old = $this->getChildren(self::CHILDREN_ALIAS);
        //if we save template values - all stored data should be negative, so we use $multiplier to divide them
        $multiplier = 1;//$this->_template_used ? -1 : 1;
        foreach($new as $value) {
            if(!in_array($value, $old)) {
                $add[] = $value * $multiplier;
            }
        }
        foreach($old as $value) {
            if(!in_array($value, $new)) {
                $delete[] = $value * $multiplier;
            }
        }
        if(sizeof($add) == 0 && sizeof($delete) == 0) {
            //nothing to update
            return false;
        }
        
        $resource = Mage::getSingleton('core/resource')->getConnection('core_write'); 
        $table = $this->_getChildTable();
        
        if(sizeof($delete) > 0) {
            $resource->delete($table, array(
                'row_id = ?'=> (int)$this->getRowId(), 
                'child_value_id in ('.implode(',', $delete).')' => ''
            ));        
        }

        if(sizeof($add) > 0) {
            foreach($add as $value) {
                $resource->insert($table, array
                (
                    'row_id'  => (int)$this->getRowId(),
                    'child_value_id'     => $value,
                ));
            }
         }
         return $this;
    }
    
    public function setOptionValueId($value)
    {
        //if($this->_template_used && $value > 0) $value *= -1;
        if($value != $this->getData('option_value_id')) {
            $this->setData('option_value_id', $value);
        }
        return $this;
    }
    
    public function getChildren($id = self::CHILDREN_ALIAS, $useAbsoluteValues = true)
    {
        $array = $this->getData($id);
        if(!$array || $array == '') {
            return array();
        }
        $array = explode(',', $array);
        foreach($array as $key=>$value) {
            if($value != 0) {
                if($useAbsoluteValues) $value = abs((int)$value);
                $array[$key] = (int)$value;
            }
        }
        $return = array_unique($array);
        asort($return);
        return $return;
    }
    
    public function getDefaultChildren($useAbsoluteValues = false)
    {
        return $this->getChildren(self::CHILDREN_ALIAS, $useAbsoluteValues);
    }
    
    public function reset()
    {
        $this->unsetData();
        $this->_origData = null;
        $this->_hasDataChanges = false;
        $this->_template_used = false;
        return $this;
    }     

    /**
     *
     * @param Mage_Catalog_Model_Product $oldProduct
     * @param Mage_Catalog_Model_Product $newProduct
     */
    public function duplicate( $oldProduct, $newProduct )
    {
        $dependable = $this->getCollection()->loadByProduct( $oldProduct->getId() );
        $options = $newProduct->getProductOptionsCollection();
        foreach($dependable as $item) {
            $option = $oldProduct->getOptionById( $item->getOptionId() );
            if(!$option->getNewOption()) {
                foreach($options as $newOption) {
                    if(!$newOption->getIsFound() && $option->getSku() == $newOption->getSku() && $option->getType() == $newOption->getType() && $option->getTitle() == $newOption->getTitle()) {
                        $option->setNewOption( $newOption );
                        $newOption->setIsFound(1);
                        break;
                    }
                }
            }
            if(!$option->getNewOption() ) continue;
            $this->reset()
                ->setOptionValueId( $item->getOptionValueId() )
                ->setNewChildren( $item->getData(self::CHILDREN_ALIAS) )
                ->setOptionId( $option->getNewOption()->getId() )
                ->setOptionTypeId(0)
                ->setProductId( $newProduct->getId() );
            if($item->getOptionTypeId()) {
                $value = $option->getValueById($item->getOptionTypeId());
                foreach($option->getNewOption()->getValues() as $newValue) {
                    if($value->getTitle() == $newValue->getTitle()) {
                        $this->setOptionTypeId( $newValue->getOptionTypeId() );
                        break;
                    }
                }
            }
            $this->save();
        }
        return $this;
    }
    
}