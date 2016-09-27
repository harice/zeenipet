<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Block_Adminhtml_Cnslider extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'cybernetikz_cnslider';
        $this->_controller = 'adminhtml_cnslider';
        $this->_headerText = Mage::helper('cybernetikz_cnslider')->__('Manage Banners');

        parent::__construct();

        if (Mage::helper('cybernetikz_cnslider/admin')->isActionAllowed('save')) {
            $this->_updateButton('add', 'label', Mage::helper('cybernetikz_cnslider')->__('Add New Banner'));
        } else {
            $this->_removeButton('add');
        }
		
        $this->addButton(
            'slider_flush_images_cache',
            array(
                'label'      => Mage::helper('cybernetikz_cnslider')->__('Flush Images Cache'),
                'onclick'    => 'setLocation(\'' . $this->getUrl('*/*/flush') . '\')',
            )
        );

    }
}