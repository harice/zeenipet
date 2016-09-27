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
class Aitoc_Aitoptionstemplate_Model_Aitoption2tpl extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/aitoption2tpl');
    }

    /**
     * Save template options to hidden product
     * @param type $data
     */
    public function saveOptions($data)
    {
        $options = Mage::getModel('catalog/product_option');
        $this->setHasRequired(0);
                
        $aOptionIds = array();
        if (isset($data['product']) AND is_array($data['product']['options']) and !empty($data['product']['options']))
        {
            foreach ($data['product']['options'] as $aOption)
            {
                if ($aOption['is_require'])
                {
                    $this->setHasRequired(1);
                    break;
                }
            }
                    
            $aOptionIds = $options->saveTemplateOptions($data['product']['options']);
            if (!empty($aOptionIds))
            {
                $this->_getResource()->clearTemplateOptions($this->getTemplateId());

                foreach ($aOptionIds as $iOptionId)
                {
                    $this->_getResource()->addRelationship($this->getTemplateId(), $iOptionId);
                }
                Mage::helper('aitoptionstemplate/dependable')->applyOptionsToTemplate( $aOptionIds, $this->getTemplateId() );
            }
        }
        else
        {
            $this->setHasRequired(
                $this->_getResource()->checkProductHasRequiredTemplateOptions($this->getTemplateId())
            );
        }
    }
}