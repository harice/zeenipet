<?php 

class Magedev_Ordermanager_Model_Resource_Order_Grid_Collection extends Mage_Sales_Model_Resource_Order_Grid_Collection
{
	  public function getSelectCountSql1()
    {
      $countSelect = parent::getSelectCountSql();
	  
	  //echo '<br/>';
	//  $countSelect->reset(Zend_Db_Select::GROUP);
	 // die;
	  return $countSelect->reset(Zend_Db_Select::GROUP);
    }
	
	    public function getSelectCountSql()
    {
//        if ($this->getIsCustomerMode()) {
            $this->_renderFilters();

            $unionSelect = clone $this->getSelect();

            $unionSelect->reset(Zend_Db_Select::COLUMNS);
            $unionSelect->columns('main_table.entity_id');

            $unionSelect->reset(Zend_Db_Select::ORDER);
            $unionSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $unionSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

            $countSelect = clone $this->getSelect();
            $countSelect->reset();
            $countSelect->from(array('a' => $unionSelect), 'COUNT(*)');
//        } else {
//            $countSelect = parent::getSelectCountSql();
//        }

        return $countSelect;
    }
}

?>