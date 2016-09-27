<?php

class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Tracking
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
		$order_id = $row['increment_id'];
		$result = "";
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

		$tracknums[0] = '';
/*******/
		 $tracking = Mage::getResourceModel('sales/order_shipment_track_collection')
					->setOrderFilter($order)
					->getData();
				
		foreach ($tracking as $trackData)
		{	
			$tracknums[] = $trackData['track_number'].'/'. $trackData['entity_id'] ; 
		}
	
/*******/

		$tracknums = array_filter($tracknums);
		$countTracknums =  count($tracknums);
		if($countTracknums >0 ){
			return implode(",",$tracknums);
		}else{
			return '';
		}
 
    }
}
?>