<?php

class Indies_Partialpayment_Block_Adminhtml_Widget_Form_Element_Dependence extends Mage_Adminhtml_Block_Widget_Form_Element_Dependence
{
	public function addFieldDependence($fieldName, $fieldNameFrom, $refValues)
	{
		$this->_depends[$fieldName][$fieldNameFrom] = $refValues;
		return $this;
	}
}