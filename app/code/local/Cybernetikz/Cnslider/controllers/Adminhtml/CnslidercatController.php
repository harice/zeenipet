<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Adminhtml_CnslidercatController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Cybernetikz_Cnslider_Adminhtml_SliderController
     */
    protected function _initAction()
    {
        Mage::helper('cybernetikz_cnslider')->getCnlv();
		
		// load layout, set active menu and breadcrumbs
		$this->loadLayout()
            ->_setActiveMenu('cnslider/manage')
            ->_addBreadcrumb(
                  Mage::helper('cybernetikz_cnslider')->__('Slider'),
                  Mage::helper('cybernetikz_cnslider')->__('Slider')
              )
            ->_addBreadcrumb(
                  Mage::helper('cybernetikz_cnslider')->__('Manage Sliders'),
                  Mage::helper('cybernetikz_cnslider')->__('Manage Sliders')
              )
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('Slider'))
             ->_title($this->__('Manage Sliders'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new Slider item
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit Slider item
     */
    public function editAction()
    {
        $this->_title($this->__('Slider'))
             ->_title($this->__('Manage Sliders'));

        // 1. instance slider model
        /* @var $model Cybernetikz_Cnslider_Model_Item */
        $model = Mage::getModel('cybernetikz_cnslider/cat');
		//print_r($model);
        // 2. if exists id, check it and load data
        $CatId = $this->getRequest()->getParam('id');
        if ($CatId) {
            $model->load($CatId);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('cybernetikz_cnslider')->__('Category item does not exist.')
                );
                return $this->_redirect('*/*/');
            }
            // prepare title
            $this->_title($model->getName());
            $breadCrumb = Mage::helper('cybernetikz_cnslider')->__('Edit Slider');
        } else {
            $this->_title(Mage::helper('cybernetikz_cnslider')->__('New Slider'));
            $breadCrumb = Mage::helper('cybernetikz_cnslider')->__('New Slider');
        }

        // Init breadcrumbs
        $this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
		
		//print_r($model);
		//exit;
        // 4. Register model to use later in blocks
        Mage::register('slider_item', $model);

        // 5. render layout
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectPath   = '*/*';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            $data = $this->_filterPostData($data);
            // init model and set data
            /* @var $model Cybernetikz_Cnslider_Model_Item */
            $model = Mage::getModel('cybernetikz_cnslider/cat');

            // if slider item exists, try to load it
            $CatId = $this->getRequest()->getParam('cat_id');
            if ($CatId) {
                $model->load($CatId);
            }
			
            $model->addData($data);

            try {
                $hasError = false;
				
                // save the data
                $model->save();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('cybernetikz_cnslider')->__('The slider has been saved.')
                );

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $redirectPath   = '*/*/edit';
                    $redirectParams = array('id' => $model->getId());
                }
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    Mage::helper('cybernetikz_cnslider')->__('An error occurred while saving the slider.')
                );
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
                $redirectPath   = '*/*/edit';
                $redirectParams = array('id' => $this->getRequest()->getParam('id'));
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        $itemId = $this->getRequest()->getParam('id');
        if ($itemId) {
            try {
                // init model and delete
                /** @var $model Cybernetikz_Cnslider_Model_Item */
                $model = Mage::getModel('cybernetikz_cnslider/cat');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('cybernetikz_cnslider')->__('Unable to find a slider item.'));
                }
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('cybernetikz_cnslider')->__('The slider item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('cybernetikz_cnslider')->__('An error occurred while deleting the slider item.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/');
    }

	public function massDeleteAction() {
        $slidercatIds = $this->getRequest()->getParam('slidercat');
        if(!is_array($slidercatIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select sliders'));
        } else {
            try {
                foreach ($slidercatIds as $slidercatId) {
                    $slidercat = Mage::getModel('cybernetikz_cnslider/cat')->load($slidercatId);
                    $slidercat->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d slider(s) were successfully deleted', count($slidercatIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
	
    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
		switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('cnslider/manage/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('cnslider/manage/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('cnslider/manage');
                break;
        }
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('time_published'));
        return $data;
    }

    /**
     * Grid ajax action
     */
    public function gridAction()
    {
		$this->loadLayout();
        $this->renderLayout();
    }
}