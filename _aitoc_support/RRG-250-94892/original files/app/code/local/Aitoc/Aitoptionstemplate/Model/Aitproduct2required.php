<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitoptionstemplate_Model_Aitproduct2required extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/aitproduct2required');
    }
    
    public function updateProductRequiredOption($productId)
    {
        if(!empty($productId))
        {
            $stores = Mage::getModel('catalog/product')->load($productId)->getStoreIds();  
            foreach($stores as $storeId)
            {
                $this->getResource()->updateProductRequireOption($productId,$storeId,0);
            }   
        }        
    }
}
?>