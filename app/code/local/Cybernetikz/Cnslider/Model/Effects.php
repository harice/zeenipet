<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Model_Effects {
    public function toOptionArray()
    {
        return array(
            array('value'=>'fade', 'label'=>Mage::helper('cybernetikz_cnslider')->__('fade')),
			array('value'=>'fadeZoom', 'label'=>Mage::helper('cybernetikz_cnslider')->__('fadeZoom')),
			array('value'=>'all', 'label'=>Mage::helper('cybernetikz_cnslider')->__('Random')),
			array('value'=>'wipe', 'label'=>Mage::helper('cybernetikz_cnslider')->__('wipe')),
            array('value'=>'scrollUp', 'label'=>Mage::helper('cybernetikz_cnslider')->__('scrollUp')),            
            array('value'=>'scrollDown', 'label'=>Mage::helper('cybernetikz_cnslider')->__('scrollDown')), 
			array('value'=>'scrollLeft', 'label'=>Mage::helper('cybernetikz_cnslider')->__('scrollLeft')), 
			array('value'=>'scrollRight', 'label'=>Mage::helper('cybernetikz_cnslider')->__('scrollRight')), 
			array('value'=>'cover', 'label'=>Mage::helper('cybernetikz_cnslider')->__('cover')), 
			array('value'=>'shuffle', 'label'=>Mage::helper('cybernetikz_cnslider')->__('shuffle')), 
			array('value'=>'toss', 'label'=>Mage::helper('cybernetikz_cnslider')->__('toss')),
			array('value'=>'turnUp', 'label'=>Mage::helper('cybernetikz_cnslider')->__('turnUp')), 
			array('value'=>'turnDown', 'label'=>Mage::helper('cybernetikz_cnslider')->__('turnDown')), 
			array('value'=>'turnRight', 'label'=>Mage::helper('cybernetikz_cnslider')->__('turnRight')),
			array('value'=>'blindX', 'label'=>Mage::helper('cybernetikz_cnslider')->__('blindX')),
			array('value'=>'blindY', 'label'=>Mage::helper('cybernetikz_cnslider')->__('blindY')),
			array('value'=>'blindZ', 'label'=>Mage::helper('cybernetikz_cnslider')->__('blindZ')),                 
        );
    }
}