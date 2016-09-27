<?php

class Indies_Partialpayment_Adminhtml_PartialpaymentController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('partialpayment/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('partialpayment/partialpayment')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('partialpayment_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('partialpayment/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('partialpayment/adminhtml_partialpayment_edit'))
				->_addLeft($this->getLayout()->createBlock('partialpayment/adminhtml_partialpayment_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('partialpayment')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {			
			$model = Mage::getModel('partialpayment/partialpayment');
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));

			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('partialpayment')->__("Partially paid order's information has been successfully saved."));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('partialpayment')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('partialpayment/partialpayment');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $partialpaymentIds = $this->getRequest()->getParam('partialpayment');
        if(!is_array($partialpaymentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($partialpaymentIds as $partialpaymentId) {
                    $partialpayment = Mage::getModel('partialpayment/partialpayment')->load($partialpaymentId);
                    $partialpayment->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($partialpaymentIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $partialpaymentIds = $this->getRequest()->getParam('partialpayment');
        if(!is_array($partialpaymentIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($partialpaymentIds as $partialpaymentId) {
                    $partialpayment = Mage::getSingleton('partialpayment/partialpayment')
									  ->load($partialpaymentId)
									  ->setPartialPaymentStatus($this->getRequest()->getParam('partial_payment_status'))
									  ->setIsMassupdate(true)
									  ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($partialpaymentIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'partialpayment.csv';
        $content    = $this->getLayout()->createBlock('partialpayment/adminhtml_partialpayment_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'partialpayment.xml';
        $content    = $this->getLayout()->createBlock('partialpayment/adminhtml_partialpayment_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}