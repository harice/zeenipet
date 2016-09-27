<?php
class Cybernetikz_Cnslider_Block_Adminhtml_Cnslider_Grid_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    } 
    protected function _getValue(Varien_Object $row)
    {       
        $val = $row->getData($this->getColumn()->getIndex());
		$val = str_replace("no_selection", "", $val);
        $url = Mage::getBaseUrl('media') . 'cnslider/' . $val; 
		/*
		echo $row->getSliderId();
		$slider=Mage::getModel('cybernetikz_cnslider/slider')->load($row->getSliderId());
		$width=60;
		$url=Mage::helper('cybernetikz_cnslider/image')->resize($slider, $width);*/
        $out = "<img src=". $url ." width='60px'/>"; 
        return $out;
    }
}
?>