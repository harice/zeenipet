<?php
 
class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Orderstatus
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
	 
		$orderId = $row->getId();$string = "";
		$order = Mage::getModel('sales/order')->load($orderId);
 		$path = $this->getUrl("ordermanager/adminhtml_ordereditor/save/field/order_status/type/edit_ord/")."order/".$order->getId();
 
		$statuses = Mage::getSingleton('sales/order_config')->getStatuses(); 
		$i = 0 ;$str = ''; 
		$count = count($statuses) ;
		 foreach($statuses as $key=>$value)
		 {
			if($i <= $count){
				$str .= "['"  .  $key  ."', '"  . $value . "'], " ; 
			 }	
		 }
		 
		$string = '<span id="edit_order_status" title="Click to edit">'.ucfirst($order->getStatus()).'</span>';
		$string .= '<script type="text/javascript">';
		$string .=	'new Ajax.InPlaceCollectionEditor("edit_order_status", "'.$path.'",{ highlightColor:"#E2F1B1",';
		return $string .=  'collection: [ '.$str.']});</script>';
  
 	}
}
?>