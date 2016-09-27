<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/


class Cybernetikz_Cnslider_Block_Adminhtml_Cnslider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize edit form container
     *
     */
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'cybernetikz_cnslider';
        $this->_controller = 'adminhtml_cnslider';

        parent::__construct();

        if (Mage::helper('cybernetikz_cnslider/admin')->isActionAllowed('save')) {
            $this->_updateButton('save', 'label', Mage::helper('cybernetikz_cnslider')->__('Save Banner'));
            $this->_addButton('saveandcontinue', array(
                'label'   => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }

        if (Mage::helper('cybernetikz_cnslider/admin')->isActionAllowed('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('cybernetikz_cnslider')->__('Delete Banner'));
        } else {
            $this->_removeButton('delete');
        }
		
		$id=$this->getRequest()->getParam('id');
		if($id!=""){
			$prview_url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)."slider/index/bannerpreview/id/".$id;
			$this->_addButton('preview', array(
				'label'   => Mage::helper('adminhtml')->__('Preview Banner'),
				'onclick' => 'bannerPreview(\''.$prview_url.'\')',
				'class'   => 'scalable',
			), 0);
		}
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
			
			function bannerPreview(prview_url){
				var con=confirm('All saved data will show in banner preview.');
				if(con==true){
					window.open(prview_url);
				}
			}
        ";
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        $model = Mage::helper('cybernetikz_cnslider')->getSliderItemInstance();
		///print_r($model);
        if ($model->getId()) {
            return Mage::helper('cybernetikz_cnslider')->__("Edit Banner '%s'",
                 $this->escapeHtml($model->getName()));
        } else {
            return Mage::helper('cybernetikz_cnslider')->__('New Banner');
        }
    }
}