<?php

require_once 'app/code/local/Indies/Partialpayment/controllers/Adminhtml/PartialpaymentController.php';

class Indies_Partialpaymentadmin_Adminhtml_PartialpaymentadminController extends Indies_Partialpayment_Adminhtml_PartialpaymentController
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

			$orderId = $model->getOrderId(); // existing order to duplicate
			$session = Mage::getSingleton('adminhtml/session_quote');
			$session->clear();
			$ordercreatemodel = Mage::getSingleton('adminhtml/sales_order_create');
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
			if (!Mage::helper('sales/reorder')->canReorder($order)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('partialpayment')->__('Item does not exist'));
				$this->_redirect('*/*/');
			}
			if ($order->getId()) {
				$order->setReordered(true);
				$session->setUseOldShippingMethod(true);
				$ordercreatemodel->initFromOrder($order);
			}
			else{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('partialpayment')->__('Partially paid order is not available.'));
			}

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

			$this->_addContent($this->getLayout()->createBlock('partialpaymentadmin/adminhtml_partialpaymentadmin_edit'))
				->_addLeft($this->getLayout()->createBlock('partialpaymentadmin/adminhtml_partialpaymentadmin_edit_tabs'));

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
		$postData = $this->getRequest()->getPost();

		$action = Mage::app()->getRequest()->getParam('action');

		if (isset($postData['partial_payment_id']) && $postData['partial_payment_id']) {
			$partial_payment_id = $postData['partial_payment_id'];
			$partial_payment_data = Mage::getModel('partialpayment/partialpayment')->load($partial_payment_id);
			$is_installment_paid = 0;

			if (isset($postData['installment_id']) && $postData['installment_id']) {
				$partial_payment_installment_id = $postData['installment_id'];
				$partial_payment_installment_data = Mage::getModel('partialpayment/installment')->load($partial_payment_installment_id);

				$installment_due_date = $postData['installment'][$partial_payment_installment_id]['installment_due_date'];
				$installment_status = $postData['installment'][$partial_payment_installment_id]['installment_status'];

				if (($installment_status == 'Remaining' || $installment_status == 'Canceled') && $partial_payment_installment_data->getInstallmentStatus() == 'Paid') {
					$partial_payment_data->setPaidAmount($partial_payment_data->getPaidAmount() - $partial_payment_installment_data->getInstallmentAmount());
					$partial_payment_data->setRemainingAmount($partial_payment_data->getRemainingAmount() + $partial_payment_installment_data->getInstallmentAmount());
					$partial_payment_data->setPaidInstallment($partial_payment_data->getPaidInstallment() - 1);
					$partial_payment_data->setRemainingInstallment($partial_payment_data->getRemainingInstallment() + 1);
					$partial_payment_data->save();

					$partial_payment_installment_data->setInstallmentPaidDate('');
					$partial_payment_installment_data->setPaymentMethod('');
					$partial_payment_installment_data->save();
				}
				elseif ($installment_status == 'Paid' && ($partial_payment_installment_data->getInstallmentStatus() == 'Remaining' || $partial_payment_installment_data->getInstallmentStatus() == 'Canceled')) {
					if ($this->_capturePayment($partial_payment_id, $partial_payment_data->getOrderId(), $partial_payment_installment_data->getInstallmentAmount())) {
						$partial_payment_data->setPaidAmount($partial_payment_data->getPaidAmount() + $partial_payment_installment_data->getInstallmentAmount());
						$partial_payment_data->setRemainingAmount($partial_payment_data->getRemainingAmount() - $partial_payment_installment_data->getInstallmentAmount());
						$partial_payment_data->setPaidInstallment($partial_payment_data->getPaidInstallment() + 1);
						$partial_payment_data->setRemainingInstallment($partial_payment_data->getRemainingInstallment() - 1);
						$partial_payment_data->save();

						$is_installment_paid++;

						$partial_payment_installment_data->setInstallmentPaidDate(date('Y-m-d'));
						$partial_payment_installment_data->setPaymentMethod($postData['payment']['method']);
						$partial_payment_installment_data->save();
					}
				}

				$partial_payment_installment_data->setInstallmentDueDate($installment_due_date);
				$partial_payment_installment_data->setInstallmentStatus($installment_status);
				$partial_payment_installment_data->save();
			}

			$partial_payment_data->setPartialPaymentStatus($postData['partial_payment_status']);
			$partial_payment_data->setUpdatedDate(date('Y-m-d'));
			$partial_payment_data->save();

			if ($partial_payment_data->getPaidInstallment() <= 0) {
				$partial_payment_data->setPaidInstallment(0);
				$partial_payment_data->setRemainingInstallment($partial_payment_data->getTotalInstallment());
				$partial_payment_data->setPartialPaymentStatus('Pending');
				$partial_payment_data->save();
			}
			elseif ($partial_payment_data->getPaidInstallment() >= $partial_payment_data->getTotalInstallment()) {
				$partial_payment_data->setPaidInstallment($partial_payment_data->getTotalInstallment());
				$partial_payment_data->setRemainingInstallment(0);
				$partial_payment_data->setPartialPaymentStatus('Complete');
				$partial_payment_data->save();
			}

			if ($is_installment_paid) {
				if ($partial_payment_data->getPaidInstallment() == 1) {
					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("partialpaymentadmin")->__($partial_payment_data->getPaidInstallment() . "st installment of order # " . $partial_payment_data->getOrderId() . " has been done successfully."));
				}
				elseif ($partial_payment_data->getPaidInstallment() == 2) {
					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("partialpaymentadmin")->__($partial_payment_data->getPaidInstallment() . "nd installment of order # " . $partial_payment_data->getOrderId() . " has been done successfully."));
				}
				elseif ($partial_payment_data->getPaidInstallment() == 3) {
					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("partialpaymentadmin")->__($partial_payment_data->getPaidInstallment() . "rd installment of order # " . $partial_payment_data->getOrderId() . " has been done successfully."));
				}
				else {
					Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("partialpaymentadmin")->__($partial_payment_data->getPaidInstallment() . "th installment of order # " . $partial_payment_data->getOrderId() . " has been done successfully."));
				}
			}
			else {
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("partialpaymentadmin")->__("Partially paid order's information has been edited successfully."));
			}

			if ($action == 'saveandcontinue') {
				$this->_redirect('*/*/edit', array('id' => $partial_payment_id));
			}
			else {
				$this->_redirect('partialpayment/adminhtml_partialpayment/index');
			}
		}
		else {
	        Mage::getSingleton("adminhtml/session")->addError(Mage::helper("partialpaymentadmin")->__("Unable to edit partially paid order's information."));
    	    $this->_redirect('partialpayment/adminhtml_partialpayment/index');
		}
	}


	protected function _capturePayment($partial_payment_id, $order_id, $installment_amount)
	{
		$order = Mage::getModel("sales/order")->loadByIncrementId($order_id);
		$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
		$invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_OPEN);
		$capturePayment = false;
		$paymentInfo = $this->getRequest()->getPost('payment', array());

		try
		{
			if (is_array($paymentInfo) && count($paymentInfo) && isset($paymentInfo['method']))
			{
				$calculationHelper = Mage::helper('partialpayment/calculation');
				$partialpaymentHelper = Mage::helper('partialpayment/partialpayment');

				$invoice->setBaseGrandTotal($calculationHelper->convertCurrencyAmount($installment_amount));
				$invoice->setGrandTotal($installment_amount);
				$invoice->getOrder()->setTotalDue($installment_amount);
				$capturePayment = Mage::getModel('partialpayment/sales_order_capture_payment');
				$capturePayment->setOrder($invoice->getOrder());
				$capturePayment->importData($paymentInfo);	
				$capturePayment->setAmountOrdered($installment_amount);
				$capturePayment->setBaseAmountOrdered($calculationHelper->convertCurrencyAmount($installment_amount));
				$capturePayment->setShippingAmount(0);
				$capturePayment->setBaseShippingAmount(0);
				$capturePayment->setAmountAuthorized($installment_amount);
				$capturePayment->setBaseAmountAuthorized($calculationHelper->convertCurrencyAmount($installment_amount));

				$clonedInvoice = clone $invoice;
				$invoice->getOrder()->addRelatedObject($capturePayment);
				if ($capturePayment->canCapture()) {
					$capturePayment->capture($clonedInvoice);
					$capturePayment->pay($clonedInvoice);
				}
				else {
					$capturePayment->pay($clonedInvoice);
				}
				return true;
			}
		} catch(Exception $e) {
			 Mage::throwException($this->__("Gateway error : {$e -> getMessage()}"));
			 $this->_redirect('*/*/edit', array('id' => $partial_payment_id));
		}
	}


	public function deleteAction() {
		$postData = $this->getRequest()->getPost();

		$partial_payment_id = $this->getRequest()->getParam('id');

		if ($partial_payment_id) {
			$partialpayment_model = Mage::getModel('partialpayment/partialpayment');
			$partialpayment_model->setId($partial_payment_id)->delete();

			$partial_payment_installments = Mage::getModel('partialpayment/installment')->getCollection()
												->addFieldToFilter('partial_payment_id', array('eq' => $partial_payment_id));
			$partial_payment_installments = $partial_payment_installments->getData();

			$partialpayment_installment_model = Mage::getModel('partialpayment/installment');

			for ($i=0;$i<sizeof($partial_payment_installments);$i++) {
				$partialpayment_installment_model->setId($partial_payment_installments[$i]['installment_id'])->delete();
			}

			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("partialpaymentadmin")->__("Partially paid order and installments information has been deleted successfully."));
		}
		else {
			Mage::getSingleton("adminhtml/session")->addError(Mage::helper("partialpaymentadmin")->__("Unable to delete partially paid order and installments information."));
		}

		$this->_redirect('partialpayment/adminhtml_partialpayment/index');
	}


    public function massDeleteAction() {		
        $partialpaymentIds = $this->getRequest()->getParam('partialpayment');

        if(!is_array($partialpaymentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s).'));
        }
		else {
            try {
                foreach ($partialpaymentIds as $partialpaymentId) {
					$partial_payment_installments = Mage::getModel('partialpayment/installment')->getCollection()
														->addFieldToFilter('partial_payment_id', array('eq' => $partialpaymentId));
					$partial_payment_installments = $partial_payment_installments->getData();

					$partialpayment_installment_model = Mage::getModel('partialpayment/installment');

					for ($i=0;$i<sizeof($partial_payment_installments);$i++) {
						$partialpayment_installment_model->setId($partial_payment_installments[$i]['installment_id'])->delete();
					}

                    $partialpayment = Mage::getModel('partialpayment/partialpayment')->load($partialpaymentId);
                    $partialpayment->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted.', count($partialpaymentIds)
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
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s).'));
        }
		else {
            try {
                foreach ($partialpaymentIds as $partialpaymentId) {
                    $partialpayment = Mage::getSingleton('partialpayment/partialpayment')
									  ->load($partialpaymentId)
									  ->setPartialPaymentStatus($this->getRequest()->getParam('partial_payment_status'))
									  ->setIsMassupdate(true)
									  ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated.', count($partialpaymentIds))
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