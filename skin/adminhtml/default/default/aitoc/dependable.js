
/**
 * Custom Options Templates
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitoptionstemplate
 * @version      3.2.2
 * @license:     n/a
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
var aitdependable = {     
    _config: {},
    lastRowId: 0,
    div: null,
    ifFlag: 1,
    templateSyntax : /(^|.|\r|\n)({{(\w+)}})/,    
    templateOptionId: 0,
    templateRowsMap: {},
    templateChilds: {},
    fields: {
        th_id:      '<th style="width:15px">ID</th>',
        th_children:'<th>Children</th>',
        id:         '<td>{{row_id}}<input type="hidden" name="product[options][{{option_id}}][row_id]" value="{{row_id}}"></td>',
        select_id:  '<td>{{row_id}}<input type="hidden" name="product[options][{{option_id}}][values][{{select_id}}][row_id]" value="{{row_id}}"></td>',
        children:   '<td>'+
                        '<input class="input-text select-type-children" type="text" name="product[options][{{option_id}}][values][{{select_id}}][children]" id="option_{{option_id}}_{{select_id}}_children" value="{{children}}">&nbsp;'+ 
                        '<div id="option_{{option_id}}_{{select_id}}_children_advice"></div>'+
                     '</td>'
    },
    
    init: function(config)
    {
        if(typeof(config) != 'undefined') {
            this._config = config.data ? config.data : {};
            this.lastRowId = parseInt( config.last_row ? config.last_row : 0 );
        }
    },
    parse:function(el) {
        if(!el.id) {
            return false;
        }
        if(el.id == 'product_options_container_top') {
            this.updateContainer(el);
            return true;
        }
        match = el.id.match(/^select_option_type_row_(\d+)$/);
        if(match && match[1]) {
            this.updateSelectRow(el, match[1], match[1]);
            return true;
        }
        match = el.id.match(/^select_option_type_row_aitocoption(\d+)/);//example: select_option_type_row_aitocoption1-12345
        //if(this.templateOptionId && match && match[1]) {
        if(match && match[1]) {
            match2 = el.id.match(/^select_option_type_row_aitocoption(\d+\-\d+)$/);//example: select_option_type_row_aitocoption1-12345
            if(match2 && match2[1]) {
                this.updateSelectRow(el, 'aitocoption' + match[1], 'aitocoption' + match2[1]);
                return true;
            }
        }
    },
    getTemplate: function(name, data)
    {
        if(name == '' || typeof(this.fields[name]) == 'undefined') {
            return '';
        }
        var template = new Template(this.fields[name], this.templateSyntax);
        return template.evaluate(data);
    },
    
    getConfig: function(id, type, element)
    {
        var ret = '';
        if(id<=0 || type<0 || typeof(this._config[id])=='undefined' || typeof(this._config[id][type]) == 'undefined') {
            if(element == 'row_id') {
                ret = ++this.lastRowId;
            }
        } else {
            ret = this._config[id][type][element];
            if(this.templateOptionId) {
                if(element == 'row_id') {
                    newId = ++this.lastRowId;
                    this.storeOptionMap(ret, newId);
                    ret = newId;
                } else if(element == 'child_rows'){
                    if(typeof(arguments[3])!='undefined') {
                        id = arguments[3];
                    }
                    this.storeChildToUpdate(id, type, ret);
                    ret = '';
                }
            }
        }        
        return ret;
    },
    
    updateSelectRow: function(parent_el, option_id, rand_id)
    {
        var id, select_id;
        var els = parent_el.select('tr');
        el = els.last();
        var td = el.down('td');
        var input = el.down('input');
        if(input.name.match(/option_type_id/)) {
            id = input.value; 
            select_id = id
            if( select_id== -1) {
                match = el.id.match(/^product_option_([\w\d\-]+)_select_(\d+)$/)
                select_id = match[2];
            }
        }
        var row_id = this.getConfig(option_id, id, 'row_id');
        Element.insert(td, {before: this.getTemplate('select_id', {option_id: rand_id, select_id:select_id ,row_id:row_id,visual_id: Math.abs(row_id)}) });
        Element.insert(el.select('td').last(), {before: this.getTemplate('children', {option_id: rand_id, select_id:select_id ,row_id:row_id,visual_id: Math.abs(row_id), children:this.getConfig(option_id, id, 'child_rows', rand_id)}) });
        //      tbody  thead 
        el = parent_el.previous(0);
        if(!el.down('th').idAdded) {
            Element.insert(el.down('th'), {before:this.getTemplate('th_id',{})});
            Element.insert(el.select('th').last(), {before:this.getTemplate('th_children',{})});
            el.down('th').idAdded = true;
        }
    },
    
    updateContainer: function(el)
    {
        if(!this.div) {
            this.div = $('product_options_container_top');
        }
        var el = this.div.next(0),
            option_id, id;
        var match = el.id.match(/^option_(\d+)$/);
        if(!match) {
            //match = el.id.match(/^option_aitocoption(\d+)$/);
            match = el.id.match(/^option_aitocoption(\d+\-\d+)$/);
            if(!match) {
                return false;                
            }
            option_id = 'aitocoption'+match[1];
            id = this.getConfig(this.templateOptionId, 0, 'row_id');
        } else {
            option_id = match[1];
            id = this.getConfig(option_id, 0, 'row_id');
        }
        Element.insert(el.down('th.opt-title'), {before:this.getTemplate('th_id',{})});
        Element.insert(el.down('td'), {before: this.getTemplate('id', {option_id: option_id, row_id:id, visual_id: Math.abs(id)}) });
    },
    
    setTemplateOptionId: function(id)
    {
        this.templateOptionId = id;
    },
    
    startTemplateImport: function()
    {
        this.templateRowsMap = {};
        this.templateChilds = {};        
    },
    
    endTemplateImport: function()
    {
        for(var id in this.templateChilds) {
            var childs = this.templateChilds[id].split(',');
            var newChilds = [];
            for(var i=0; i<childs.length; i++) {
                newChilds.push( this.templateRowsMap[ childs[i] ] );
            }
            $(id).value = newChilds.join(',');
        }
        //disabling 'aplying template' mode
        this.setTemplateOptionId(0);
    },
    
    storeOptionMap: function(oldRowId, newRowId)
    {
        this.templateRowsMap[oldRowId] = newRowId;
    },
    
    storeChildToUpdate: function(option_id, type_id, childValues)
    {
        this.templateChilds['option_'+option_id + '_' + type_id + '_children'] = childValues;
    }   
    
};

Element.aitDependant = Element.insert;
Element.insert = function(el, insertion)
{
    this.aitDependant(el, insertion);    
    aitdependable.parse(el);
}

function saveWithIndexRebuild()
{
    /*we change all names of inputs and selects so that they do not recur*/        
    if (product_info_tabsJsTabs.activeTab.id == 'product_info_tabs_customer_options')
    {
        var aRows = $$('[id*="select_option_type_row_"] tr');
        for (i=0; i < aRows.length; i++)
        {
            var aInputs = aRows[i].select('input', 'select');
            for(j=0; j < aInputs.length; j++)
            {
                var sName = aInputs[j].name;
                var sBegin = sName.indexOf('values');
                var sEnd = sName.indexOf(']', sBegin+8);
                var sNewName = sName.substring(0, sBegin+8) + i + sName.substring(sEnd);
                aInputs[j].name = sNewName;
            } 
        }   
    }
    
    productForm.submit();
}

function rebuildSaveAndContinueEdit(urlTemplate)
{
    if (product_info_tabsJsTabs.activeTab.id == 'product_info_tabs_customer_options')
    {
        var aRows = $$('[id*="select_option_type_row_"] tr');
        for (i=0; i < aRows.length; i++)
        {
            var aInputs = aRows[i].select('input', 'select');
            for(j=0; j < aInputs.length; j++)
            {
                var sName = aInputs[j].name;
                var sBegin = sName.indexOf('values');
                var sEnd = sName.indexOf(']', sBegin+8);
                var sNewName = sName.substring(0, sBegin+8) + i + sName.substring(sEnd);
                aInputs[j].name = sNewName;
            } 
        }   
    }

    var template = new Template(urlTemplate, productTemplateSyntax);
    var url = template.evaluate({tab_id:product_info_tabsJsTabs.activeTab.id});
    productForm.submit(url);    
}