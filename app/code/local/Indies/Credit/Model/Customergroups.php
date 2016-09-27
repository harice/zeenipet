<?php
class Indies_Credit_Model_Customergroups extends Varien_Object
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) 
		{
            $this->_options = Mage::getResourceModel('customer/group_collection')
                ->setRealGroupsFilter()
                ->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
?>