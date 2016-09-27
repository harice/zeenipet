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

class Aitoc_Aitoptionstemplate_Block_Backup_Main  extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitoptionstemplate/main.phtml');
    }

    protected function _prepareLayout()
    {
    	parent::_prepareLayout();                           
        $this->setChild('synchro_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Check Synchronization'),
                    'onclick'   => "
                                new Ajax.Request('".$this->getUrl('*/*/check')."', {
                                  onSuccess: function(response) {
                                  }
                                })",
                    'class'     => 'scalable'
                ))
        );
        $this->setChild('backup_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Backup data to file'),
                    'onclick'   => "doRequest();",
                    'class'     => 'scalable'
                ))
        );
        $this->setChild('restore_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Restore data from reserve tables'),
                    'onclick'   => "
                                new Ajax.Request('".$this->getUrl('*/*/restore')."', {
                                  onSuccess: function(response) {
                                      setLocation('".$this->getUrl('*/*/index')."');
                                  }
                                })",
                    'class'     => 'scalable'
                ))
        );
        $this->setChild('export_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Export'),
                    'onclick'   => "doRequest();",
                    'class'     => 'scalable'
                ))
        );
        $this->setChild('import_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Restore from backup file'),
                    'onclick'   => "$('isFrame').value='0';
        $('edit_form').submit();",
                    'class'     => 'scalable'
                ))
        );
        
        return $this;
    }
    
    public function getSynchroButtonHtml()
    {
        return $this->getChildHtml('synchro_button');
    }
    
    public function getExportButtonHtml()
    {
        return $this->getChildHtml('export_button');
    }
    
    public function getRestoreButtonHtml()
    {
        return $this->getChildHtml('restore_button');
    }
    
    public function getImportButtonHtml()
    {
        return $this->getChildHtml('import_button');
    }
    
    public function getBackupButtonHtml()
    {
        return $this->getChildHtml('backup_button');
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }
    
    public function getUploadUrl()
    {
        return $this->getUrl('*/*/upload');
    }
    
    public function getCheckUrl()
    {
        return $this->getUrl('*/*/check');
    }

    public function getRestoreUrl()
    {
        return $this->getUrl('*/*/restore');
    }
    
    public function getCheckFileUrl()
    {
        return $this->getUrl('*/*/file');
    }
    
    public function getIndexUrl()
    {
        return $this->getUrl('*/*/index');
    }
    
}

?>