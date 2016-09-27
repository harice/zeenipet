<?php

class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Options
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
		return $this->_getValue($row);
    }
	
	 public function _getValue(Varien_Object $row)
     {
	 	 $format_val = '';$finalResult = array();  $result = array();
		$order_id = $row['increment_id'];$result = "";
		
		$textLimit = Mage::getStoreConfig('orderview/general/custom_option_limit');

		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

		$items = $order->getAllVisibleItems();
			$block = Mage::app()->getLayout()->createBlock('sales/order_item_renderer_default');
			foreach ($items as $item) {
			//attach the item to the block
			$_options = $item->getProductOptions();
//			  echo '<pre>';print_r($_options); 

		   if (isset($_options['options']) && is_array($_options['options'])  ) {
				$options = array();
				foreach ($_options['options'] as $option) {
					  $valLength = substr($option['value'] ,0, $textLimit); ;
					 $options[] = '<span style="font-weight:bold;">' . $option['label'] . '</span>'. ' : ' . $valLength;
				}
				$format_val .= implode(',<br/> ', $options);//echo $format_val;die;
				$format_val .= '<br/>';
			}
			else if (isset($_options['attributes_info']) && is_array($_options['attributes_info'])  ) { //configurable products
				$options = array();
				foreach ($_options['attributes_info'] as $option) {
						
					  $valLength = substr($option['value'] ,0, $textLimit); ;
					 $options[] = '<span style="font-weight:bold;">' . $option['label'] . '</span>'. ' : ' . $valLength;
				}
				
				$format_val .= implode(',<br/> ', $options);//echo $format_val;die;
				$format_val .= '<br/>';
			}
			else if (isset($_options['bundle_options']) && is_array($_options['bundle_options'])) {
                    // bundle
                    $options = array();
                    foreach ($_options['bundle_options'] as $option) {
                        if (is_array($option['value'])) {
                            $values = array();
                            foreach ($option['value'] as $value) {
							
								$valLength = substr($value['title'] ,0, $textLimit);
                                $values[] = $valLength;
                            }
							
                            $options[] = '<span style="font-weight:bold;">'.$option['label'] . '</span>'.': ' . implode(', ', $values);
                        } else {                        
							$valLength = substr($option['value'] , 0,$textLimit);
                            $options[] = $option['label'] . ' : ' . $valLength;
                        }
                    }
                    $format_val .= implode(',<br/> ', $options);
					$format_val .= '<br/>';
                }
		} 
		 return $format_val;
     }
}
?>