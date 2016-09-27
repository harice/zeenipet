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
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitoptionstemplate_Block_Template_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('aitoptionstemplate_template_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('aitoptionstemplate')->__('Template'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('aitoptionstemplate')->__('General'),
            'content'   => $this->getLayout()->createBlock('aitoptionstemplate/template_edit_tab_general')->toHtml(),
            'active'    => true,
        ));
        
        $this->addTab('options_section', array(
            'label' => Mage::helper('catalog')->__('Options'),
            'url'   => $this->getUrl('adminhtml/catalog_product/options', array('_current' => true, 'id' => Mage::getStoreConfig('general/aitoptionstemplate/default_product_id'), 'aitflag'=> 1)),
            'class' => 'ajax',
        ));

        $this->addTab('related', array(
            'label'     => Mage::helper('catalog')->__('Assigned Products'),
            'url'       => $this->getUrl('*/*/related', array('_current' => true)),
            'class'     => 'ajax',
        ));
        
        
        return parent::_beforeToHtml();
    }

    protected function _toHtml()
    {
        $sContent = parent::_toHtml();
        
    	$model = Mage::registry('current_aitoptionstemplate_template');
    	
        if ($model->getId()) 
        {        
            $oBlock = $this->getLayout()->createBlock('adminhtml/store_switcher');
            $sContent = $oBlock->toHtml() . $sContent;
        }
        
        $sContent .= '
        
<script type="text/javascript">
//<![CDATA[        
        
    var productLinksController = Class.create();

    productLinksController.prototype = {
        initialize : function(fieldId, products, grid) {
            this.saveField = $(fieldId);
            this.saveFieldId = fieldId;
            this.products    = $H(products);
            this.grid        = grid;
            this.tabIndex    = 1000;
            this.grid.rowClickCallback = this.rowClick.bind(this);
            this.grid.initRowCallback = this.rowInit.bind(this);
            this.grid.checkboxCheckCallback = this.registerProduct.bind(this);
            this.grid.rows.each(this.eachRow.bind(this));
            this.saveField.value = this.serializeObject(this.products);
            this.grid.reloadParams = {"products[]":this.products.keys()};
        },
        eachRow : function(row) {
            this.rowInit(this.grid, row);
        },
        registerProduct : function(grid, element, checked) {
            if(checked){
                if(element.inputElements) {
                    this.products.set(element.value, {});
                    for(var i = 0; i < element.inputElements.length; i++) {
                        element.inputElements[i].disabled = false;
                        this.products.get(element.value)[element.inputElements[i].name] = element.inputElements[i].value;
                    }
                }
            }
            else{
                if(element.inputElements){
                    for(var i = 0; i < element.inputElements.length; i++) {
                        element.inputElements[i].disabled = true;
                    }
                }

                this.products.unset(element.value);
            }
            this.saveField.value = this.serializeObject(this.products);
            this.grid.reloadParams = {"products[]":this.products.keys()};
        },
        serializeObject : function(hash) {
            var clone = hash.clone();
            clone.each(function(pair) {
                clone.set(pair.key, encode_base64(Object.toQueryString(pair.value)));
            });
            return clone.toQueryString();
        },
        rowClick : function(grid, event) {
            var trElement = Event.findElement(event, "tr");
            var isInput   = Event.element(event).tagName == "INPUT";
            if(trElement){
                var checkbox = Element.select(trElement, "input");
                if(checkbox[0]){
                    var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    this.grid.setCheckboxChecked(checkbox[0], checked);
                }
            }
        },
        inputChange : function(event) {
            var element = Event.element(event);
            if(element && element.checkboxElement && element.checkboxElement.checked){
                this.products.get(element.checkboxElement.value)[element.name] = element.value;
                this.saveField.value = this.serializeObject(this.products);
            }
        },
        rowInit : function(grid, row) {
            var checkbox = $(row).select(".checkbox")[0];
            var inputs = $(row).select(".input-text");
            if(checkbox && inputs.length > 0) {
                checkbox.inputElements = inputs;
                for(var i = 0; i < inputs.length; i++) {
                    inputs[i].checkboxElement = checkbox;
                    if(this.products.get(checkbox.value) && this.products.get(checkbox.value)[inputs[i].name]) {
                        inputs[i].value = this.products.get(checkbox.value)[inputs[i].name];
                    }
                    inputs[i].disabled = !checkbox.checked;
                    inputs[i].tabIndex = this.tabIndex++;
                    Event.observe(inputs[i],"keyup", this.inputChange.bind(this));
                    Event.observe(inputs[i],"change", this.inputChange.bind(this));
                }
            }
        }
    };        
//]]>
</script>        
        ';
        
        return $sContent;
    }
    
}