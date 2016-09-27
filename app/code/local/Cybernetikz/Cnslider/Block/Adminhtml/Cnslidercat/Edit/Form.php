<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Block_Adminhtml_Cnslidercat_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
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
            'legend' => Mage::helper('cybernetikz_cnslider')->__('Category Content'),
            'class'  => 'fieldset-medium'
        ));
		
		if ($model->getId()) {
            $fieldset->addField('cat_id', 'hidden', array(
                'name' => 'cat_id',
            ));
        }
		
				
		$fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Title'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Title'),
            'maxlength' => '250',
            'required'  => true,
			'disabled'  => $isElementDisabled
        ));
 		
		$font_size=$fieldset->addField('font_size', 'select', array(
            'name'      => 'font_size',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Font Size'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Font Size'),
			'options'   => array(
                '28px' => Mage::helper('cybernetikz_cnslider')->__('Large'),
                '22px' => Mage::helper('cybernetikz_cnslider')->__('Medium'),
				'16px' => Mage::helper('cybernetikz_cnslider')->__('Small')
            ),
			'disabled'  => $isElementDisabled
        ));
				
		$sort_order=$fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Order'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Order'),
			'class'		=> 'validate-number',
            'maxlength' => '20',
            'required'  => false,
			'disabled'  => $isElementDisabled
        ));
		
		$settings = $fieldset->addField('settings', 'select', array(
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Configuration'),
            'name'      => 'settings',
			'required'  => true,
            'values'    => array(
                '' => '---Select---',
				'system_configuration' => 'System Configuration',
                'custom_configuration'   => 'Custom Configuration'
            )
        ));
		
		$show_title=$fieldset->addField('show_title', 'select', array(
            'name'      => 'show_title',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Show Title'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Show Title'),
			'after_element_html' => '<small>Please select "Yes/No" for Show/Hide Title in each banner image.</small>',
			'options'   => array(
                '1' => Mage::helper('cybernetikz_cnslider')->__('Yes'),
                '0' => Mage::helper('cybernetikz_cnslider')->__('No')
            ),
			'disabled'  => $isElementDisabled
        ));
		
		$show_content=$fieldset->addField('show_content', 'select', array(
            'name'      => 'show_content',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Show Content'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Show Content'),
			'after_element_html' => '<small>Please select "Yes/No" for Show/Hide Content in each banner image.</small>',
			'options'   => array(
                '1' => Mage::helper('cybernetikz_cnslider')->__('Yes'),
                '0' => Mage::helper('cybernetikz_cnslider')->__('No')
            ),
			'disabled'  => $isElementDisabled
        ));
		
		$show_link=$fieldset->addField('show_link', 'select', array(
            'name'      => 'show_link',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Set Call to Action URL'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Set Call to Action URL'),
			'after_element_html' => '<small>Please select "Yes/No" for Show/Hide Call to Action URL in each banner image.</small>',
			'options'   => array(
                '1' => Mage::helper('cybernetikz_cnslider')->__('Yes'),
                '0' => Mage::helper('cybernetikz_cnslider')->__('No')
            ),
			'disabled'  => $isElementDisabled
        ));
		
		$width=$fieldset->addField('width', 'text', array(
            'name'      => 'width',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Slider Width'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Slider Width'),
			'after_element_html' => '<small>Please enter the width of the slider.</small>',
			'class'		=> 'validate-number',
            'maxlength' => '20',
            'required'  => true,
			'disabled'  => $isElementDisabled
        ));
		
		$height=$fieldset->addField('height', 'text', array(
            'name'      => 'height',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Slider Height'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Slider Height'),
			'after_element_html' => '<small>Please enter the height of the slider.</small>',
			'class'		=> 'validate-number',
            'maxlength' => '20',
            'required'  => true,
			'disabled'  => $isElementDisabled
        ));
		
		$alleffects=Mage::getModel('cybernetikz_cnslider/effects')->toOptionArray();
		$effects="";
		foreach($alleffects as $effect){
			$effects[$effect['value']]=$effect['label'];
		}
		
		$effect=$fieldset->addField('effect', 'select', array(
            'name'      => 'effect',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Transition Effect'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Transition Effect'),
			'after_element_html' => '<small>Please select the transition effect you would like to use.</small>',
            'options'   => $effects,
			'disabled'  => $isElementDisabled
        ));
		
		$delay=$fieldset->addField('delay', 'text', array(
            'name'      => 'delay',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Transition Delay'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Transition Delay'),
			'after_element_html' => '<small>Length of time (in seconds) you would like each slide to be visible.</small>',
			'class'		=> 'validate-number',
            'maxlength' => '20',
            'required'  => true,
			'disabled'  => $isElementDisabled
        ));
		
		$length=$fieldset->addField('length', 'text', array(
            'name'      => 'length',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Transition Length'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Transition Length'),
			'class'		=> 'validate-number',
			'after_element_html' => '<small>Length of time (in seconds) you would like the transition length.</small>',
            'maxlength' => '20',
            'required'  => true,
			'disabled'  => $isElementDisabled
        ));
		
		$playpause=$fieldset->addField('playpause', 'select', array(
            'name'      => 'playpause',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Play & Pause'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Play & Pause'),
			'after_element_html' => '<small>Please select "Yes/No" for Show/Hide Play & Pause.</small>',
			'options'   => array(
                '1' => Mage::helper('cybernetikz_cnslider')->__('Yes'),
                '0' => Mage::helper('cybernetikz_cnslider')->__('No')
            ),
			'disabled'  => $isElementDisabled
        ));
		
		$pagination=$fieldset->addField('pagination', 'select', array(
            'name'      => 'pagination',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Pagination'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Pagination'),
			'after_element_html' => '<small>Please select "Yes/No" for Show/Hide Pagination.</small>',
			'options'   => array(
                '1' => Mage::helper('cybernetikz_cnslider')->__('Yes'),
                '0' => Mage::helper('cybernetikz_cnslider')->__('No')
            ),
			'disabled'  => $isElementDisabled
        ));
		
		$nextprev=$fieldset->addField('nextprev', 'select', array(
            'name'      => 'nextprev',
            'title'     => Mage::helper('cybernetikz_cnslider')->__('Next & Prev'),
            'label'     => Mage::helper('cybernetikz_cnslider')->__('Next & Prev'),
			'after_element_html' => '<small>Please select "Yes/No" for Show/Hide Next & Prev.</small>',
			'options'   => array(
                '1' => Mage::helper('cybernetikz_cnslider')->__('Yes'),
                '0' => Mage::helper('cybernetikz_cnslider')->__('No')
            ),
			'disabled'=>$isElementDisabled
        ));
				
        $form->setValues($model->getData());
		$form->setUseContainer(true);
        $this->setForm($form);
		
		$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($settings->getHtmlId(), $settings->getName())
            ->addFieldMap($show_title->getHtmlId(), $show_title->getName())
			->addFieldMap($show_content->getHtmlId(), $show_content->getName())
			->addFieldMap($show_link->getHtmlId(), $show_link->getName())
			->addFieldMap($width->getHtmlId(), $width->getName())
            ->addFieldMap($height->getHtmlId(), $height->getName())
			->addFieldMap($effect->getHtmlId(), $effect->getName())
			->addFieldMap($delay->getHtmlId(), $delay->getName())
			->addFieldMap($length->getHtmlId(), $length->getName())
			->addFieldMap($playpause->getHtmlId(), $playpause->getName())
			->addFieldMap($pagination->getHtmlId(), $pagination->getName())
			->addFieldMap($nextprev->getHtmlId(), $nextprev->getName())
            ->addFieldDependence(
				$show_title->getName(),
                $settings->getName(),
                'custom_configuration'
            )
			->addFieldDependence(
				$show_content->getName(),
                $settings->getName(),
                'custom_configuration'
            )
			->addFieldDependence(
				$show_link->getName(),
                $settings->getName(),
                'custom_configuration'
            )
			->addFieldDependence(
				$width->getName(),
                $settings->getName(),
                'custom_configuration'
            )
            ->addFieldDependence(
				$height->getName(),
                $settings->getName(),
                'custom_configuration'
            )
			->addFieldDependence(
				$effect->getName(),
                $settings->getName(),
                'custom_configuration'
            )
			->addFieldDependence(
				$delay->getName(),
                $settings->getName(),
                'custom_configuration'
            )
			->addFieldDependence(
				$length->getName(),
                $settings->getName(),
                'custom_configuration'
            )
            ->addFieldDependence(
				$playpause->getName(),
                $settings->getName(),
                'custom_configuration'
            )->addFieldDependence(
				$pagination->getName(),
                $settings->getName(),
                'custom_configuration'
            )
            ->addFieldDependence(
				$nextprev->getName(),
                $settings->getName(),
                'custom_configuration'
            )
        );
		
        return parent::_prepareForm();
    }
}