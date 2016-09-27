<?php
 
class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Ordergroup
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
		$order_id = $row['increment_id'];$result = "";
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		$orderGroup = $order->getOrderGroup();
		if($orderGroup == 1){return Mage::helper('sales')->__('Archived');}
		else{return Mage::helper('sales')->__('Active');}
		 
    }
}
?>