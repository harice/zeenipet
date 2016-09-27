<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Helper_Admin extends Mage_Core_Helper_Abstract
{
    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    public function isActionAllowed($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cnslider/manage/' . $action);
    }
}