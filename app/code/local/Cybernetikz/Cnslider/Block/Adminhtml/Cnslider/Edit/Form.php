<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Block_Adminhtml_Cnslider_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
		
		$this->getLayout()->getBlock('head')->addJs('jscolor/jscolor.js');
    }
	
	/**
     * Prepare form action
     *
     * @return Cybernetikz_Cnslider_Block_Adminhtml_Cnslider_Edit_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::helper('cybernetikz_cnslider')->getSliderItemInstance();
		
		$form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));
		
		/**
         * Checking if user have permissions to save information
         */
        if (Mage::helper('cybernetikz_cnslider/admin')->isActionAllowed('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        //$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('cnslider_content_');

        $fieldset = $form->addFieldset('content_fieldset', array(
            'legend' => Mage::helper('cybernetikz_cnslider')->__('Slider Content'),
            'class'  => 'fieldset-medium'
        ));
		
		if ($model->getId()) {
            $fieldset->addField('slider_id', 'hidden', array(
                'name' => 'slider_id',
            ));
        }
		
				
		$fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Title'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Title'),
            'maxlength' => '250',
            'required'  => true,
			'disabled'  => $isElementDisabled
        ));
 		
		/**
		 * Active or Deactive
		 */
    	$fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Status'),
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('cybernetikz_cnslider')->__('Enabled'),
                '0' => Mage::helper('cybernetikz_cnslider')->__('Disabled')
            ),
         
        ));
		
		/**
         * Store ID
         */
        if (!Mage::app()->isSingleStoreMode()) {
        	$fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'store_id[]',
                'label'     => Mage::helper('cybernetikz_cnslider')->__('Store View'),
                'title'     => Mage::helper('cybernetikz_cnslider')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }
		
		$categories = array();
	  	$collection = Mage::getModel('cybernetikz_cnslider/cat')->getCollection()->setOrder('sort_order', 'asc');
		foreach ($collection as $cat) {
			$categories[] = ( array(
				'label' => (string)$cat->getTitle(),
				'value' => $cat->getCatId()
				));
		}
		
	  	$fieldset->addField('cat_id', 'multiselect', array(
                'name'      => 'cat_id[]',
                'label'     => Mage::helper('cybernetikz_cnslider')->__('Slider'),
                'title'     => Mage::helper('cybernetikz_cnslider')->__('Slider'),
                'required'  => true,
				'style'		=> 'height:100px',
                'values'    => $categories,
     	));
		
		$this->_addElementTypes($fieldset);
		$fieldset->addField('slider_image', 'image', array(
            'name'      => 'slider_image',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Slider Image'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Slider Image'),
            'required'  => true,
			'disabled'  => $isElementDisabled
        ));
		
		$fieldset->addField('link_url', 'text', array(
			'name'      => 'link_url',
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action URL'),
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action URL'),
			'maxlength' => '250',
			'required'  => true,
			'disabled'  => $isElementDisabled
		));
		
		$fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Order'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Order'),
			'class'		=> 'validate-number',
            'maxlength' => '20',
            'required'  => false,
			'disabled'  => $isElementDisabled
        ));
		
		$fieldset->addField('active_from', 'date', array(
          'label'     => Mage::helper('cybernetikz_cnslider')->__('Active From'),
		  'name'      => 'active_from',
		  'required'  => true,
		  'image' => $this->getSkinUrl('images/grid-cal.gif'),
		  'disabled'  => $isElementDisabled,
	      'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM) 
		));
		
		$fieldset->addField('active_to', 'date', array(
          'label'     => Mage::helper('cybernetikz_cnslider')->__('Active To'),
		  'name'      => 'active_to',
		  'required'  => true,
		  'image' => $this->getSkinUrl('images/grid-cal.gif'),
		  'disabled'  => $isElementDisabled,
	      'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM) 
		));
		
		$fieldset->addField('name_color', 'text', array(
			'name'      => 'name_color',
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Title Text Color'),
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Title Text Color'),
			'class'		=>'color',
			'maxlength' => '250',
			'required'  => true,
			'disabled'  => $isElementDisabled
		));
		
		$fieldset->addField('content_color', 'text', array(
			'name'      => 'content_color',
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Content Text Color'),
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Content Text Color'),
			'class'		=>'color',
			'maxlength' => '250',
			'required'  => true,
			'disabled'  => $isElementDisabled
		));
		
		$call_to_action_type=$fieldset->addField('call_to_action_type', 'select', array(
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Type'),
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Type'),
			'name'      => 'call_to_action_type',
			'values'   => array(
				'showlinkwithborowsebutton' => Mage::helper('cybernetikz_cnslider')->__('Call to Action with Button'),
				'showlinkwithimage' => Mage::helper('cybernetikz_cnslider')->__('Call to Action with Banner Image')
			),
		 
		));
		
		/**
		 * Active or Deactive
		 */
		$call_to_action=$fieldset->addField('call_to_action', 'select', array(
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action'),
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action'),
			'name'      => 'call_to_action',
			'required'  => true,
			'value'  => '1',
			'values'   => array(
				'1' => Mage::helper('cybernetikz_cnslider')->__('Enabled'),
				'0' => Mage::helper('cybernetikz_cnslider')->__('Disabled')
			),
		 
		));
		
		$call_action_text=$fieldset->addField('call_action_text', 'text', array(
			'name'      => 'call_action_text',
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Button Text'),
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Button Text'),
			'after_element_html' => '<small>Call to Action Button Text i.e "Shop Now"</small>',
			'maxlength' => '250',
			'required'  => true,
			'disabled'  => $isElementDisabled
		));
		
		$call_action_textcolor=$fieldset->addField('call_action_textcolor', 'text', array(
			'name'      => 'call_action_textcolor',
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Text Color'),
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Text Color'),
			'class'		=>'color',
			'maxlength' => '250',
			'required'  => true,
			'disabled'  => $isElementDisabled
		));
		
		$call_action_text_shadow=$fieldset->addField('call_action_text_shadow', 'text', array(
			'name'      => 'call_action_text_shadow',
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Text Effect Color'),
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Text Effect Color'),
			'class'		=>'color',
			'maxlength' => '250',
			'required'  => true,
			'disabled'  => $isElementDisabled
		));
		
		$call_action_background_from=$fieldset->addField('call_action_background_from', 'text', array(
			'name'      => 'call_action_background_from',
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Background Top Gradient'),
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Background Top Gradient'),
			'class'		=>'color',
			'maxlength' => '250',
			'required'  => true,
			'disabled'  => $isElementDisabled
		));
		
		$call_action_background_to=$fieldset->addField('call_action_background_to', 'text', array(
			'name'      => 'call_action_background_to',
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Background Bottom Gradient'),
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Background Bottom Gradient'),
			'class'		=>'color',
			'maxlength' => '250',
			'required'  => true,
			'disabled'  => $isElementDisabled
		));
		
		$call_action_background_border=$fieldset->addField('call_action_background_border', 'text', array(
			'name'      => 'call_action_background_border',
			'title'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Border Color'),
			'label'     => Mage::helper('cybernetikz_cnslider')->__('Call to Action Border Color'),
			'class'		=>'color',
			'maxlength' => '250',
			'required'  => true,
			'disabled'  => $isElementDisabled
		));
	
		$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
			->addFieldMap($call_to_action_type->getHtmlId(), $call_to_action_type->getName())
			->addFieldMap($call_to_action->getHtmlId(), $call_to_action->getName())
			->addFieldMap($call_action_text->getHtmlId(), $call_action_text->getName())
			->addFieldMap($call_action_textcolor->getHtmlId(), $call_action_textcolor->getName())
			->addFieldMap($call_action_text_shadow->getHtmlId(), $call_action_text_shadow->getName())
			->addFieldMap($call_action_background_from->getHtmlId(), $call_action_background_from->getName())
			->addFieldMap($call_action_background_to->getHtmlId(), $call_action_background_to->getName())
			->addFieldMap($call_action_background_border->getHtmlId(), $call_action_background_border->getName())
			->addFieldDependence(
				$call_to_action->getName(),
				$call_to_action_type->getName(),
				'showlinkwithborowsebutton'
			)
			->addFieldDependence(
				$call_action_text->getName(),
				$call_to_action_type->getName(),
				'showlinkwithborowsebutton'
			)
			->addFieldDependence(
				$call_action_textcolor->getName(),
				$call_to_action_type->getName(),
				'showlinkwithborowsebutton'
			)
			->addFieldDependence(
				$call_action_text_shadow->getName(),
				$call_to_action_type->getName(),
				'showlinkwithborowsebutton'
			)
			->addFieldDependence(
				$call_action_background_from->getName(),
				$call_to_action_type->getName(),
				'showlinkwithborowsebutton'
			)->addFieldDependence(
				$call_action_background_to->getName(),
				$call_to_action_type->getName(),
				'showlinkwithborowsebutton'
			)
			->addFieldDependence(
				$call_action_background_border->getName(),
				$call_to_action_type->getName(),
				'showlinkwithborowsebutton'
			)
			->addFieldDependence(
				$call_action_text->getName(),
				$call_to_action->getName(),
				'1'
			)
			->addFieldDependence(
				$call_action_textcolor->getName(),
				$call_to_action->getName(),
				'1'
			)
			->addFieldDependence(
				$call_action_text_shadow->getName(),
				$call_to_action->getName(),
				'1'
			)
			->addFieldDependence(
				$call_action_background_from->getName(),
				$call_to_action->getName(),
				'1'
			)->addFieldDependence(
				$call_action_background_to->getName(),
				$call_to_action->getName(),
				'1'
			)
			->addFieldDependence(
				$call_action_background_border->getName(),
				$call_to_action->getName(),
				'1'
			)
		);
		
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
            //'tab_id' => $this->getTabId()
        ));

       	if(Mage::helper('cybernetikz_cnslider')->isEnableContent()){	
			$contentField = $fieldset->addField('content', 'editor', array(
				'name'     => 'content',
				'style'     => 'height:30em; width:50em;',
				'title'     => Mage::helper('cybernetikz_cnslider')->__('Banner Content'),
				'label'     => Mage::helper('cybernetikz_cnslider')->__('Banner Content'),
				'required' => true,
				'disabled' => $isElementDisabled,
				'config'   => $wysiwygConfig
			));
		}
		
		$storeid = explode(',',$model->getStoreId());
		$allcatids = explode(',',$model->getCatId());
		$scatid="";
		foreach($allcatids as $value){
			$scatid[]=trim(str_replace(array("cns_","_cns"),array("",""),$value));
		}
		
		$model->setStoreId($storeid);
		$model->setCatId($scatid);
		
        $form->setValues($model->getData());
		$form->setUseContainer(true);
        $this->setForm($form);
		
        return parent::_prepareForm();
    }
	
	/**
     * Retrieve predefined additional element types
     *
     * @return array
     */
     protected function _getAdditionalElementTypes()
     {
         return array(
            'image' => Mage::getConfig()->getBlockClassName('cybernetikz_cnslider/adminhtml_cnslider_edit_form_element_image')
        );
     }
}