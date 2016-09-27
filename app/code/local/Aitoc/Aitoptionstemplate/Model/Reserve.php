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
class Aitoc_Aitoptionstemplate_Model_Reserve extends Mage_Core_Model_Abstract
{
    
    protected function compareModels($model,$compareModel,$joinField,$inverse=false)
    {
        
        $resource = Mage::getModel($model)->getResource();
        $compareResource = Mage::getModel($compareModel)->getResource();
        $columns = $resource->getReadConnection()->describeTable($resource->getMainTable());
        if($inverse)
        {
            unset($columns['reserve_id']);
        }
        $sql = 'SELECT COUNT(*) - ('.
        ' SELECT COUNT(*) FROM '.$resource->getMainTable().') FROM '.$resource->getMainTable().' as res '.
        ' INNER JOIN '.$compareResource->getMainTable().' as ish on res.'.$joinField.'=ish.'.$joinField.' WHERE ';
        foreach($columns as $column=>$value)
        {
            $arr[] = ' COALESCE(ish.'.$column.',\'\') = COALESCE(res.'.$column.',\'\') ';
        }
        $sql .= join('AND',$arr); 
        #print($sql."<br>");
        $result = Mage::getModel($model)->getResource()->getReadConnection()->query($sql);
        return (bool)!$result->fetchColumn(0);      
    }
    
    protected function compareValues($model,$compareModel,$joinField)
    {
        $resource = Mage::getModel($model)->getResource();
        $compareResource = Mage::getModel($compareModel)->getResource();
        $columns = $resource->getReadConnection()->describeTable($resource->getMainTable());
        unset($columns['reserve_id']);
        $sql='SELECT COUNT(*) - ('.
            ' SELECT COUNT(*) FROM '.$resource->getTable('aitoptionstemplate/aitoption2tpl').' as temp '.
            ' INNER JOIN '.$resource->getTable('catalog/product_option_type_value').' as val on temp.option_id=val.option_id '.            
            ' INNER JOIN '.$resource->getMainTable().' as tl on val.option_type_id=tl.option_type_id) '.
            ' FROM '.$resource->getTable('aitoptionstemplate/aitoption2tpl').' as temp '.
            ' INNER JOIN '.$resource->getTable('catalog/product_option_type_value').' as val on temp.option_id=val.option_id 
'.
            ' INNER JOIN '.$resource->getMainTable().' as tl on val.option_type_id=tl.option_type_id '.
            ' INNER JOIN '.$compareResource->getMainTable().' as res on res.'.$joinField.'=tl.'.$joinField.'  WHERE ';
        foreach($columns as $column=>$value)
        {
            $arr[] = ' COALESCE(res.'.$column.',\'\') = COALESCE(tl.'.$column.',\'\') ';
        }
        $sql .= join('AND',$arr); 
        #print('COMPARE VALUES:'.$sql."<br>");

        $result = Mage::getModel($model)->getResource()->getReadConnection()->query($sql);
        return (bool)!$result->fetchColumn(0);  
    }
    
    
    protected function compareOptionValues($model,$compareModel,$idField)
    {
        $resource = Mage::getModel($model)->getResource();
        $compareResource = Mage::getModel($compareModel)->getResource();
        $columns = $resource->getReadConnection()->describeTable($resource->getMainTable());
        unset($columns['reserve_id']);
        $sql='SELECT Count(*)-('.
        ' SELECT COUNT(*) FROM '.$resource->getTable('aitoptionstemplate/aitoption2tpl').' as tmp '.
        ' INNER JOIN '.$compareResource->getMainTable().' as ish on ish.option_id=tmp.option_id) '.
        ' FROM '.$resource->getTable('aitoptionstemplate/aitoption2tpl').' as temp '.
        ' LEFT JOIN '.$resource->getMainTable().' as opt on opt.'.$idField.'=temp.'.$idField.' 
INNER JOIN '.$compareResource->getMainTable().' as ish on ish.option_id=opt.option_id WHERE ';
        #print_r($columns);
        foreach($columns as $column=>$value)
        {
            $arr[] = ' COALESCE(ish.'.$column.',\'\') = COALESCE(opt.'.$column.',\'\') ';
        }
        $sql .= join('AND',$arr); 
        #print($sql."<br>");
        $result = Mage::getModel($model)->getResource()->getReadConnection()->query($sql); 
        return (bool)!$result->fetchColumn(0);  
    }    
    
    
    protected function compareOptions($model,$compareModel,$idField)
    {
        $resource = Mage::getModel($model)->getResource();
        $compareResource = Mage::getModel($compareModel)->getResource();
        $columns = $resource->getReadConnection()->describeTable($resource->getMainTable());
        unset($columns['reserve_id']);
        $sql='SELECT Count(*)-('.
        ' SELECT COUNT(*) FROM '.$resource->getTable('aitoptionstemplate/aitoption2tpl').
        ' as op INNER JOIN '.$resource->getMainTable().' as mn on op.'.$idField.'=mn.'.$idField.') FROM '.$resource->getTable('aitoptionstemplate/aitoption2tpl').' as temp '.
        ' INNER JOIN '.$resource->getMainTable().' as opt on opt.'.$idField.'=temp.'.$idField.' 
INNER JOIN '.$compareResource->getMainTable().' as ish on ish.option_id=opt.option_id WHERE ';
        foreach($columns as $column=>$value)
        {
            $arr[] = ' COALESCE(ish.'.$column.',\'\') = COALESCE(opt.'.$column.',\'\') ';
        }
        $sql .= join('AND',$arr); 
        #print($sql."<br>");exit;
        $result = Mage::getModel($model)->getResource()->getReadConnection()->query($sql);
        return (bool)!$result->fetchColumn(0);    
    }
    
    protected function compareProductTemplates($inverse = false )
    {
        $resource = Mage::getModel('catalog/product')->getResource();
        if($inverse)
        {
            $sql = 'SELECT COUNT(DISTINCT(reserve_id))-('.
            ' SELECT COUNT(*) FROM '.$resource->getTable('aitoptionstemplate/aitproduct2tpl').') FROM '.$resource->getTable('aitoptionstemplate/aitproduct2tpl').' as temp '.
            ' INNER JOIN '.$resource->getTable('catalog/product').' AS prod on prod.entity_id=temp.product_id '.
            ' INNER JOIN '.$resource->getTable('aitoptionstemplate/reserve_catalog_product_template').' AS base on prod.sku=base.product_sku';
        }
        else
        {
            $sql = 'SELECT COUNT(*)-('.
            ' SELECT COUNT(DISTINCT(reserve_id)) FROM '.$resource->getTable('catalog/product').' AS base '.
            ' INNER JOIN '.$resource->getTable('aitoptionstemplate/reserve_catalog_product_template').' AS prod on prod.product_sku=base.sku '.
            ') FROM '.$resource->getTable('aitoptionstemplate/aitproduct2tpl').' AS temp ';
        }
        #print($sql."<br>");
        $result = Mage::getModel('aitoptionstemplate/reserve_catalog_product_template')->getResource()->getReadConnection()->query($sql);
        return (bool)!$result->fetchColumn(0);
    }
    
    public function compareDependableOptions($inverse = false )
    {
        $resource = Mage::getModel('aitoptionstemplate/product_option_dependable')->getResource();
        if($inverse)
        {
            $sql = 'SELECT COUNT(*)- '.
            '('.
                ' SELECT COUNT(*) FROM '.$resource->getTable('aitoptionstemplate/aitoption2tpl').' AS temp '.
                ' INNER JOIN '.$resource->getTable('aitoptionstemplate/product_option_dependable').' AS dep on temp.option_id=dep.option_id '.
            ')'.
            ' FROM '.$resource->getTable('aitoptionstemplate/reserve_product_option_dependable').' as base ';
        }
        else
        {
            $sql = 'SELECT COUNT(*)-'.
            '('.
                ' SELECT COUNT(*) FROM '.$resource->getTable('aitoptionstemplate/reserve_product_option_dependable').' AS base '.
            ') '.
            ' FROM '.$resource->getTable('aitoptionstemplate/aitoption2tpl').' AS temp '.
            ' INNER JOIN '.$resource->getTable('aitoptionstemplate/product_option_dependable').' AS dep on temp.option_id=dep.option_id ';
        }
        #print($sql."<br>");
        $result = Mage::getModel('aitoptionstemplate/reserve_catalog_product_template')->getResource()->getReadConnection()->query($sql);
        return (bool)!$result->fetchColumn(0);
    }
    
    protected function isReserveSynchronized()
    {
        $flag = true;
        $flag &= $this->compareModels('aitoptionstemplate/reserve_catalog_product_optiontemplate','aitoptionstemplate/aittemplate','template_id',true);
        $flag &= $this->compareModels('aitoptionstemplate/reserve_catalog_product_required','aitoptionstemplate/aitproduct2required','product_id',true);
        $flag &= $this->compareProductTemplates();
        $flag &= $this->compareModels('aitoptionstemplate/reserve_catalog_product_option2template','aitoptionstemplate/aitoption2tpl','option_id',true);
        $flag &= $this->compareOptionValues('aitoptionstemplate/reserve_catalog_product_optiontitle','aitoptionstemplate/product_option_title','option_id');
        $flag &= $this->compareOptions('aitoptionstemplate/reserve_catalog_product_option','catalog/product_option','option_id');
        $flag &= $this->compareOptionValues('aitoptionstemplate/reserve_catalog_product_optionprice','aitoptionstemplate/product_option_price','option_id');
        $flag &= $this->compareOptionValues('aitoptionstemplate/reserve_catalog_product_option_typevalue','catalog/product_option_value','option_id');
        $flag &= $this->compareValues('aitoptionstemplate/reserve_catalog_product_option_typetitle','aitoptionstemplate/product_option_value_title','option_type_title_id');
        $flag &= $this->compareValues('aitoptionstemplate/reserve_catalog_product_option_typeprice','aitoptionstemplate/product_option_value_price','option_type_price_id');
        $flag &= $this->compareDependableOptions();
        return $flag;
    }
 
    protected function isBaseSynchronized()
    {
        $flag = true; 
        $flag &= $this->compareModels('aitoptionstemplate/aittemplate','aitoptionstemplate/reserve_catalog_product_optiontemplate','template_id');
        $flag &= $this->compareModels('aitoptionstemplate/aitproduct2required','aitoptionstemplate/reserve_catalog_product_required','product_id');
        $flag &= $this->compareModels('aitoptionstemplate/aitoption2tpl','aitoptionstemplate/reserve_catalog_product_option2template','option_id');
        $flag &= $this->compareProductTemplates(true);
        $flag &= $this->compareOptions('aitoptionstemplate/reserve_catalog_product_option','catalog/product_option','option_id');
        $flag &= $this->compareOptionValues('aitoptionstemplate/product_option_title','aitoptionstemplate/reserve_catalog_product_optiontitle','option_id');
        $flag &= $this->compareOptionValues('aitoptionstemplate/product_option_price','aitoptionstemplate/reserve_catalog_product_optionprice','option_id');
        $flag &= $this->compareOptionValues('catalog/product_option_value','aitoptionstemplate/reserve_catalog_product_option_typevalue','option_id');
        $flag &= $this->compareValues('aitoptionstemplate/product_option_value_title','aitoptionstemplate/reserve_catalog_product_option_typetitle','option_type_title_id');
        $flag &= $this->compareValues('aitoptionstemplate/product_option_value_price','aitoptionstemplate/reserve_catalog_product_option_typeprice','option_type_price_id');
        $flag &= $this->compareDependableOptions(true);
        return $flag;
    }
    
    public function checkSynchronization()
    {
        return (bool)($this->isReserveSynchronized() && $this->isBaseSynchronized());
    }
    
    protected function reserveTable($model,$reserveModel,$delete=true)
    {
        $resourceReserve = Mage::getModel($reserveModel)->getResource();
        $resource = Mage::getModel($model)->getResource();
        //removing all data from reserving table
        if($delete==true)
		{
			$resourceReserve->getReadConnection()->query('set foreign_key_checks=0;');
			$resourceReserve->getReadConnection()->truncate($resourceReserve->getMainTable());
		}
        
        $sql = 
            'set foreign_key_checks=0;'.
            'INSERT DELAYED IGNORE INTO '.$resourceReserve->getMainTable().'( ';
        
        $columns = $resource->getReadConnection()->describeTable($resource->getMainTable());
        unset($columns['reserve_id']);
        $sql .= join(',',array_keys($columns));
        $sql .= ' ) SELECT ';
        $sql .= join(',',array_keys($columns));
        $sql .= ' FROM '.$resource->getMainTable().' ;SET foreign_key_checks = 1;';
        #print('RESERVING:'.$sql.'<br>');
        $resourceReserve->getReadConnection()->query($sql);
    }
    
    protected function reserveTemplateRelations()
    {
        $resourceReserve = Mage::getModel('aitoptionstemplate/reserve_catalog_product_template')->getResource();
        $resourceReserve->getReadConnection()->truncate($resourceReserve->getMainTable());
        //removing all data from reserving table
        $sql='set foreign_key_checks=0; INSERT DELAYED IGNORE INTO '.$resourceReserve->getTable('aitoptionstemplate/reserve_catalog_product_template').'(product_sku,template_id,sort_order) '.
        ' SELECT sku,template_id,sort_order from '.$resourceReserve->getTable('aitoptionstemplate/aitproduct2tpl').' as temp '.
        ' INNER JOIN '.$resourceReserve->getTable('catalog/product').' as ent on temp.product_id=ent.entity_id ; set foreign_key_checks=1;';
        #print($sql.'-'.$resourceReserve->getMainTable());
        $resourceReserve->getReadConnection()->query($sql);
    }
    
    protected function restoreTemplateRelations()
    {
        $resource = Mage::getModel('aitoptionstemplate/aitproduct2tpl')->getResource();
        //removing all data from reserving table
        #echo "Main table Relations:".$resource->getMainTable()."<br>";
        $resource->getReadConnection()->truncate($resource->getMainTable());
        $sql = 'set foreign_key_checks=0; INSERT DELAYED IGNORE INTO '.$resource->getTable('aitoptionstemplate/aitproduct2tpl').'(product_id,template_id,sort_order) '.
        ' SELECT entity_id,template_id,sort_order from '.$resource->getTable('aitoptionstemplate/reserve_catalog_product_template').' as temp '.
        ' INNER JOIN '.$resource->getTable('catalog/product').' as ent on temp.product_sku=ent.sku'.' ;set foreign_key_checks=1;';
        #echo $sql;
        $resource->getReadConnection()->query($sql);
               
    }
    
    protected function reserveOptions($model,$reserveModel)
    {
        $resourceReserve = Mage::getModel($reserveModel)->getResource();
        $resource = Mage::getModel($model)->getResource();
        //removing all data from reserving table
        $result = $resourceReserve->getReadConnection()->truncate($resourceReserve->getMainTable());
        $sql = 'set foreign_key_checks=0;INSERT DELAYED IGNORE INTO '.$resourceReserve->getMainTable().'( ';
        
        $columns = $resource->getReadConnection()->describeTable($resource->getMainTable());
        unset($columns['reserve_id']);
        $sql .= join(',',array_keys($columns));
        $sql .= ' ) SELECT opt.';
        $sql .= join(', opt.',array_keys($columns));
        $sql.=' FROM '.$resource->getTable('aitoptionstemplate/aitoption2tpl').' as temp INNER JOIN '.$resource->getMainTable();
        $sql.=' as opt on temp.option_id=opt.option_id; set foreign_key_checks=1;';
        #print($sql."<br>");
        $resourceReserve->getReadConnection()->query($sql);        
    }
    
    protected function reserveValues($model,$reserveModel)
    {
        $resourceReserve = Mage::getModel($reserveModel)->getResource();
        $resource = Mage::getModel($model)->getResource();
        //removing all data from reserving table
        $result = $resourceReserve->getReadConnection()->truncate($resourceReserve->getMainTable());
        $sql = 'INSERT DELAYED IGNORE INTO '.$resourceReserve->getMainTable().'( ';
        
        $columns = $resource->getReadConnection()->describeTable($resource->getMainTable());
        unset($columns['reserve_id']);
        $sql .= join(',',array_keys($columns));
        $sql .= ' ) SELECT tit.';
        $sql .= join(', tit.',array_keys($columns));
        $sql.=' FROM '.$resource->getTable('aitoptionstemplate/aitoption2tpl').' as temp '.
        ' INNER JOIN '.$resource->getTable('catalog/product_option_type_value').' as opt on temp.option_id=opt.option_id '.
        'INNER JOIN '.$resource->getMainTable();
        $sql.=' as tit on tit.option_type_id=opt.option_type_id';
        #print($sql."<br>");
        $resourceReserve->getReadConnection()->query($sql);     
    }
    
    protected function reserveDependableOptions()
    {
        $resourceReserve = Mage::getModel('aitoptionstemplate/reserve_catalog_product_option_dependable')->getResource();
        //removing all data from reserving table, child table will be cleared by foreign key
        $resourceReserve->getReadConnection()->truncate($resourceReserve->getMainTable());
        
        $columns = $resourceReserve->getReadConnection()->describeTable($resourceReserve->getMainTable());
        unset($columns['template_id']);
        unset($columns['reserve_id']);

        $childColumns = $resourceReserve->getReadConnection()->describeTable($resourceReserve->getTable('aitoptionstemplate/product_option_dependable_child'));
        unset($childColumns['id']);
        $childColumns = array_keys($childColumns);
        $childColumnsPlain = implode(',',$childColumns);
        $childColumnsTable = array();
        foreach($childColumns as $column) {
            $childColumnsTable[] = 'rows.'.$column;
        }
        $childColumnsTable = implode(',',$childColumnsTable);
        $sql='set foreign_key_checks=0; INSERT DELAYED IGNORE INTO '.$resourceReserve->getMainTable().'(template_id, '.implode(',',array_keys($columns)).') '.
        ' SELECT template_id, dep.* FROM '.$resourceReserve->getTable('aitoptionstemplate/aitoption2tpl').' as temp '.
        ' INNER JOIN '.$resourceReserve->getTable('aitoptionstemplate/product_option_dependable').' as dep on temp.option_id=dep.option_id ; '.
        ' INSERT DELAYED IGNORE INTO '.$resourceReserve->getTable('aitoptionstemplate/reserve_product_option_dependable_child').'('.$childColumnsPlain.') '.
        ' SELECT '.$childColumnsTable.' FROM '.$resourceReserve->getTable('aitoptionstemplate/aitoption2tpl').' as temp '.
        ' INNER JOIN '.$resourceReserve->getMainTable().' as dep on temp.option_id=dep.option_id '.
        ' INNER JOIN '.$resourceReserve->getTable('aitoptionstemplate/product_option_dependable_child').' as rows on rows.row_id=dep.row_id;'.        
        'set foreign_key_checks=1;';
        #print($sql.'-'.$resourceReserve->getMainTable());
        $resourceReserve->getReadConnection()->query($sql);
    }
    
    protected function restoreDependableOptions()
    {
        $resource = Mage::getModel('aitoptionstemplate/product_option_dependable')->getResource();
        
        $columns = $resource->getReadConnection()->describeTable($resource->getMainTable());
        $columns = implode(',',array_keys($columns));

        $childColumns = $resource->getReadConnection()->describeTable($resource->getTable('aitoptionstemplate/product_option_dependable_child'));
        unset($childColumns['id']);
        $childColumns = implode(',',array_keys($childColumns));
        
        $sql='set foreign_key_checks=0; INSERT DELAYED IGNORE INTO '.$resource->getMainTable().'('.$columns.') '.
        ' SELECT '.$columns.' FROM '.$resource->getTable('aitoptionstemplate/reserve_product_option_dependable').' as reserve ;';
        $sql_childs = ' INSERT DELAYED IGNORE INTO '.$resource->getTable('aitoptionstemplate/product_option_dependable_child').'('.$childColumns.') '.
        ' SELECT '.$childColumns.' FROM '.$resource->getTable('aitoptionstemplate/reserve_product_option_dependable_child').' as rows ;'.
        'set foreign_key_checks=1;';
        #print($sql.'-'.$resourceReserve->getMainTable());
        $resource->getReadConnection()->query($sql);
        $resource->getReadConnection()->query($sql_childs);
    }
    
    public function reserveTemplates()
    {
        $this->reserveTemplateRelations();
        $this->reserveTable('aitoptionstemplate/aitoption2tpl','aitoptionstemplate/reserve_catalog_product_option2template');
        $this->reserveTable('aitoptionstemplate/aittemplate','aitoptionstemplate/reserve_catalog_product_optiontemplate');
        $this->reserveTable('aitoptionstemplate/aitproduct2required','aitoptionstemplate/reserve_catalog_product_required');
        $this->reserveOptions('catalog/product_option','aitoptionstemplate/reserve_catalog_product_option');
        $this->reserveOptions('aitoptionstemplate/product_option_title','aitoptionstemplate/reserve_catalog_product_optiontitle');
        $this->reserveOptions('aitoptionstemplate/product_option_price','aitoptionstemplate/reserve_catalog_product_optionprice');
        $this->reserveOptions('catalog/product_option_value','aitoptionstemplate/reserve_catalog_product_option_typevalue');   
        $this->reserveValues('aitoptionstemplate/product_option_value_title','aitoptionstemplate/reserve_catalog_product_option_typetitle');         
        $this->reserveValues('aitoptionstemplate/product_option_value_price','aitoptionstemplate/reserve_catalog_product_option_typeprice');         
        $this->reserveDependableOptions();
    }
    
    protected function _deleteOldOptions()
    {
        $resource = Mage::getResourceModel('aitoptionstemplate/aitoption2tpl');
        $sql='SELECT * FROM '.$resource->getMainTable();
        $result = $resource->getReadConnection()->query($sql)->fetchAll(); 
        $sqlDelete = 'DELETE FROM '.$resource->getTable('catalog/product_option').' where option_id IN(' ;   
        $sqlDeleteTitle = 'DELETE FROM '.$resource->getTable('catalog/product_option_title').' where option_id IN(' ; 
        $sqlDeletePrice = 'DELETE FROM '.$resource->getTable('catalog/product_option_price').' where option_id IN(' ;
        $sqlDeleteValue = 'DELETE FROM '.$resource->getTable('catalog/product_option_type_value').' where option_id IN(' ; 
        $sqlSelectValue = 'SELECT * FROM '.$resource->getTable('catalog/product_option_type_value').' where option_id IN(' ;
        #echo $sql;
        #print_r($result);
        foreach ($result as $option)
        {
              $sqlDelete.= $option['option_id'].' ,';
              $sqlDeleteTitle.= $option['option_id'].' ,'; 
              $sqlDeletePrice.= $option['option_id'].' ,';
              $sqlDeleteValue.= $option['option_id'].' ,';
              $sqlSelectValue.= $option['option_id'].' ,';   
        }
        $sqlDelete.=' 0)';
        $sqlDeleteTitle.=' 0)';  
        $sqlDeletePrice.=' 0)';
        $sqlDeleteValue.=' 0)';
        $sqlSelectValue.=' 0)';     
        #print_r($sqlDelete.'<br>'.$sqlDeleteTitle);
        $result = $resource->getReadConnection()->query($sqlSelectValue)->fetchAll();
        $sqlDeleteTypeTitle =  'DELETE FROM '.$resource->getTable('catalog/product_option_type_title').' where option_type_id IN(' ;
        $sqlDeleteTypePrice =  'DELETE FROM '.$resource->getTable('catalog/product_option_type_price').' where option_type_id IN(' ;
        foreach($result as $value)
        {
            $sqlDeleteTypeTitle.= $value['option_type_id'].' ,';
            $sqlDeleteTypePrice.= $value['option_type_id'].' ,';  
        }
        $sqlDeleteTypeTitle.=' 0)';  
        $sqlDeleteTypePrice.=' 0)';
        #echo  $sqlSelectValue.'<----SELECT <br>'.$sqlDeleteTypeTitle.'<--Title<br>'.$sqlDeleteTypePrice.'<---Price<br>'.$sqlDeleteValue.'<---value<br>'.$sqlDeleteTitle."<--TITle<br>".$sqlDeletePrice.'<---Price<br>'.$sqlDelete.'<---Option<br>';
        #exit;
        $resource->getReadConnection()->query($sqlDeleteTypeTitle);
        $resource->getReadConnection()->query($sqlDeleteTypePrice);
        $resource->getReadConnection()->query($sqlDeleteValue);   
        $resource->getReadConnection()->query($sqlDeleteTitle);
        $resource->getReadConnection()->query($sqlDeletePrice);
        $resource->getReadConnection()->query($sqlDelete);
    }
    
    protected function _addNewOptions()
    {
        $this->reserveTable('aitoptionstemplate/reserve_catalog_product_option','catalog/product_option',false);
        $this->reserveTable('aitoptionstemplate/reserve_catalog_product_optiontitle','aitoptionstemplate/product_option_title',false); 
        $this->reserveTable('aitoptionstemplate/reserve_catalog_product_optionprice','aitoptionstemplate/product_option_price',false); 
        $this->reserveTable('aitoptionstemplate/reserve_catalog_product_option_typevalue','catalog/product_option_value',false); 
        $this->reserveTable('aitoptionstemplate/reserve_catalog_product_option_typeprice','aitoptionstemplate/product_option_value_price',false); 
        $this->reserveTable('aitoptionstemplate/reserve_catalog_product_option_typetitle','aitoptionstemplate/product_option_value_title',false); 
    }    
    
    protected function _updateOptionsProductRelations()
    {
        $resource = Mage::getResourceModel('aitoptionstemplate/reserve_catalog_product_option'); 
        $config = Mage::getConfig()->getStoresConfigByPath('general/aitoptionstemplate/default_product_id'); 
        $sql = 'Update '.$resource->getMainTable().' SET product_id='.$config[0];
        $resource->getReadConnection()->query($sql);
    }
    
    public function getEmptyProducts()
    {
        $resource = Mage::getModel('aitoptionstemplate/aitproduct2tpl')->getResource();
        //removing all data from reserving table
        #echo "Main table Relations:".$resource->getMainTable()."<br>";
        $sql =  ' SELECT DISTINCT(product_sku) from '.$resource->getTable('aitoptionstemplate/reserve_catalog_product_template').' as temp '.
                ' LEFT JOIN '.$resource->getTable('catalog/product').' as ent on temp.product_sku=ent.sku'.
                ' WHERE ent.entity_id IS NULL';
        #echo $sql;
        $result = $resource->getReadConnection()->query($sql);
        return (array)$result->fetchAll();      
    }
     
    public function restoreTemplates()       
    {
        $this->_updateOptionsProductRelations();          
        $this->_deleteOldOptions();
        $this->reserveTable('aitoptionstemplate/reserve_catalog_product_optiontemplate','aitoptionstemplate/aittemplate'); 
        $this->restoreTemplateRelations(); 
        $this->_addNewOptions();
        $this->reserveTable('aitoptionstemplate/reserve_catalog_product_option2template','aitoptionstemplate/aitoption2tpl');
        $this->reserveTable('aitoptionstemplate/reserve_catalog_product_required','aitoptionstemplate/aitproduct2required');
        $this->restoreDependableOptions();
        #return ($this->getEmptyProducts());
    }
}