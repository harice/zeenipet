<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 28.12.11
 * Time: 9:38
 * To change this template use File | Settings | File Templates.
 */
class Infomodus_Upslabel_Model_Observer
{
    public function initUpslabel($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ((get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction' || get_class($block) == 'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction') && $block->getRequest()->getControllerName() == 'sales_order') {
            $block->addItem('upslabel_pdflabels', array(
                'label' => Mage::helper('sales')->__('Print UPS Shipping Labels'),
                'url' => Mage::app()->getStore()->getUrl('upslabel/adminhtml_pdflabels'),
            ));
        }
        return $this;
    }
}
