<?php
/**
 * Custom Options Templates
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitoptionstemplate
 * @version      3.2.2
 * @license:     FoqAPsDYiV5k0g2ao7Zx8pPoJiVNkUYZBpxixzEerG
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2010 AITOC, Inc. 
 */
  class Aitoc_Aitoptionstemplate_Model_Mysql4_Reserve_Catalog_Product_Template extends Aitoc_Aitoptionstemplate_Model_Mysql4_Aitproduct2tpl
{
    
    protected $_templateId = null;
    
    protected function _construct()
    {
        $this->_init('aitoptionstemplate/reserve_catalog_product_template','reserve_id');
    }
    
     /**
	 * @param $fetchAll is added for compatibility with Aitoc_Aitoptionstemplate_Model_Mysql4_Aitproduct2tpl::getTemplateProducts
	 */
	public function getTemplateProducts($iTemplateId, $fetchAll = false)
    {
        $stmt = $this->_getReadAdapter()->select()
           ->from($this->getTable('aitoptionstemplate/aitproduct2tpl'), array('product_id','sort_order'))
           ->where('template_id = ?', $iTemplateId);

        $aProdHash = $this->_getReadAdapter()->fetchAll($stmt);
        return $aProdHash;
    }
    
    public function getProductTemplates($iProductId, $bActiveOnly = true)
    {
        if ($bActiveOnly)
        {
            $sWhereActive = 'at.is_active = 1';
        }
        else 
        {
            $sWhereActive = '1';
        }
        
        $stmt = $this->_getReadAdapter()->select()
           ->from($this->getTable('aitoptionstemplate/reserve_catalog_product_template')." AS p2t", array('reserve_id','template_id', 'sort_order','product_sku'))
           ->joinInner($this->getTable('aitoptionstemplate/reserve_catalog_product_optiontemplate')." AS at"," p2t.template_id = at.template_id",array('template_id','description','title'))
           ->where('p2t.product_sku = ?', $iProductId)
           ->where($sWhereActive)
           ->order('p2t.sort_order DESC')
           ->order('p2t.template_id DESC');
        $aTemplates = $this->_getReadAdapter()->fetchAll($stmt);
        return $aTemplates;
    }
    
    public function getProductSkuesByIds(array $ids , $getSortOrder = false)
    {
        $where = '';
        if(!empty($ids))
        {
            $where = ' product.entity_id IN ('. join("," , $ids) .')';   
        }   
        $sql = $this->_getReadAdapter()->select()
                ->from($this->getTable('catalog/product')." AS product", array('product_sku' => 'sku','product_id' => 'entity_id'));
       if($getSortOrder)
       {
            $sql->joinLeft($this->getTable('aitoptionstemplate/aitproduct2tpl') . " AS rel", " rel.product_id = product.entity_id" ,array('sort_order'));   
       }
       $sql->where($where)
           ->order('product.entity_id ASC');   
       return $this->_getReadAdapter()->fetchAll($sql);  
    }
    
    protected function _prepareForInsert(&$params , $key)
    {
        unset($params['product_id']);
        $params['template_id'] = $this->_templateId;       
    }
    
    public function reserveTemplateProducts($templateId = null)
    {
        //set template id for array_walk
        $this->_templateId = $templateId;
        $this->_getWriteAdapter()->delete(
           $this->getTable('aitoptionstemplate/reserve_catalog_product_template'),
           'template_id = "' . intval($templateId) . '" '
        ); 
        $products = Mage::getResourceModel('aitoptionstemplate/aitproduct2tpl')->getTemplateProducts($templateId, true);
/*        $productData = $this->getProductSkuesByIds(array_keys($products) , true);
        array_walk($productData , array($this , '_prepareForInsert'));
        if(empty($productData))
        {
            return false;
        }
        return $this->_getWriteAdapter()->insertArray($this->getMainTable(),array('product_sku' , 'sort_order' , 'template_id' ) , $productData);
*/
        if(count($products) > 0)
        {
            $productData = $this->getProductSkuesByIds(array_keys($products) , true);
            array_walk($productData , array($this , '_prepareForInsert'));
            return $this->_getWriteAdapter()->insertArray($this->getMainTable(),array('product_sku' , 'sort_order' , 'template_id') , $productData);
        }
        else
        {
            return false;
        }
    }
    
}