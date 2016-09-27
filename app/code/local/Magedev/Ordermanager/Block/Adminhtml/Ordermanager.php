<?php
class Magedev_Ordermanager_Block_Adminhtml_Ordermanager extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_ordermanager';
    $this->_blockGroup = 'ordermanager';
    $this->_headerText = Mage::helper('ordermanager')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('ordermanager')->__('Add Item');
    parent::__construct();
  }
}