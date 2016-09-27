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


class Aitoc_Aitoptionstemplate_Model_Export extends Mage_Core_Model_Abstract
{   
    protected $_delimiter='FUMAFUMAFUMA__FUMAFUMAFUMA';    
    protected $_Xml;
    protected $_numLines=1000;
    
    public function createXml()
    {
        $string = 
        '<?xml version="1.0"?> 
        <tables />';
        $xml = simplexml_load_string($string);
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_option',$xml);
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_optionprice',$xml);
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_optiontitle',$xml);
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_optiontemplate',$xml);
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_option2template',$xml);
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_required',$xml);
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_option_typevalue',$xml);
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_option_typeprice',$xml);
        
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_option_typetitle',$xml);
        
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_option_dependable',$xml);
        $xml = $this->_getModelToXml('aitoptionstemplate/reserve_catalog_product_option_dependable_child',$xml);
        #echo 'Tag Name'.$xml->getName();
        #echo "XML:<pre>".print_r($xml,1)."</pre>";
        $this->_Xml = $xml;
        $this->_Xml->asXML($this->getFilePath());
        $session = Mage::getModel('adminhtml/session');
        $session->setStep('csv');
        return array('step' => 'xml','check' =>$session->getCheckStatus());
    }

    protected function _insertCsvArrayIntoTable($arr)
    {
        $model = Mage::getModel('aitoptionstemplate/reserve_catalog_product_template');
        $resource = $model->getResource();
        $session = Mage::getModel('adminhtml/session');
        $page = $session->getPage();
        if($page <= 2)
        {
            $resource->getReadConnection()->truncate($resource->getMainTable());
        } 
        foreach($arr as $fields)
        {
            if(!empty($fields))
            {
                $model = Mage::getModel('aitoptionstemplate/reserve_catalog_product_template');
                $model->setData('product_sku',$fields[1]);
                $model->setData('template_id',$fields[2]);
                $model->setData('sort_order',$fields[3]);
                $model->save();                
            }
        }
    }
    
    protected function _uploadXmlFile()
    {
        foreach($this->_Xml->children() as $table=>$item)
        {
            #echo $table."<pre>".print_r($item ,1)."</pre>";
            $this->_insertXmlIntoTable($table,$item);
        }
        return $this;
    }
    
    protected function _insertXmlIntoTable($table,$xml)
    {
        $module = Mage::getModel('aitoptionstemplate/'.$table);
        $resource = $module->getResource();
        $resource->getReadConnection()->truncate($resource->getMainTable());
        foreach($xml as $name=>$value)
        {
            $arr = array();
            //echo $value[0]->option_id;
            foreach($value as $key=>$element)
            {
                if($key!='reserve_id')
                    $arr[$key] = (string)$value[0]->{$key}[0];
            }
            $module->setData($arr);
            //we have truncated table so we always set up models to use INSERT instead of UPDATE, if protected method _useIsObjectNew set to true in resource model
            $module->isObjectNew(true);
            #echo $table."<pre>".print_r($module,1)."</pre>";
            $module->save();
        }
    }
    

    
    protected function _getModelToXml($modelName,$xml)
    {
        $model = Mage::getModel($modelName);
        $resource = $model->getResource();
        $collection  = $model->getCollection();
        $columns = $resource->getReadConnection()->describeTable($resource->getMainTable());
        #echo "<pre>".print_r($columns,1)."</pre>";
        $name = substr($model->getResourceName(),strpos($model->getResourceName(),'/')+1);
        $table = $xml->addChild($name);
        //var_dump($string);
        #echo "XML:<pre>".print_r($xml,1)."</pre>";
        foreach($collection as $item)
        {
            $row = $table->addChild('item');
            foreach($columns as $column=>$value)
            {
                //$row->addChild($column,$item->getData($column));
                $row->$column =$item->getData($column);
            }
        }
        #echo "XML:<pre>".print_r($xml,1)."</pre>";
        return $xml;   
    }


/*
 * @refactor
 * simplify
 */
    public function createCsv()
    {
        $arr = array();
        $model = Mage::getModel('aitoptionstemplate/reserve_catalog_product_template');
        $resource = $model->getResource();
        $session = Mage::getModel('adminhtml/session');
        $page = $session->getPage();
        if(empty($page))
        {
            $page = 1; 
        }
        $collection  = $model->getCollection()->setCurPage($page)->setPageSize($this->_numLines);
        $size = $model->getCollection()->getSize();
        if($session->getStep()=='Upload')  
        {
            $session->setCheckStatus(null);
            $session->setStep(null);
            return file_get_contents($this->getFilePath());
        }
        $fp = fopen($this->getFilePath(), 'a');
        if($page==1)
            fwrite($fp,$this->_delimiter.PHP_EOL);
        #print_r($arr);       

        foreach ($collection as $fields) 
        {
            fputcsv($fp, array_map('addslashes', $fields->getData()),',','"');
        }
        $page++;
        $session->setPage($page);
        $arr['page'] = $page;
        #$session->setFilePosition($filePosition);
        #$arr['file_position'] = $filePosition;
        fclose($fp);
        $step = $session->getStep();
        $arr['step'] = $step;
        if($size>=$this->_numLines)
        {
             $arr['increment'] = round(round(($page-1)*$this->_numLines/($size),1)*70,0);
        }
        else
        {
            $arr['increment'] = 70;
        }
        
        if($size<=$this->_numLines*($page-1))
        {
            $session->setFilePosition(null);
            $session->setPage(null);
            $session->setStep('Upload');
            return array('step'=>'Upload','check'=>$session->getCheckStatus(),'size'=>$size);      
        }
        else
        {
            return $arr;
        }
        
        //return $csv;
    }


/*
 * @refactor
 * simplify
 */
    public function importFile($path)
    {
        set_time_limit(5);
        $session = Mage::getModel('adminhtml/session');
        $step = $session->getStep();
        $backup = fopen($path,'r'); 
        if(empty($step))
        {
            $session->setStep('csv_import');
            $session->setPage(1);
            $xml_content='';
            $line = '';
            while((!feof($backup))&&($line!=$this->_delimiter.PHP_EOL))
            {
                $line = fgets($backup);
                if($line!=$this->_delimiter.PHP_EOL)
                    $xml_content.= $line;
            }
            $this->_Xml = simplexml_load_string($xml_content);
            $this->_uploadXmlFile();
        }
        
        $page = $session->getPage();
        $page++;
        $step = $session->getStep();
        $session->setPage($page);
        #print_r($content);
        $csv_array = array();
        
        $pointer = $session->getPointer();
        if(!empty($pointer))
        {
            fseek($backup,$pointer);
        }
        $line = 0;
        while((!feof($backup))&&($line<$this->_numLines))
        {
            #$cont = array_map('stripslashes',fgetcsv($backup,0,',','"'));
            #echo $line."Content:".print_r($cont);
            $arr = fgetcsv($backup,0,',','"');
            if(!empty($arr))
            {
                $csv_array[] = array_map('stripslashes',$arr);
            }
            $line++;
        }
        $session->setPointer(ftell($backup));
        $this->_insertCsvArrayIntoTable($csv_array);
        $fileLines = count(file($path))-3;
        if($fileLines>=$this->_numLines)
        {
            $csv_array['increment'] = round(round(($page-1)*$this->_numLines/$fileLines,1)*70,0);
        }
        else
        {
            $csv_array['increment'] = 70;
        }
        
        
        if(feof($backup))
        {
            $session->setStep('restore');
            $session->setPointer(null);
            $session->setPage(null);
            fclose($backup);
            unlink($path);
        }
        $step = $session->getStep();
        $csv_array['step'] = $step;
        $csv_array['page'] = $page;
        $csv_array['pointer'] = $session->getPointer();
        return $csv_array;
        #echo htmlspecialchars($xml_content);

        
    }
    
    

}