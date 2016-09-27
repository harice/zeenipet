<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      1.1.7
 * @license:     CGRl40OoIpwl63Yy9HmSwXtQ6ZlFDRlIXEc7HbfxdJ
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @author Adjustware
 */   
class AdjustWare_Deliverydate_Block_Adminhtml_Holiday extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_holiday';
    $this->_blockGroup = 'adjdeliverydate';
    $this->_headerText = Mage::helper('adjdeliverydate')->__('Holidays');
    $this->_addButtonLabel = Mage::helper('adjdeliverydate')->__('Add Holiday');
    parent::__construct();
  }
}