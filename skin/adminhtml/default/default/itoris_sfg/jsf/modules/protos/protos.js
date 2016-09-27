document.prototypes = new Array();
document.presets = new Array();

SFG_PresetElement = function (obj) {
	this.protoName = obj.getAttribute('proto');
	this.data = new SFG_Prototype(obj);
}

SFG_Preset = function (obj) {
	this.name = obj.getAttribute('name');
	this.elements = new Array();
	var preview = obj.getElementsByTagName('preview')[0].childNodes;
	this.preview = '';
	for (var i=0; i<preview.length; i++) if (preview[i].tagName) {
		var text = preview[i].text ? preview[i].text.replace(/^\s+|\s+$/g,"") : preview[i].textContent ? preview[i].textContent.replace(/^\s+|\s+$/g,"") : '';
		this.preview += '<'+preview[i].tagName;
		var style = preview[i].getAttribute('style');
		if (style) this.preview += ' style="'+style+'"';
		var type = preview[i].getAttribute('type');
		if (type) this.preview += ' type="'+type+'"';
		var src = preview[i].getAttribute('src');
		if (src) this.preview += ' src="'+src+'"';
		this.preview += '>'+text+'</'+preview[i].tagName+'>';
	}
	this.preview = this.preview.replace('{captcha0}', document.sfgSkin+'jsf/images/alikon-captcha.png');
	this.preview = this.preview.replace('{live_site}', document.siteURL);
	this.preview = this.preview.replace('{sfg_files}', document.sfgFiles);
	this.preview = this.preview.replace('{sfg_skin}', document.sfgSkin);
	var presets = obj.getElementsByTagName('elm');
	for (var i=0; i<presets.length; i++) {
		this.elements[this.elements.length] = new SFG_PresetElement(presets[i]);
	}
}

SFG_Prototype = function (obj) {
	this.name = obj.getAttribute('name');
	this.tag = obj.getAttribute('tag');
	this.attributes = new SFG_ArrtibutesList (obj.getElementsByTagName('attributes')[0]);
	this.events = new SFG_ArrtibutesList (obj.getElementsByTagName('events')[0]);
	this.styles = new SFG_ArrtibutesList (obj.getElementsByTagName('styles')[0]);
	this.params = new SFG_ArrtibutesList (obj.getElementsByTagName('params')[0]);
	this.content = obj.getElementsByTagName('content')[0];
	this.contentPHP = obj.getElementsByTagName('contentPHP')[0];
	if (this.content) this.content = this.content.text ? this.content.text.replace(/^\s+|\s+$/g,"") : this.content.textContent.replace(/^\s+|\s+$/g,"");
}

SFG_ArrtibutesList = function (obj) {
	var list = Array();
	if (!obj) {
		return list;
	}

	for (var i=0; i<obj.childNodes.length; i++) {
		var tag = obj.childNodes[i].tagName;
		var text = obj.childNodes[i].text
				? obj.childNodes[i].text.replace(/^\s+|\s+$/g,"")
				: obj.childNodes[i].textContent.replace(/^\s+|\s+$/g,"");
		text = text.replace('{live_site}', document.siteURL);
		text = text.replace('{sfg_files}', document.sfgFiles);
		text = text.replace('{sfg_skin}', document.sfgSkin);
		if (text == 'true')	{
			text = true;
		}

		if (tag && text) {
			list[list.length] = Array(tag, text, '');
		}
	}

	return list;
}

function protoMouseDown(ev) {
	if (window.event) obj=window.event.srcElement; else obj=ev.currentTarget;
	stopEvent(ev);
	deselectAll();
	setStuckPoints();
	var id = parseInt(obj.id.substr(5));
	document.tmpObject = new SFG_Element(document.prototypes[id]);
	document.tmpObject.object.style.left = mouse['x'] - 5 + 'px';
	document.tmpObject.object.style.top = mouse['y'] - 5 + 'px';
	addEvent(document.tmpObject.object, 'mouseup', protoMouseUp);
	document.body.appendChild(document.tmpObject.object);
	hideList();
}

function protoMouseUp(ev) {
	document.body.removeChild(document.tmpObject.object);
	var left = parseInt(document.tmpObject.object.style.left) - document.mostLeftScrolled;
	var top = parseInt(document.tmpObject.object.style.top) - document.mostTopScrolled;
	var cnt = document.allElements.length;
	document.tmpObject.updateProp(document.tmpObject.styles,'left', left + 'px', document.tmpObject.getPHP(document.tmpObject.styles, 'left')) ;
	document.tmpObject.updateProp(document.tmpObject.styles,'top', top + 'px', document.tmpObject.getPHP(document.tmpObject.styles, 'top')) ;
	document.allElements[cnt] = document.tmpObject.clone();
	document.allElements[cnt].select();
	document.tmpObject = null;
	document.getElementById('sfg_inner_container').appendChild(document.allElements[cnt].object);
	document.currentPageElements[document.currentPageElements.length] = document.allElements[cnt];
	setStuckPoints();
	showProps();
	showAllElementsList();	
}

function showPresetList(ev) {
	document.overPresets = true;
	document.body.appendChild(document.presetList);
	document.presetList.style.left = document.mostLeft + 'px';
	document.presetList.style.top = document.mostTop + 'px';
	document.presetList.style.height = document.getElementById('sfg_container').offsetHeight - 20 + 'px';
	document.getElementById('presetsData').style.height = document.presetList.offsetHeight - 20 + 'px';
}

function presetsOver(ev) {
	document.overPresets = true;
}

function presetsOut(ev) {
	document.overPresets = false;
	setTimeout('closePresetsList()',500);
}

function closePresetsList() {
	if (!document.overPresets && document.getElementById('presetList')) document.body.removeChild(document.getElementById('presetList'));
}

function presetMouseDown(ev) {
	if (window.event) obj=window.event.srcElement; else obj=ev.currentTarget;
	while (obj.tagName.toLowerCase() != 'div' && obj.id.indexOf('preset')==-1) obj = obj.parentNode;
	stopEvent(ev);
	deselectAll();
	setStuckPoints();
	var id = parseInt(obj.id.substr(6));
	document.overPresets = false;
	closePresetsList();
	for (q=0; q<document.presets[id].elements.length; q++) {
		var p = -1;
		for (j=0; j<document.prototypes.length; j++) if (document.presets[id].elements[q].protoName == document.prototypes[j].name) {p=j;break;}
		if (p==-1) continue;
		var tmp = new SFG_Element(document.prototypes[p]);
		document.allElements[document.allElements.length] = tmp;
		document.currentPageElements[document.currentPageElements.length] = tmp;
		document.getElementById('sfg_inner_container').appendChild(tmp.object);
		tmp.select();
		tmp.page = document.currentPage;
		for (w=0; w<document.presets[id].elements[q].data.styles.length; w++) tmp.updateProp(tmp.styles,document.presets[id].elements[q].data.styles[w][0],document.presets[id].elements[q].data.styles[w][1],tmp.getPHP(tmp.styles,document.presets[id].elements[q].data.styles[w][0]));
		for (w=0; w<document.presets[id].elements[q].data.attributes.length; w++) tmp.updateProp(tmp.attributes,document.presets[id].elements[q].data.attributes[w][0],document.presets[id].elements[q].data.attributes[w][1],tmp.getPHP(tmp.attributes,document.presets[id].elements[q].data.attributes[w][0]));
		for (w=0; w<document.presets[id].elements[q].data.events.length; w++) tmp.updateProp(tmp.events,document.presets[id].elements[q].data.events[w][0],document.presets[id].elements[q].data.events[w][1],tmp.getPHP(tmp.events,document.presets[id].elements[q].data.events[w][0]));
		for (w=0; w<document.presets[id].elements[q].data.params.length; w++) tmp.updateProp(tmp.params,document.presets[id].elements[q].data.params[w][0],document.presets[id].elements[q].data.params[w][1],tmp.getPHP(tmp.params,document.presets[id].elements[q].data.params[w][0]));
		if (document.presets[id].elements[q].data.content) tmp.content = document.presets[id].elements[q].data.content;
		tmp.updateStyles();
		tmp.updateAttributes();
		if (tmp.content) tmp.applyContent();
		document.deltaLeft = 5;
		document.deltaTop = 5;
		if (tmp.getProp(document.presets[id].elements[q].data.styles,'left')=='0px' && tmp.getProp(document.presets[id].elements[q].data.styles,'top')=='0px') document.drag = tmp;
	}
	//areaMove(ev);
}

// add preset menu icon
var img = document.createElement('img');
img.className = 'prototype';
img.src = document.sfgSkin+'jsf/modules/protos/presets.png';
addEvent(img,'dragstart',stopEvent);
addEvent(img,'mouseover',showPresetList);
addEvent(img,'mouseout',presetsOut);
document.getElementById('sfg_elements_bar').appendChild(img);


var protoXML = loadXMLFile(document.sfgSkin+'jsf/modules/protos/protos.xml');
var items = protoXML.documentElement.getElementsByTagName('preset');

for (var i=0; i<items.length; i++) {
	document.presets[document.presets.length] = new SFG_Preset(items[i]);
}

var items = protoXML.documentElement.getElementsByTagName('element');
for (var i=0; i<items.length; i++) {
	document.prototypes[document.prototypes.length] = new SFG_Prototype (items[i]);
	var img = document.createElement('img');
	img.className = 'prototype';
	img.id = 'proto'+i;
	img.src = document.sfgSkin+'jsf/modules/protos/' + items[i].getAttribute('image');
	img.title = '<b>'+lang['Element']+': </b><b style="color:red">'+items[i].getAttribute('name')+'</b><br /><b>'+lang['HTML Tag']+': </b><b style="color:blue">'+items[i].getAttribute('tag')+'</b>';
	var proto = document.prototypes[document.prototypes.length-1];
	if (proto.attributes.length>0) {
		img.title += '<br /><b>'+lang['Attributes']+':</b>';
		for(j=0; j<proto.attributes.length; j++) img.title += '<br /><b style="color:blue; margin-left:10px">' + proto.attributes[j][0]+'="'+proto.attributes[j][1] + '"</b>';
	}
	if (proto.styles.length>0) {
		img.title += '<br /><b>'+lang['Styles']+':</b><br /><b style="color:blue; margin-left:10px">';
		for(j=0; j<proto.styles.length; j++) img.title += proto.styles[j][0]+': '+proto.styles[j][1] + '; ';
		img.title += '</b>';
	}
	addEvent(img,'dragstart',stopEvent);
	addEvent(img,'mousedown',protoMouseDown);
	addEvent(img,'mousemove',showHint);
	addEvent(img,'mouseout',hideHint);

	var img_div = document.createElement('div');
	img_div.className = 'prototype_cont';
	img_div.appendChild(img);

	document.getElementById('sfg_elements_bar').appendChild(img_div);
}


document.presetList = document.createElement('div');
document.presetList.id = 'presetList';
addEvent(document.presetList,'mousemove',updateMouse);
var div = document.createElement('div');
div.id = 'propertiesHeader';
div.innerHTML = '<div style="float:left">'+getTranslation('Snippets')+'</div>';
addEvent(document.presetList,'mouseover',presetsOver);
addEvent(document.presetList,'mouseout',presetsOut);
document.presetList.appendChild(div);
var div = document.createElement('div');
div.id = 'presetsData';
document.presetList.appendChild(div);
for (i=0; i<document.presets.length; i++) {
	var div2 = document.createElement('div');
	div2.id = 'preset'+i;
	div2.innerHTML = '<center><b style="color:red">'+document.presets[i].name+'</b></center>'+document.presets[i].preview;
	div.appendChild(div2);
	addEvent(div2,'mousedown', presetMouseDown);
	var imgs = div2.getElementsByTagName('img');
	for (var j=0; j<imgs.length; j++) {
		addEvent(imgs[j],'drag',stopEvent);
	}
}
