<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Cnslider_Model_Resource_Cat extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
		$this->_init('cybernetikz_cnslider/cat', 'cat_id');
    }
}