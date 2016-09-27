<?php
 
class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Target
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	public function detail()
	{
		$layout = Mage::getSingleton('core/layout');
	 
		  
		$update = $layout->getUpdate();
		$update->load('order_items');
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
		//echo '<pre>';print_r($row);die;
		$order_id = $row['increment_id'];$result = "";
		
		//$order = Mage::getModel('sales/order')->load($order_id);echo '<pre>';print_r($order);die;
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		
		$items = $order->getAllVisibleItems();
		//echo '<pre>';print_r($items);die;
		
		$viewImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/btn_show-hide_icon.gif';
		$customerViewImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/fam_user.gif';
		$closeImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/rule_component_remove.gif';
		
		$itemcount=count($items);
		$name=array();
		$unitPrice=array();
		$sku=array();
		$ids=array();
		$qty=array();
		
		$block = $this->getLayout()->createBlock('ordermanager/adminhtml_ordermanager')
			   ->setData('order',$order)
			   ->setTemplate('ordermanager/detail.phtml'); 
		 echo $block->renderView();
		
		
		$customerBlock = $this->getLayout()->createBlock('ordermanager/adminhtml_ordermanager')
			   ->setData('order',$order)
			   ->setTemplate('ordermanager/customerdetail.phtml'); 
		 echo $customerBlock->renderView();
		 
		/* $block = $this->getLayout()->createBlock('adminhtml/sales_order_view')
			   ->setData('order',$order)
			   ->setOrder($order)
			   ->setTemplate('sales/order/view/info.phtml');
			   
			     $layout = Mage::getModel('core/layout');
				$layout->getUpdate()->load('order_info');
				$layout->generateXml()->generateBlocks();
				echo $layout->getOutput();
			
	*/		   

//echo Mage::app()->getLayout()->createBlock('adminhtml/sales_order_view_info', null, array('order'=>$order, 'template'=>'sales/order/view/info.phtm'))->toHtml();
	 
		//echo $block->renderView();
		
		$jsOrderId = "'d_".$order_id."'";
		$jsCusOrderId = "'c_".$order_id."'";
		
		/* FOr account Information start*/
	//	$result .= '<table><tr><td>';
		/* FOr account Information ends*/
		
	//	$result .= '<table><tr><td colspan="5">';

	$result .= '<a href="javascript:void(0);"><div style="width:200px;"><img style="margin-left:5px;" onclick="showDetail('.$jsOrderId.');" src="'.$viewImage.'" />';
	$result .= '<img style="margin-left:5px;" onclick="hideDetail('.$jsOrderId.')" src="'.$closeImage.'" />';

$result .= '<img style="margin-left:5px;" onclick="showCusDetail('.$jsCusOrderId.');" src="'.$customerViewImage.'" />';
$result .= '<img style="margin-left:5px;" onclick="hideCusDetail('.$jsCusOrderId.')" src="'.$closeImage.'" /></div></a>';


	$itemOnGrid = Mage::getStoreConfig('orderview/general/hide_product_view');
		
		if($itemOnGrid == 0)
		{
		
			//$result .='<td style="font-weight:bold;">Image</td> <td style="font-weight:bold;">Qty</td>
			//	   <td style="font-weight:bold;">Product</td><td style="font-weight:bold;">Status</td> <td style="font-weight:bold;">Price</td></tr>';
		//	$result .='<td style="font-weight:bold;">Image</td>  <td style="font-weight:bold;">Product</td> </tr>';
		}
		
		$i = 0 ;
		$_coreHelper = Mage::helper('core');
		
		foreach ($items as $itemId => $item)
		{
			//echo '<pre>';print_r($item);die;
		 	$imageSize = Mage::getStoreConfig('orderview/general/product_thumb_size');
		 
		   $productName = $item->getName();
		   $_productId = $item->getProductId();
		   $_product = Mage::getModel('catalog/product')->load($_productId);
		   $productImage = Mage::helper('catalog/image')->init($_product, 'image')->resize($imageSize);
 
		    
					
					
		   if($itemOnGrid == 0)
		   {
				$result .= '<div style="clear:both;"><div style="float:left;width:29%"><img src="'.$productImage.'" /></div><div style="margin:3px;">'.$productName.'</div></div>';
				//$result .= '<td>'.$orderQty.'</td>';
				//$result .= '<div>'.$productName.'</div>';
			//	$result .= '<td>'.$itemStatus.'</td>';
			//	$result .= '<td>'.$itemPrice.'</td>';
//				$result .= '</tr>';
		  }
		  
			$i++;
		} 
		//$result .= '</table>' ;
		
		/* FOr account Information*/
			//$result .= '</td>';
			 
		/* FOr account Information ends*/
		
	/*	$result .= '<td><span style="font-weight:bold;">Name: </span>'.$this->htmlEscape($order->getCustomerName()).'<br/>';
		$result .= '<span style="font-weight:bold;">Email:</span> <a href="mailto:'.$order->getCustomerEmail().'"><strong>'.$order->getCustomerEmail().'</strong></a>';
		$isAllowed = Mage::getStoreConfigFlag('sales/general/hide_customer_ip', $order->getStoreId());
		if($order->getRemoteIp() && !$isAllowed){
			$result .= '<br/><span style="font-weight:bold;">IP: '.$order->getRemoteIp(); 
		}
		$result .= '</td>';*/
		
//		$result .= '</tr></table>';
		return $result;
		die;		
	 
    }
}
?>