<?php

class Indies_Partialpaymentadmin_Lib_Varien_Data_Form_Element_ExtendedLabel extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml()
    {	
        $html = Mage::app()->getLayout()->createBlock('adminhtml/sales_order_create_billing_method_form')
					->setTemplate('partialpaymentadmin/installment.phtml')
					->toHtml();
        return $html;
    }

    public function getLabelHtml($idSuffix = '') {
        if (!is_null($this->getLabel())) {
            $html = '<label for="'.$this->getHtmlId() . $idSuffix . '" style="'.$this->getLabelStyle().'">'.$this->getLabel()
                . ( $this->getRequired() ? ' <span class="required">*</span>' : '' ).'</label>'."\n";
        }
        else {
            $html = '';
        }
        return $html;
    }
}
