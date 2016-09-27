<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      1.1.7
 * @license:     CGRl40OoIpwl63Yy9HmSwXtQ6ZlFDRlIXEc7HbfxdJ
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @author Adjustware
 */ 
class AdjustWare_Deliverydate_Block_Renderer_Deliverydate
    extends Mage_Core_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{    
    private $_element;
    
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->setTemplate('adjdeliverydate/renderer/field.phtml');
    }
   
    
    private function _initEndDate()
    {
		$cart = Mage::helper('checkout/cart')->getCart()->getQuote()->getAllItems();
		$iMaxMoreDays = 14; 
		foreach($cart as $item) {
			$_customOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
			foreach($_customOptions['options'] as $_option){
				if($_option['label']=='Upload Your Logo' && !empty($_option['value']))
					$iMaxMoreDays = 21;
			}
		}
		
        $iMaxDays = intval(Mage::getStoreConfig('checkout/adjdeliverydate/max'))+$iMaxMoreDays;
        if ($iMaxDays) 
        {
            echo $endDate = strftime('new Date(%Y,%m-1,%e)',time()+$iMaxDays*86400);
        }
        if(isset($endDate))
        {
            $this->setEndDate($endDate);
            return;
        }
        $this->setEndDate(0);  
    }
    
    private function _initToday()
    {
        $holiday = Mage::getSingleton('adjdeliverydate/holiday');
        $firstAvailable = $holiday->getFirstAvailableDate();
		//Pradeep
		foreach($cart as $item) {
			$_customOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
			foreach($_customOptions['options'] as $_option){
				if($_option['label']=='Upload Your Logo' && !empty($_option['value'])){
					//$iMaxDays = 21;
					$firstAvailable = date("Y-m-d",strtotime("+21 days"));
				}
			}
		}
		echo $firstAvailable."--";
		//Pradeep
        if (!$this->_element->getValue() || $holiday->isHoliday($this->_element->getValue('yyyy-MM-dd')))
        {
            $this->_element->setValue($firstAvailable);
        }        
        $today = $this->_getDateJs($firstAvailable);
        $this->setToday($today);
    }
    
    private function _initHolidays()
    {
        $holiday = Mage::getSingleton('adjdeliverydate/holiday');
        $holidays = '';
        foreach ($holiday->getHolidays() as $iKey => $aYear)
        {
            $holidays .= 'DELIVERY_HOLIDAY[' . $iKey . '] = {' . $this->_getHolidaysJs($aYear) . '};';
        }
        $this->setHolidays($holidays);  
    }

    private function _initWeekend()
    {
        $holiday = Mage::getSingleton('adjdeliverydate/holiday');
        $weekend = $holiday->getWeekend();
        $this->setWeekend($weekend);
    }
    
    
    private function _init()
    {
        $this->_initEndDate();
        $this->_initToday();
        $this->_initHolidays();
        $this->_initWeekend();
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        $this->_init();
        
        $html = $this->_element->getLabelHtml() . '<br />' . $this->_element->getElementHtml();
        $js = $this->_toHtml($html);
        $result = str_replace('Calendar.setup({', $js, $html);
        return $result; 
    }
    
    // date in Y-m-d format
    private function _getDateJs($date)
    {
        list($y, $m, $d) = explode('-', $date);
        return ' new Date(' . join(',', array((int)$y, (int)$m-1, (int)$d)) . ')';
    }
    
    private function _getHolidaysJs($mon)
    {
        $js = ' ';
        foreach ($mon as $num => $days)
        {
            $js .= "\r\n" . ($num-1) . ':[' . join(',', array_keys($days)) . '],';
        }
        return substr($js, 0, -1); // remove last comma
    }
    
    

}