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
class Aitoc_Aitoptionstemplate_IndexController extends Mage_Adminhtml_Controller_Action
{
    private $_templateId = null;
    private $_template = null;
    private $_hasRequiredOptions = null;
    private $_hasReceivedProductsIds;
    private $_receivedProductsIds = null;
    private $_addedProductsIds = null;
    private $_removedProductsIds = null;
    private $_backupTemplateData = null;

    private function _getTemplateId()
    {
        if (null == $this->_templateId)
        {
            $this->_templateId = $this->getRequest()->getParam('template_id');
        }

        return $this->_templateId;
    }

    private function _getTemplate()
    {
        if (null == $this->_template)
        {
            $this->_template = Mage::getModel('aitoptionstemplate/aittemplate')->load($this->_getTemplateId());
        }

        return $this->_template;
    }

    private function _getPostData()
    {
        return $this->getRequest()->getPost();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/aitoc')
            ->_addBreadcrumb(
                Mage::helper('aitoptionstemplate')->__('Options Template'),
                Mage::helper('aitoptionstemplate')->__('Options Template')
            );
        
        return $this;
    }  
    
    public function indexAction()
    {
        Mage::helper('aitoptionstemplate')->cacheManager();

        $this->_initAction();
        
        $this->_addContent(
            $this->getLayout()->createBlock('aitoptionstemplate/template_grid')
        );

        $this->renderLayout();
    }

    public function editAction()
    {
        if ($this->_getTemplateId() && !$this->_getTemplate()->getId())
        {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('aitoptionstemplate')->__('This template no longer exists')
            );
            
            $this->_redirect('*/*');
            return;
        }

        if ($this->_getBackupTemplateData())
        {
            $this->_restoreTemplateData();
        }

        Mage::register('current_aitoptionstemplate_template', $this->_getTemplate());

        $this->_initAction();

        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

        $breadCrumbText = $this->_getTemplateId() ?
            Mage::helper('salesrule')->__('Edit Template') :
            Mage::helper('salesrule')->__('New Template');

        $this->_addBreadcrumb($breadCrumbText, $breadCrumbText);

        $block = $this->getLayout()
            ->createBlock('aitoptionstemplate/template_edit')
            ->setData('action', $this->getUrl('*/*/save'));

        $this->_addContent($block)
            ->_addLeft($this->getLayout()->createBlock('aitoptionstemplate/template_edit_tabs'))
            ->renderLayout();
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $data = $this->_getPostData();

        if (!$data)
        {
            $this->_redirect('*/*/');
            return;
        }

        if ($this->_getTemplateId() && !$this->_getTemplate()->getId())
        {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('salesrule')->__('The page you are trying to save no longer exists')
            );

            $this->_backupTemplateData($data);
            $this->_redirect('*/*/edit', array('template_id' => $this->_getTemplateId()));
            return;
        }

        $session = Mage::getSingleton('adminhtml/session');
        $newProductOptions = $session->getNewOptions();
        if ($newProductOptions != null)
        {
            $this->_addOptionsToProduct($newProductOptions);
            $session->setNewOptions(null);
        }

        $this->_getTemplate()->setData($data);
        $this->_backupTemplateData();

        $redirect = '*/*/edit';
        /* {#AITOC_COMMENT_END#}
        if (!$this->_checkRuler())
        {
            $this->_redirect($redirect, array('template_id' => $this->_getTemplateId()));
            return;
        }
        {#AITOC_COMMENT_START#} */

        try
        {
            $this->_getTemplate()->addData(array('required_options' => $this->_hasRequiredOptions()));
            $this->_getTemplate()->save();

            $this->_saveOptions();
            $this->_updateAssignedProducts();
            $this->_updateAssignedProductsRequiredOptions();
            $this->_updateReserveData();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salesrule')->__('Template was successfully saved'));
            Mage::getSingleton('adminhtml/session')->setPageData(false);

            if ($this->getRequest()->getParam('back', false))
            {
                if ($this->_getTemplateId())
                {
                    $this->_redirect('*/*/edit', array('template_id' => $this->_getTemplateId()));
                }
                else
                {
                    $this->_redirect('*/*/edit', array('template_id' => $this->_getTemplate()->getId()));
                }
            }
            else
            {
                $this->_redirect('*/*/');
            }
            Mage::app()->getCacheInstance()->invalidateType( array('block_html') );
            return;
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('template_id' => $this->_getTemplateId()));
            return;
        }
    }

    private function _addOptionsToProduct($newProductOptions)
    {
        $product = Mage::getModel('catalog/product')->load($newProductOptions);
        $product->setStoreId(Mage::app()->getStore()->getId());
        $collection = $product->getProductOptionsCollection();
        $dependableArray = Mage::getModel('aitoptionstemplate/product_option_dependable')
            ->getCollection()
            ->loadByProduct($newProductOptions)
            ->getOptionArray();

        foreach ($collection as $option)
        {
            $values = array();
            $newOldTypeIdMap = array();
            $old_option_id = $option->getOptionId();
            $option->setId(null);

            foreach ($option->getValues() as $value)
            {
                $old_type_id = $value->getOptionTypeId();
                $value->setId(null);
                $value->save();

                $values[$value->getId()] = $value->getData();
                $newOldTypeIdMap[$old_type_id] = $value->getId();
            }
            $option->setData('values', $values);
            $option->getValueInstance()->setValues($values);
            $option->save();

            foreach ($dependableArray[$old_option_id] as $type_id => $dependable)
            {
                $dependable->setOptionId($option->getId())->setRowId(null);

                if ($type_id != 0 && isset($newOldTypeIdMap[$type_id]))
                {
                    $dependable->setOptionTypeId($newOldTypeIdMap[$type_id])
                        //forcing to resave dependables
                        ->setNewChildren($dependable->getData(Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable::CHILDREN_ALIAS))
                        ->setData(Aitoc_Aitoptionstemplate_Model_Product_Option_Dependable::CHILDREN_ALIAS, '');
                }

                $dependable->save();
            }
        }
    }

    private function _getProduct2TplModel()
    {
        return Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
    }

    private function _saveOptions()
    {
        $options = Mage::getModel('aitoptionstemplate/aitoption2tpl');
        $options->setTemplateId($this->_getTemplate()->getId());
        $options->saveOptions($this->_getPostData());
    }

    private function _getAssignedProductsIds()
    {
        return (array)$this->_getProduct2TplModel()->getTemplateProducts($this->_getTemplateId());
    }

    private function _hasReceivedProductsIds()
    {
        if (is_null($this->_hasReceivedProductsIds)) {
            $data = $this->_getPostData();
            $this->_hasReceivedProductsIds = isset($data['links']['related']);
        }
        return $this->_hasReceivedProductsIds;
    }
    
    private function _getReceivedProductsIds()
    {
        if (null == $this->_receivedProductsIds)
        {
            $this->_receivedProductsIds = array();

            $data = $this->_getPostData();
            if (isset($data['links']['related']) && $data['links']['related'])
            {
                $links = explode('&', $data['links']['related']);
                foreach ($links as $link)
                {
                    $this->_receivedProductsIds[] = (int)$link;
                }
            }
        }

        return $this->_receivedProductsIds;
    }

    private function _getAddedProductsIds()
    {
        if (null == $this->_addedProductsIds)
        {
            $this->_addedProductsIds = array_diff(
                $this->_getReceivedProductsIds(),
                $this->_getAssignedProductsIds()
            );
        }

        return $this->_addedProductsIds;
    }

    private function _getRemovedProductsIds()
    {
        if (null == $this->_removedProductsIds)
        {
            $this->_removedProductsIds = array_diff(
                $this->_getAssignedProductsIds(),
                $this->_getReceivedProductsIds()
            );
        }

        return $this->_removedProductsIds;
    }

    private function _updateAssignedProducts()
    {
        if ($this->_hasReceivedProductsIds()) {
            foreach ($this->_getAddedProductsIds() as $productId) {
                $this->_getProduct2TplModel()->addRelationship(
                    $productId,
                    array (
                        'template_id' => $this->_getTemplate()->getId(),
                        'sort_order' => 0
                    )
                );
            }
    
            if ($this->_getRemovedProductsIds()) {
                $this->_getProduct2TplModel()->clearTemplateProducts(
                    $this->_getTemplate()->getId(),
                    $this->_getRemovedProductsIds()
                );
            }
        }
    }

    private function _updateAssignedProductsRequiredOptions()
    {
        Mage::getResourceModel('aitoptionstemplate/aitproduct2required')->setTemplateHasRequiredOptions(
            $this->_getTemplate()->getId(),
            $this->_hasRequiredOptions(),
            $this->_getAssignedProductsIds()
        );
    }

    private function _updateReserveData()
    {
        Mage::getResourceModel('aitoptionstemplate/reserve_catalog_product_optiontemplate')->saveData($this->_getTemplate()->getId());
    }

    private function _checkRuler()
    {
        /* {#AITOC_COMMENT_END#}
        $ruler = Aitoc_Aitsys_Abstract_Service::get()->getRuler('Aitoc_Aitoptionstemplate');
        if (!$ruler->checkAssignTemplateAllow($this->_getTemplate()->getId(), $this->_getReceivedProductsIds())) {
            foreach ($ruler->getErrors() as $error) {
                Mage::getSingleton('adminhtml/session')->addError($error);
            }
            return false;
        }
        {#AITOC_COMMENT_START#} */
        return true;
    }

    private function _backupTemplateData($templateData = null)
    {
        $templateData = $templateData ? $templateData : $this->_getTemplate()->getData();
        Mage::getSingleton('adminhtml/session')->setPageData($templateData);
    }

    private function _restoreTemplateData()
    {
        $this->_getTemplate()->addData($this->_getBackupTemplateData());
    }

    private function _getBackupTemplateData()
    {
        if (null == $this->_backupTemplateData)
        {
            $this->_backupTemplateData = Mage::getSingleton('adminhtml/session')->getPageData(true);
        }

        return $this->_backupTemplateData;
    }

    private function _hasRequiredOptions()
    {
        if (null == $this->_hasRequiredOptions)
        {
            $data = $this->_getPostData();

            if (!$data['is_active'])
            {
                $this->_hasRequiredOptions = false;
            }
            else if (isset($data['product']['options']) && $data['product']['options'])
            {
                $this->_hasRequiredOptions = false;
                
                foreach ($data['product']['options'] as $option)
                {
                    if ($option['is_require'])
                    {
                        $this->_hasRequiredOptions = true;
                        break;
                    }
                }
            }
            else
            {
                $this->_hasRequiredOptions = false;
            }
        }

        return $this->_hasRequiredOptions;
    }

    public function deleteAction()
    {
        if (!$this->_getTemplateId())
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('salesrule')->__('Unable to find a page to delete'));
            $this->_redirect('*/*/');
            return;
        }

        try
        {
            $this->_getTemplate()->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salesrule')->__('Template was successfully deleted'));
            $this->_redirect('*/*/');
            return;
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->_getTemplateId()));
            return;
        }
    }

    /**
     * Get connected with template products grid and serializer block
     */
    public function relatedAction()
    {
        $gridBlock = $this->getLayout()->createBlock('aitoptionstemplate/template_edit_tab_related')
            ->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'related')))
        ;

        $iTemplateId = $this->_getTemplateId();

        $product2tpl = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl');
        $productsArray = $product2tpl->getTemplateProducts($iTemplateId);

        $serializerBlock = $this->_createSerializerBlock('links[related]', $gridBlock, $productsArray);

        $this->_outputBlocks($gridBlock, $serializerBlock);
    }    


    /**
     * Get specified tab grid
     */
    public function gridOnlyAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitoptionstemplate/template_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
                ->toHtml()
        );
    }    
    
    /**
     * Create serializer block for a grid
     *
     * @param string $inputName
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     * @param array $productsArray
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer
     */
    protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray)
    {
        return $this->getLayout()->createBlock('aitoptionstemplate/template_edit_tab_ajax_serializer')
            ->setGridBlock($gridBlock)
            ->setProducts($productsArray)
            ->setInputElementName($inputName)
        ;
    }    
    
    /**
     * Output specified blocks as a text list
     */
    protected function _outputBlocks()
    {
        $blocks = func_get_args();
        $output = $this->getLayout()->createBlock('adminhtml/text_list');
        foreach ($blocks as $block) {
            $output->insert($block, '', true);
        }
        $this->getResponse()->setBody($output->toHtml());
    }    
    
    /**
     * Create tpl duplicate
     */
    public function duplicateAction()
    {
        $iTemplateId    = (int) $this->_getTemplateId();
        $oTemplate      = Mage::getModel('aitoptionstemplate/aittemplate')->load($iTemplateId);
        
        if ($oTemplate->getData())
        {
            try {
                $oNewTemplate = $oTemplate->duplicate();
                $this->_getSession()->addSuccess($this->__('Template duplicated'));
                $this->_redirect('*/*/edit', array('_current'=>true, 'template_id'=>$oNewTemplate->getId()));
            }
            catch (Exception $e){
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current'=>true));
            }
        }
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/aitoptionstemplate/template');
    }
}