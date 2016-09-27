<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Block_Bannerpreview extends Mage_Core_Block_Template
{
    protected $_bannerCollection = null;
	
	 protected $_sliderCollection = null;

    protected function _getCollection($bannerid=null)
    {
		$collection=Mage::getResourceModel('cybernetikz_cnslider/slider_collection');

		$collection->addFieldToFilter('slider_id',array(
			array('eq'=>$bannerid)
		));

		return $collection;
    }

    public function getCollection($sliderid=null)
    {
        Mage::helper('cybernetikz_cnslider')->getCnlv();
		
		if (is_null($this->_bannerCollection)) {
            $this->_bannerCollection = $this->_getCollection($sliderid);
        }
        return $this->_bannerCollection;
    }
	
	protected function _getSliderCollection($sliderid=null)
    {
		$collection=Mage::getResourceModel('cybernetikz_cnslider/cat_collection');
			$collection->addFieldToFilter('cat_id',array(
				array('eq'=>$sliderid)
			));
			
		$collection->addOrder('sort_order','ASC');
		
		//echo $collection->getSelect()->__toString();
		//print_r($collection->getData());
		//exit;
		return $collection;
    }

    public function getSliderCollection($sliderid=null)
    {
        if (is_null($this->_sliderCollection)) {
            $this->_sliderCollection = $this->_getSliderCollection($sliderid);
        }
        return $this->_sliderCollection;
    }

    public function getImageUrl($item, $width)
    {
        return Mage::helper('cybernetikz_cnslider/image')->resize($item, $width);
    }
}
