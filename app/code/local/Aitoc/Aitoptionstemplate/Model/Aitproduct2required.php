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
    
    public function updateProductRequiredOption($productId, $stores)
    {
        if(!empty($productId))
        {
            foreach($stores as $storeId)
            {
                $this->getResource()->updateProductRequireOption($productId,$storeId,0);
            }   
        }        
    }
}
?>