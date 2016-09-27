
/**
 * Product:     Custom Product Preview
 * Package:     Aitoc_Aitcg_3.0.1_1.0.0_520274
 * Purchase ID: n/a
 * Generated:   2013-03-05 20:52:02
 * File path:   js/aitoc/aitcg/aitcg/main.js
 * Copyright:   (c) 2013 AITOC, Inc.
 */
Aitcg.Main = new Class.create();
Aitcg.Main.prototype =
{
    id: '',
    editorEnabled: false,
    window: null,
    cp: false,
    popupHtml:'',
    addImageUrl : '/aitcg/ajax/addImage',
        
    initialize : function(id) {
        this.id = id;
        this.imgThumbSelector = '#' +this.id + '_imagediv div.th';

        if(typeof(AitPopupHtml)!= 'undefined') {
            //$$('body')[0].insert( {bottom:AitPopupHtml} );
            //start  ie < 8.016 fix
            Event.observe(document, 'dom:loaded', function(){
                $$('body')[0].insert( {bottom:AitPopupHtml} );
            });
            //end ie < 8.016 fix
            
        }
    },

    getControlsHtml: function() 
    {
        return '<div style="cursor: pointer;" class="popup-separator popup-separator-title"' +
                ' onclick="toggleNextElement($(this));">' +
                '<span> + </span><span style="display:none"> - </span>';
    },
    
    getPopupHtmlIsPredefinedImage: function() 
    {
        popupHtml = '';
        if (this.usePredefinedImage)
            {
                popupHtml = 
                    this.getControlsHtml() +
                    '{{predefined_title}}</div>'+
                    '<span style="display:none;">' +
                    '<select onchange="opCimage{{rand}}.categoryPreview();" id="category-selector{{rand}}">' +
                    '<option value="0">Start Customizing Here...</option>' + 
                    this.predefinedOptions +
                    '</select>' +
                    '<button type="button" onclick="opCimage{{rand}}.addPredefined();">{{addimage_text}}</button>' +
                    '<div class="popup-separator"></div>' +
                    '<span id="predefined-images{{rand}}"></span>' +
                    '<div style="display:none;" id="add_predefined_{{rand}}_error" class="validation-advice">{{required_text}}</div></span>';
            }
            return popupHtml;
    },
    
    getPopupHtmlIsUserImage: function() 
    {
        popupHtml = '';
        if (this.useUserImage)
            {
                popupHtml+=
                    this.getControlsHtml() +
                    '{{user_title}}</div>' +
                    '<span style="display:none;"><input type="file" id="add_image_{{rand}}" name="new_image">' +
                    '<button type="button" onclick="opCimage{{rand}}.addImage(\'add_image_{{rand}}\');">{{addimage_text}}</button>'+
                    '<div style="display:none;" id="add_image_{{rand}}_error" class="validation-advice">{{required_text}}</div></span>';
            }
         return popupHtml;
    },
    
    getPopupHtmlIsText: function() 
    {
        popupHtml = '';
        if (this.useText)
        {
            // controls header
            popupHtml +=
                this.getControlsHtml() + '{{text_title}}</div>';

            // begin form
            popupHtml +=
                '<span style="display:none;"><form id="add_text_form{{rand}}">';

            // begin table
            popupHtml +=
                '<table class="form-list">';

            // text
            popupHtml +=
                '<tr>' +
                '<td class="label"><label for="add_text_{{rand}}">{{texttoadd_text}}</label></td>' +
                '<td class="value">' +
                '<input type="text" class="required-entry input-text" id="add_text_{{rand}}" name="text" value=""' +
                ' onkeyup="$(this.id).next().innerHTML = this.getValue().length;"' +
                (this.textLength ? (' maxlength="' + this.textLength + '"> <span>0</span>/' + this.textLength) :
                ('"> <span>0</span>')) +
                '</td>' +
                '</tr>';

            // font
            popupHtml +=
                '<tr>' +
                '<td class="label"><label for="font-selector{{rand}}">{{font_text}}</label></td>' +
                '<td class="value">' +
                '<select onchange="opCimage{{rand}}.fontPreview();" id="font-selector{{rand}}" name="font" class="required-entry select">{{fontOptions}}</select>' +
                '</td>' +
                '</tr>';

            // font preview
            popupHtml +=
                '<tr>' +
                '<td class="label"><label for="font-preview{{rand}}">{{fontpreview_text}}</label></td>' +
                '<td class="value">' +
                '<span><img id="font-preview{{rand}}" src="{{empty_img_url}}"></span>' + 
                '</td>' +
                '</tr>';

            
            popupHtml += this.getPopupHtmlIsColorpick();
            // end table
            popupHtml +=
                '</table>';

            // add text button
            popupHtml +=
                '<div class="form-buttons">' +
                '<button type="button" class="scalable add" onclick="opCimage{{rand}}.addText(\'add_text_{{rand}}\');">{{addtext_text}}</button>' +
                '</div>';

            // end form
            popupHtml +=
                '</form>' +
                '</span>';
        }
        
        return popupHtml;
    },
    
    getPopupHtmlIsColorpick: function() 
    {
        popupHtml = '';
        if (this.allowColorpick)
            {
                if(this.allowOnlyPredefColor)
                {
                    popupHtml +=
                    '<tr>' +
                    '<td class="label"><label for="font-selector{{rand}}">{{pickcolor_text}}</label></td>' +
                    '<td class="value">' +
                    '<input id="colorfield{{rand}}" class="jscolorpicker {pickerOnfocus:false}" readonly="readonly" name="color" value="#000000" style="width: 100px; background-color:#000000;">' +
                    '<div id="aitcg_colorset_container{{rand}}" class="aitcg_colorset_container" ></div>'+
                    '</td>' +
                    '</tr>';
                }
                else
                {
                    popupHtml +=
                    '<tr>' +
                    '<td class="label"><label for="font-selector{{rand}}">{{pickcolor_text}}</label></td>' +
                    '<td class="value">' +
                    '<input id="colorfield{{rand}}" name="color" class="jscolorpicker" value="#000000" style="width: 100px;">' +
                    '</td>' +
                    '</tr>';
                }

            }
        return popupHtml;
    },
    
    getPopupHtmlIsEditor: function() 
    {
        popupHtml = '';
        if (this.editorEnabled)
        {
            popupHtml +=
                '<a href="#" onclick="return false;" title="' + this.editorHelp + '" class="help2 tooltip-help">&nbsp;</a>';
        }

        popupHtml +=
            '</div><div class="aitclear"></div>';

        if (this.editorEnabled)
        {    
            popupHtml +=
                '<div class="message-popup-ait" style="text-align: left;">';
            
            popupHtml += this.getPopupHtmlIsPredefinedImage();
            popupHtml += this.getPopupHtmlIsUserImage();
            popupHtml += this.getPopupHtmlIsText();
            

            
        }
        return popupHtml;
    },
    getPopupTemplateControlPanel: function() 
    {
        popupHtml = '<div class="message-popup-head" id="qqq">' +
                '<div id="saveas-buttons">' +
                ((/MSIE ([0-7]+.\d+);/.test(navigator.userAgent) && (document.documentMode <=7))? '' : '' +
                '<form target="_blank" method="post" action="{{saveSvgUrl}}">'+
                    '<input type="hidden" name="data" value="" id="savedisc{{rand}}">'+
                    '<input type="hidden" name="type" value="" id="savedisc_type{{rand}}">'+
                    '{{scale_text}}'+
                    '<input type="text" name="print_scale" id="print_scale" value="1" className="validate-number" class="validate-number" size="1"> ' +
                    '<button title="'+this.buttonHelp+'" class="tooltip-help" onclick="saveAsSvg(opCimage{{rand}});">'+
                        '{{svg_text}}'+
                    '</button>'+
                '</form>'+
                ((Prototype.Browser.IE) ? '' : '<button type="button" onclick="opCimage{{rand}}.editor.e.unselect();var data = opCimage{{rand}}.getPrintableVersion($(\'print_scale\').getValue());opCimage{{rand}}.savePng(data);" title="'+this.buttonHelp+'" class="tooltip-help">{{png_text}}</button>'+
                '<canvas id="canvas{{rand}}" style="display:none;"></canvas>' +
                '<a id="canvas_link{{rand}}" style="display:none;" href="" target="_blank"></a>'))+
                '</div>'+

                (this.editorEnabled? '<a class="apply-but" href="#" onclick="opCimage{{rand}}.apply(); return false;" title="{{apply_text}}">{{apply_text}}</a>'+
                    '<a class="reset-but" href="#" onclick="opCimage{{rand}}.reset(); return false;" title="{{reset_text}}">{{reset_text}}</a>':'')
        return popupHtml;
    },
    getPopupTemplate: function() 
    {
        if (this.popupHtml == '')
        {
            
            var popupHtml = 
                '<div id="message-popup-window-mask" onclick="opCimage{{rand}}.closeEditor();"></div>' +
                '<div id="message-popup-window" class="message-popup print-area-editor">' +
                '<div class="message-popup-head">' +
                '<a href="#" onclick="opCimage{{rand}}.closeEditor(); return false;" title="{{close_text}}" class="close2">&nbsp;</a>';

            popupHtml += this.getPopupHtmlIsEditor();

            popupHtml += '<hr>';

            popupHtml +=
                '<div id="aitcg_popup_image_container" class="message-popup-ait" style="width:{{img_width}}px;height:{{img_height}}px;">' +
                '<img src="{{full_image}}" id="printable-area-image" width="{{img_width}}" height="{{img_height}}" alt="" />' +
                '<div id="{{option_id}}_raph" class="message-popup-aitraph" style="left:{{left}};top:{{top}};width:{{width}};height:{{height}};"></div>' +
                '</div> '+
                this.getPopupTemplateControlPanel() +
                '<script type="text/javascript">$$(".tooltip-help").each( function(link) {new Tooltip(link, {delay: 100, opacity: 1.0});});</script>'+
                '</div>'+
                '</div>';
            this.popupHtml = popupHtml;
        }
        else
            popupHtml = this.popupHtml;
        return popupHtml;
    },
    
    getTextPopupTemplate: function() {
        var str = '<div id="message-popup-window-mask" onclick="opCimage{{rand}}.agree(false);"></div>'+
                '<div id="message-popup-window" class="aitcgpopup">'+
                    '<div class="aitcgpopInner"><a class="close_btn" onclick="opCimage{{rand}}.agree(false); return false;" ></a></div>'+
                    '<div class="aitcgpop_text">{{text}}</div>'+
                    '<div>'+
                     '<a class="aitcgpop_btn" onclick="opCimage{{rand}}.agree(true); return false;"><div class="pop_btn_right"></div><p>{{agree_text}}</p></a>'+
                     '<a class="aitcgpop_btn" onclick="opCimage{{rand}}.agree(false); return false;"><div class="pop_btn_right"></div><p>{{disagree_text}}</p></a>'+
                    '</div>'+
                  '</div>';
        return str;
    },

    agree : function( data ) {
        $(this.id+'_checkbox').checked = data;
        this.closeEditor();
    },    
    
    closeEditor : function() {
        this.window.closeEditor( );
        this.window = null;
    },
    getArrayTemplateSetting : function()
    {
      return {
            full_image : this.productImageFullUrl,
            rand: this.rand,
            option_id: this.id,
            close_text: this.text.close,
            apply_text: this.text.apply,
            reset_text: this.text.reset,
            required_text: this.text.required,
            texttoadd_text: this.text.texttoadd,
            addtext_text: this.text.addtext,
            pickcolor_text: this.text.pickcolor,
            addimage_text: this.text.addimage,
            svg_text: this.text.svg,
            png_text: this.text.png,
            font_text: this.text.font,
            fontpreview_text: this.text.fontpreview,
            scale_text:this.text.scale,

            predefined_title: this.text.predefined_title,            
            user_title: this.text.user_title,            
            text_title: this.text.text_title,            
            
            areaSizeX: this.areaSizeX,
            areaSizeY: this.areaSizeY,
            areaOffsetX: this.areaOffsetX,
            areaOffsetY: this.areaOffsetY,
            fontOptions: this.fontOptions,
            saveSvgUrl: this.saveSvgUrl,
            empty_img_url: this.emptyImgUrl            
        }  
        
    },
    
    addPxToValue : function(arrays)
    {
        array_new = {};
        for(var item in array_new)
        {
            array_new[item] = arrays[item]+'px';

        }
        return array_new;
    },
    startEditor : function()
    {
       /* if (this.window != null)
        {
            this.closeEditor();
        }*/

        this.window = new Aitcg.Popup(this.getPopupTemplate(), this.getArrayTemplateSetting());
        
        var scr = this.window.showWindow( this.id, this.productImageSizeX, this.productImageSizeY, 1 );

        var optdata = {
            width  : Math.floor(this.areaSizeX * scr.mult),
            height : Math.floor(this.areaSizeY * scr.mult),
            left : Math.max(0, Math.round(this.areaOffsetX * scr.mult - 1)),
            top  : Math.max(0, Math.round(this.areaOffsetY * scr.mult - 1))
        };
        
       /* var options = {
            width  : optdata.width + 'px',
            height : optdata.height+ 'px',
            left :   optdata.left  + 'px',
            top  :   optdata.top   + 'px'
        };*/
        var options =this.addPxToValue(optdata);
        
        this.shownScr = scr;
        this.shownScr.opt = options;
        
        var el = $(this.id + '_raph');
        el.setStyle(options);
        this.editor = new Aitcg.Editor(el, optdata.width, optdata.height, this.textImgAspectRatio);
        this.editor.load($('options_' + this.optionId).getValue(), !this.editorEnabled, 1);

        jscolor.init();
        if(this.allowOnlyPredefColor)
        {
            eval('aitcgColorset'+this.rand+'.renderSet()');
        }
    },
    
    reset : function()
    {
        this.editor.e.deleteAll();
        this.editor.load($('options_'+this.optionId).getValue(),!this.editorEnabled,1);
    },

    apply : function()
    {
        var value = this.editor.save();

        $('options_'+this.optionId).setValue((value=='[]')?'':value);

        if((this.optionIsRequired=='0')&&(this.checkboxEnabled=='1'))
        {
            if($('options_'+this.optionId).getValue())
            {
                $('options_'+this.optionId+'_checkbox').addClassName('required-entry');
            }
            else
            {
                $('options_'+this.optionId+'_checkbox').removeClassName('required-entry');
            }
        }
        this.preview.e.deleteAll();
        this.preview.load($('options_'+this.optionId).getValue(),1,this.previewScale);
        opConfig.reloadPrice();
        this.closeEditor();
    },
    
    savePng : function(svg)
    {
        if (typeof(svg) == 'undefined') {
            var svg = $(this.id+'_raph').innerHTML;
        }
        svg = svg.replace(/>\s+/g, ">")
            .replace(/\s+</g, "<")
            .replace(/<svg/g,'<svg xmlns:xlink="http://www.w3.org/1999/xlink"');
            //only for ie (if will add convert to png support 
            //.replace(/xmlns="http:\/\/www.w3.org\/2000\/svg"/g,'');
        
        if(svg.match(/xlink\s*:\s*href\s*=/ig) == null && svg.match(/href\s*=/ig))
        {
            svg = svg.replace(/href\s*=/g,'xlink:href=');
        }   
        
        var c = $('canvas'+this.rand);
		c.width = this.areaSizeX;
		c.height = this.areaSizeY;
        var obj = this;        
        canvg(c, svg, { ignoreMouse: true, ignoreAnimation: true, renderCallback: function()
        {
            var img = $('canvas'+this.rand).toDataURL("image/png");
            $('canvas_link'+this.rand).href = img;
            obj.simulatedClick($('canvas_link'+this.rand), {type: 'click'});
        }.bind(this) });

    },
    
    simulatedClick: function (target, options) {

        var event = target.ownerDocument.createEvent('MouseEvents'),
            options = options || {};

        //Set your default options to the right of ||
        var opts = {
            type: options.type                  || 'click',
            canBubble:options.canBubble             || true,
            cancelable:options.cancelable           || true,
            view:options.view                       || target.ownerDocument.defaultView, 
            detail:options.detail                   || 1,
            screenX:options.screenX                 || 0, //The coordinates within the entire page
            screenY:options.screenY                 || 0,
            clientX:options.clientX                 || 0, //The coordinates within the viewport
            clientY:options.clientY                 || 0,
            ctrlKey:options.ctrlKey                 || false,
            altKey:options.altKey                   || false,
            shiftKey:options.shiftKey               || false,
            metaKey:options.metaKey                 || false, //I *think* 'meta' is 'Cmd/Apple' on Mac, and 'Windows key' on Win. Not sure, though!
            button:options.button                   || 0, //0 = left, 1 = middle, 2 = right
            relatedTarget:options.relatedTarget     || null
        }

        //Pass in the options
        event.initMouseEvent(
            opts.type,
            opts.canBubble,
            opts.cancelable,
            opts.view, 
            opts.detail,
            opts.screenX,
            opts.screenY,
            opts.clientX,
            opts.clientY,
            opts.ctrlKey,
            opts.altKey,
            opts.shiftKey,
            opts.metaKey,
            opts.button,
            opts.relatedTarget
        );

        //Fire the event
        target.dispatchEvent(event);
    },
    
    addImage : function(id) {
        if(!$(id).value)
        {   
            $('add_image_'+this.rand+'_error').show();
            return;
        }
        $('add_image_'+this.rand+'_error').hide();
        this.showLoader();
        AIM.upload(
        this.addImageUrl,
        id,
        {
            onComplete:this.editor.addUploadedImage,
            iinstance:this
        }
        );    
    
    },
    
    fontPreview : function()
    {
        if($('font-selector' + this.rand).getValue() > 0)
        {
            this.showLoader();
            new Ajax.Request(this.fontPreviewUrl,
            {
                method:'post',
                parameters: {font_id: $('font-selector'+this.rand).getValue(), rand: this.rand},
                onSuccess: function(transport){
                  var response = eval("("+transport.responseText+")");
                  $('font-preview'+response.rand).src = response.src;
                  this.hideLoader();                  
                }.bind(this)
            });
        }    
    },
    
    categoryPreview : function() {
        
        if($('category-selector'+this.rand).getValue()>0)
        {
            this.showLoader();
            new Ajax.Request(this.categoryPreviewUrl,
            {
                method:'post',
                parameters: {category_id: $('category-selector'+this.rand).getValue(), rand: this.rand},
                onSuccess: function(transport){
                  var response = eval("("+transport.responseText+")");
                  $('predefined-images'+response.rand).update(response.images);
                  this.hideLoader();                  
                }.bind(this)
            });
        }
        else        
        {
            $('predefined-images'+this.rand).update();
        }
    
    },    
    
    addText : function ()
    {
        var addTextForm = new VarienForm('add_text_form' + this.rand);

        if (!addTextForm.validator.validate())
        {
            return false;
        }

        this.showLoader();
        var params = $('add_text_form' + this.rand).serialize();
        new Ajax.Request(this.addTextUrl,
        {
            method:'post',
            parameters: params,
            onSuccess: function(transport){
              var response = eval("("+transport.responseText+")");
              this.editor.addUploadedText(response);
              this.hideLoader();
            }.bind(this)
        });
    
    },
    
    addPredefined : function() {
        $('add_predefined_'+this.rand+'_error').hide();
        //var radios = $$('input:checked[type="radio"][name="predefined-image'+this.rand+'"]');

        var selection = document.getElementsByName('predefined-image'+this.rand);

        for (i=0; i<selection.length; i++)

        if (selection[i].checked == true){
            var radios=selection[i];
        }
        
        if(typeof(radios)=='undefined')
        {   
            $('add_predefined_'+this.rand+'_error').show();
            return;
        }        
    
        var img_id = radios.getValue();
       
       this.showLoader();
        new Ajax.Request(this.addPredefinedUrl,
        {
            method:'post',
            parameters: {img_id: img_id},
            onSuccess: function(transport){
              var response = eval("("+transport.responseText+")");
              this.editor.addUploadedText(response.url);
              this.hideLoader();                  
            }.bind(this)
        }); 
        
    },        
    
    preview : function(elementId)
    {
        scale = this.calcScale();
        this.previewScale = scale;
        thumbEditorParams = {
            areaSizeX:parseInt(this.areaSizeX*scale),
            areaSizeY:parseInt(this.areaSizeY*scale),
            areaOffsetX:parseInt(this.areaOffsetX*scale),
            areaOffsetY:parseInt(this.areaOffsetY*scale)
            }
        html = '<img class="bg" src="'+this.productImageThumbnailUrl+'">'+
        '<div class="th"><div id="preview'+this.optionId+'" style="left: '+thumbEditorParams.areaOffsetX+'px; top: '+thumbEditorParams.areaOffsetY+'px; width: '+
        thumbEditorParams.areaSizeX+'px; height: '+thumbEditorParams.areaSizeY+'px;"></div></div>';
        document.getElementById(elementId).innerHTML = html;
        this.preview = new Aitcg.Editor( $('preview'+this.optionId), thumbEditorParams.areaSizeX, thumbEditorParams.areaSizeY, this.textImgAspectRatio);
        this.preview.load($('options_'+this.optionId).getValue(),1,scale);
    },
    
    calcScale : function()
    {
        return 1/Math.max(this.productImageSizeX/this.productImageThubnailSizeX,this.productImageSizeY/this.productImageThubnailSizeY);
    },

    showLoader : function() 
    {
        if( $('loading-mask') == null ) 
        {
            if(typeof(AitPopupHtml)!= 'undefined') {
                $$('body')[0].insert( {bottom:AitPopupHtml} );
            } else {
                document.body.appendChild( '<div id="loading-mask">Please wait...</div>' );
            }
        }
        $('loading-mask').show();
    },

    hideLoader : function() {
        $('loading-mask').hide();
    },   

    checkConfirmBox : function(el) {
        el = $(el).previous();
        if(typeof(this.text.confirm)=='undefined' || this.text.confirm=='') {
            return false;
        }
        this.window = new Aitcg.Popup(this.getTextPopupTemplate(true), {            
            full_image : this.productImageFullUrl,
            rand: this.rand,
            option_id: this.id,
            close_text: this.text.close,
            agree_text: this.text.agree,
            disagree_text: this.text.disagree,
            text: this.text.confirm
        } );
        var scr = this.window.showTextWindow( this.id );
        return false;
    },
    
    getPrintableVersion : function(scale) {
        var value = this.editor.save();
        if(value!=='[]')
        {
            $('options_'+this.optionId).setValue(value);
        }    
        scale = parseFloat(scale);
        if (scale == 0) {
            scale = 1;
        }
        var divForPrint = new Element('div');
        var printable = new Aitcg.Editor(divForPrint , this.areaSizeX*scale, this.areaSizeY*scale);
        printable.load($('options_' + this.optionId).getValue(),0,scale);
        return divForPrint.innerHTML;  
    }
};