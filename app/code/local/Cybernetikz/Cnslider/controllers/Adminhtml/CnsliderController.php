<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Adminhtml_CnsliderController extends Mage_Adminhtml_Controller_Action
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
                  Mage::helper('cybernetikz_cnslider')->__('Manage Banner'),
                  Mage::helper('cybernetikz_cnslider')->__('Manage Banner')
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
             ->_title($this->__('Manage Banner'));

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
             ->_title($this->__('Manage Banner'));

        // 1. instance slider model
        /* @var $model Cybernetikz_Cnslider_Model_Item */
        $model = Mage::getModel('cybernetikz_cnslider/slider');
		//print_r($model);
        // 2. if exists id, check it and load data
        $sliderId = $this->getRequest()->getParam('id');
        if ($sliderId) {
            $model->load($sliderId);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('cybernetikz_cnslider')->__('Banner item does not exist.')
                );
                return $this->_redirect('*/*/');
            }
            // prepare title
            $this->_title($model->getName());
            $breadCrumb = Mage::helper('cybernetikz_cnslider')->__('Edit Banner');
        } else {
            $this->_title(Mage::helper('cybernetikz_cnslider')->__('New Banner'));
            $breadCrumb = Mage::helper('cybernetikz_cnslider')->__('New Banner');
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
            $model = Mage::getModel('cybernetikz_cnslider/slider');

            // if slider item exists, try to load it
            $sliderId = $this->getRequest()->getParam('slider_id');
            if ($sliderId) {
                $model->load($sliderId);
            }
            // save image data and remove from data array
            if (isset($data['slider_image'])) {
                $imageData = $data['slider_image'];
                unset($data['slider_image']);
            } else {
                $imageData = array();
            }
			
			$data['store_id']=implode(",",$data['store_id']);
			
			$catids="";
			foreach($data['cat_id'] as $catid){
				$catids[]="cns_".$catid."_cns";
			}			
			$data['cat_id']=implode(",",$catids);
			
            $model->addData($data);
			//print_r($model->getData());
			//exit;
            try {
                $hasError = false;
                /* @var $imageHelper Cybernetikz_Cnslider_Helper_Image */
                $imageHelper = Mage::helper('cybernetikz_cnslider/image');
                // remove image

                if (isset($imageData['delete']) && $model->getSliderImage()) {
                    $imageHelper->removeImage($model->getSliderImage());
                    $model->setSliderImage(null);
                }
				
				/*echo $imageData;
				echo $model->getSliderImage();
				exit;*/
				
                // upload new image
                $imageFile = $imageHelper->uploadImage('slider_image');
                if ($imageFile) {
                    if ($model->getSliderImage()) {
                        $imageHelper->removeImage($model->getSliderImage());
                    }
                    $model->setSliderImage($imageFile);
                }
                // save the data
                $model->save();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('cybernetikz_cnslider')->__('The banner item has been saved.')
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
                    Mage::helper('cybernetikz_cnslider')->__('An error occurred while saving the banner item.')
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
                $model = Mage::getModel('cybernetikz_cnslider/slider');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('cybernetikz_cnslider')->__('Unable to find a banner item.'));
                }
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('cybernetikz_cnslider')->__('The banner item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('cybernetikz_cnslider')->__('An error occurred while deleting the banner item.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/');
    }

	public function massDeleteAction() {
        $sliderIds = $this->getRequest()->getParam('sliderids');
        if(!is_array($sliderIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select banners.'));
        } else {
            try {
                foreach ($sliderIds as $sliderId) {
                    $slider = Mage::getModel('cybernetikz_cnslider/slider')->load($sliderId);
                    $slider->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d banner(s) were successfully deleted', count($sliderIds)
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
	
	/**
     * Flush News Posts Images Cache action
     */
    public function flushAction()
    {
        if (Mage::helper('cybernetikz_cnslider/image')->flushImagesCache()) {
            $this->_getSession()->addSuccess('Cache successfully flushed');
        } else {
            $this->_getSession()->addError('There was error during flushing cache');
        }
        $this->_forward('index');
    }
}