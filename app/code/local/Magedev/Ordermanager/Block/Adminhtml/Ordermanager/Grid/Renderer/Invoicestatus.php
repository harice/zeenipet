<?php
 
class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Invoicestatus
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
		$invoiceId = $row->getId();

		$invoice = Mage::getModel('sales/order_invoice')
                    ->load($invoiceId);
		$statuses = Mage::getModel('sales/order_invoice')->getStates();
		$invoiceState = $invoice->getState();
		$invoiceStateLabel = $statuses[$invoiceState];
		return	$string = '<span id="edit_invoice_status_'.$invoiceId.'" title="Click to edit">'.$invoiceStateLabel.'</span>';
		 
    }
}
?>