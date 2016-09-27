<?php
 
class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Shipping
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	public function detail()
	{
		$layout = Mage::getSingleton('core/layout');
	 
		  
		$update = $layout->getUpdate();
		$update->load('order_grid_detail');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();

		$result = array( 'outputHtml' => $output, 'otherVar' => 'foo', );
		$this->getResponse()->setBody(Zend_Json::encode($result));        
	}
	
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
		return $order->getShippingDescription().'<br/>('.$order->getShippingMethod().')';
		 
    }
}
?>