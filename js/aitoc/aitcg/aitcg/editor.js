
/**
 * Product:     Custom Product Preview
 * Package:     Aitoc_Aitcg_3.0.1_1.0.0_520274
 * Purchase ID: n/a
 * Generated:   2013-03-05 20:52:02
 * File path:   js/aitoc/aitcg/aitcg/editor.js
 * Copyright:   (c) 2013 AITOC, Inc.
 */
Aitcg.Editor = new Class.create();
Aitcg.Editor.prototype =
{
    e : null, //editor
    attr : "cx,cy,fill,fill-opacity,font,font-family,font-size,font-weight,gradient,height,opacity,path,r,rotation,rx,ry,src,stroke,stroke-dasharray,stroke-opacity,stroke-width,width,x,y,text,preserveAspectRatio".split(","),
    socialWidgetsReservedImgId: 0,

    initialize : function(el, sizeX, sizeY, imgAspectRatio)
    {
        this.e = new VectorEditor(el, sizeX, sizeY);
        this.sizeX = sizeX;
        this.sizeY = sizeY;
        this.imgAspectRatio = imgAspectRatio; 
        //this.demo();
    },
    
    addUploadedImage: function(url){
        if(!url.error)
        {
            var img = /*new Image();*/$$('.techimg')[0];
            img.onload = function(e){
                    this.iinstance.editor.addImage(getEventTarget(e));
                    this.iinstance.hideLoader();
                }.bind(this);
            img.src = url.src;    
        }
        else
        {   
            this.iinstance.hideLoader();
            alert(url.error);
        }
    },
    
    addUploadedText: function(url){
        
        var img = /*new Image();//*/$$('.techimg')[0];
        img.onload = function(e){this.addImage(getEventTarget(e));}.bind(this);
        img.src = url;    
        
    },    
    
    addImage : function(img){
        var scale = 1;
        if((img.getWidth()>(this.sizeX-40))||(img.getHeight()>(this.sizeY-40)))
        {
            scale = Math.min((this.sizeX-40)/img.getWidth(),(this.sizeY-40)/img.getHeight());
        }

        switch(Raphael.type)
        {
            case 'SVG':
                newshape = this.e.draw.image(img.src, 20, 20, img.getWidth()*scale, img.getHeight()*scale, this.imgAspectRatio);
                break;
            case 'VML':
                newshape = this.e.draw.image(img.src, 20, 20, img.getWidth()*scale, img.getHeight()*scale, this.imgAspectRatio);
//                this.e.on('addedshape', function(a,b,c,d){
//                    return;
//                });
                break;
            default :
                document.write("Error: undefined image type"); 
        }
        
        this.e.addShape(newshape,true);
    },
    
    load: function(data, noattachlistener, scale)
    {
        if (data != '')
        {
            try {
                var json = eval("("+data+")");
                $(json).each(function(item) {
                    this.loadShape(item,noattachlistener, scale);
                }.bind(this));
            } catch(err) {
                alert(err.message)
            }
        }
    },
    
    loadShape : function(shape, noattachlistener, scale){
        var instance = this.e;
        if(!shape || !shape.type || !shape.id)return;
        
        var newshape = null, draw = instance.draw;
        if (!(newshape = this.e.getShapeById(shape.id))) {
            switch  (shape.type ) 
            {
                case 'rect':
                {
                    newshape = draw.rect(0, 0, 0, 0);
                    break;
                }
                case "path":
                {
                    newshape = draw.path("");
                    break;
                }
                case "image":
                {
                    newshape = draw.image(shape.src, 0, 0, 0, 0);
                    break;
                }
                case  "ellipse":
                {
                    newshape = draw.ellipse(0, 0, 0, 0);
                    break;
                }
                case "text":
                {
                    newshape = draw.text(0, 0, shape.text);
                    break;
                }
            }
        }

        if (scale != 1)
        {
            shape.x = shape.x*scale;
            shape.y = shape.y*scale;
            shape.height = shape.height*scale;
            shape.width = shape.width*scale;
        }
        
        newshape.attr(shape);
        newshape.id = shape.id;
        newshape.subtype = shape.subtype;
    
        if (!noattachlistener) {
            instance.addShape(newshape,true);
        }
    },
    
    save : function()
    {
        return Object.toJSON($(this.e.shapes).collect(this.dumpshape.bind(this)));
    },

    dumpshape : function(shape){
        var info = {
          type: shape.type,
          id: shape.id,
          subtype: shape.subtype,
          social_widgets_reserved_img_id: this.socialWidgetsReservedImgId,//social widgets functionality
         }
        //fix for ie
        info.id++;
        for(var i = 0; i < this.attr.length; i++){
          var tmp = shape.attr(this.attr[i]);
          if(tmp){
            if(this.attr[i] == "path"){
              tmp = tmp.toString()
            }
            info[this.attr[i]] = tmp
          }
        }
        return info
    }
    
};