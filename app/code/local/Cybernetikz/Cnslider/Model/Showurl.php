<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Model_Showurl {
    public function toOptionArray()
    {
        return array(
            array('value'=>'showlinkwithimage', 'label'=>Mage::helper('cybernetikz_cnslider')->__('Call to Action with Slider Image')),
			array('value'=>'showlinkwithborowsebutton', 'label'=>Mage::helper('cybernetikz_cnslider')->__('Call to Action with Button')),                    
        );
    }
}