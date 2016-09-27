<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Block_Adminhtml_Cnslidercat extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'cybernetikz_cnslider';
        $this->_controller = 'adminhtml_cnslidercat';
        $this->_headerText = Mage::helper('cybernetikz_cnslider')->__('Manage Sliders');

        parent::__construct();

        if (Mage::helper('cybernetikz_cnslider/admin')->isActionAllowed('save')) {
            $this->_updateButton('add', 'label', Mage::helper('cybernetikz_cnslider')->__('Add New Slider'));
        } else {
            $this->_removeButton('add');
        }
    }
}