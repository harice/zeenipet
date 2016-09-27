<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Model_Cat extends Mage_Core_Model_Abstract
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('cybernetikz_cnslider/cat');
    }

    /**
     * If object is new adds creation date
     *
     * @return Cybernetikz_Cnslider_Model_Slider
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->isObjectNew()) {
            $this->setData('created_at', Varien_Date::now());
        }
        return $this;
    }
}