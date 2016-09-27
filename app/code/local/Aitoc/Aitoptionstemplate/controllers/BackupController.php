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
class Aitoc_Aitoptionstemplate_BackupController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/aitoc')
            ->_addBreadcrumb(Mage::helper('aitoptionstemplate')->__('Options Template'), Mage::helper('salesrule')->__('Options Template'))
        ;
        return $this;
    }  
    
    public function indexAction()
    {
        $this->_title($this->__('Custom Options Templates'))
             ->_title($this->__('Options Backup and Restore'));
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('aitoptionstemplate/backup_main'));
        $this->renderLayout();
    }
    
    public function checkAction()
    {
        $model = Mage::getModel('aitoptionstemplate/reserve');
        $isSynchronized = $model->checkSynchronization();
        $session = Mage::getSingleton('adminhtml/session'); 
        $session->setSynchronized($isSynchronized);
        #$this->_redirect('*/*/index');
    }
    
    public function backupAction()
    {
        $model=Mage::getModel('aitoptionstemplate/reserve');
        $isSynchronized=$model->reserveTemplates();
        #$this->_redirect('*/*/index');   
        #Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salesrule')->__('Templates was succesfully backuped'));      
    }
    
    public function restoreAction()
    {
        $session = Mage::getModel('adminhtml/session');
        $session->setStep(null);
        $model=Mage::getModel('aitoptionstemplate/reserve');
        $content = $model->restoreTemplates(); 
        if(!empty($content))
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('salesrule')->__('Backup restoring failed'));
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salesrule')->__('Backup was succesfully restored'));     
        }
        
        #$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($content));    
    }
    
    public function exportAction()
    {
        $path = BP.DS.'media'.DS.'downloadable'.DS;
        $session = Mage::getModel('adminhtml/session');
        $export = Mage::getModel('aitoptionstemplate/export')->setFilePath($path.'backup.bak');
        $step = $session->getStep();
        $checkStatus = $session->getCheckStatus();
        if(empty($checkStatus))
        {
            $model = Mage::getModel('aitoptionstemplate/reserve');
            $isSynchronized = $model->checkSynchronization();
            $session = Mage::getSingleton('adminhtml/session'); 
            $session->setCheckStatus($isSynchronized);
        }
        if(empty($step))
        {
            $content = $export->createXml(); 
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($content));           
        }
        elseif($step == 'csv')
        {
            $content = $export->createCsv();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($content));
        }
        else
        {
            $content = $export->createCsv();
            $this->_prepareDownloadResponse('backup'.date('Y:m:d').'.bak', $content);      
        }  
                       
    }
    
    public function importAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('aitoptionstemplate/backup_edit')
            ->setData('action', $this->getUrl('*/*/save'))
            );
        #$this->_addContent($this->getLayout()->createBlock('aitoptionstemplate/backup_form'));
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        $session = Mage::getModel('adminhtml/session');
        $path = BP.DS.'media'.DS.'downloadable'.DS;
        $model = Mage::getModel('aitoptionstemplate/export')->importFile($path.'backup_upload.bak');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($model)); 
        #$this->_redirect('*/*/check');
    }
    
    public function uploadAction()
    {
        $session = Mage::getModel('adminhtml/session');
        $session->setStep(null);
        $path = BP.DS.'media'.DS.'downloadable'.DS;
        $uploader = new Aitoc_Aitoptionstemplate_Model_Uploader('backup');
        $filename = $uploader->getUploadFileName();
        $uploader->save($path,'backup_upload.bak');      
        $session->setUploadFileName($filename);
    }
    
    public function fileAction()
    {
        $session = Mage::getModel('adminhtml/session');

        $arr = array();
        $path = BP.DS.'media'.DS.'downloadable'.DS;
        if((file_exists($path.'backup_upload.bak'))&&(filesize($path.'backup_upload.bak')>0))
        {
            $fileName = $session->getUploadFileName();
            $ext = explode('.',$fileName);
            $file = new Varien_File_Object($path.'backup_upload.bak');
            if(end($ext) != 'bak')
            {
                $arr['msg'] = 'error';
            }
            else
            {
                $arr['msg'] = 'good';
            }
        }
        else
        {
            $arr['msg'] = 'error';
        }
        $arr['ext'] = end($ext);
        $arr['content'] = filesize($path.'backup_upload.bak');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($arr));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/aitoptionstemplate/backup');
    }
}