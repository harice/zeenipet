<?php
/**
 * Product:     Custom Options Templates
 * Package:     Aitoc_Aitoptionstemplate_3.1.7_3.0.0_527385
 * Purchase ID: EFCrLjDgKovYWmpwnpXCoQGr8BljvVD1EcxvlonZ59
 * Generated:   2013-03-12 19:29:22
 * File path:   app/code/local/Aitoc/Aitoptionstemplate/Model/Aitproduct2required.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitoptionstemplate')){ eBEeEZaqZUBBBBjc('9c6ba7040e4a08b93bc6c8760c7b7e50'); ?><?php
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
 } ?>