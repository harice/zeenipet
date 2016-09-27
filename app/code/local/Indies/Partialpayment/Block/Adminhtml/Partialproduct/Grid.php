<?php
class Indies_Partialpayment_Block_Adminhtml_Partialproduct_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('partialproductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('asc');
    }
     
    protected function _prepareCollection()
    {
       $productList = Mage::getModel('catalog/product')->getCollection();
	   $productList->addFieldToFilter('partial_payment',1);
	   //$productList->getSelect()->joinLeft('sales_flat_order_item', 'main_table.order_item_id = sales_flat_order_item.item_id', array('product_type'));
        $this->setCollection($productList);
        return parent::_prepareCollection();
    }
     
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('partialpayment')->__('ID'),
            'index'     => 'entity_id',
            ));
         
        $this->addColumn('sku', array(
            'header'    => Mage::helper('partialpayment')->__('sku'),
            'index'     => 'sku',
            ));
         
        return parent::_prepareColumns();
    }
         
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }
}