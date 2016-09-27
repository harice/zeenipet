function moveElementsDown(obj, px) {
	var div = document.getElementById('sfg_fieldset');
	if (!obj || !div) return;
	if (obj.tagName) {
		var Etop = parseInt(obj.style.top);
		var nodes = div.childNodes;
		var objects = Array();
		var objs = Array();
		for (i=0; i<nodes.length; i++) if (nodes[i].tagName) objects[objects.length] = nodes[i];
		for (i=0; i<objects.length; i++) if (parseInt(objects[i].style.top) >= Etop) {
			objs[objs.length] = objects[i];
			objects[i].style.top = parseInt(objects[i].style.top) + px + 'px';
		}
		return objs;
	} else {
		for (i=0; i<obj.length; i++) obj[i].style.top = parseInt(obj[i].style.top) + px + 'px';
	}
}

function moveElementsUp(obj, px) {
	var div = document.getElementById('sfg_fieldset');
	if (!obj || !div) return;
	if (obj.tagName) {
		var Etop = parseInt(obj.style.top);
		var nodes = div.childNodes;
		var objects = Array();
		var objs = Array();
		for (i=0; i<nodes.length; i++) if (nodes[i].tagName) objects[objects.length] = nodes[i];
		for (i=0; i<objects.length; i++) if (parseInt(objects[i].style.top) >= Etop) {
			objs[objs.length] = objects[i];
			objects[i].style.top = parseInt(objects[i].style.top) - px + 'px';
		}
		return objs;
	} else {
		for (i=0; i<obj.length; i++) obj[i].style.top = parseInt(obj[i].style.top) - px + 'px';
	}
}

function getElementByName(rt, nm) {
	var form = rt;
	while (form && form.tagName && form.tagName.toLowerCase() != 'form') form = form.parentNode;
	var name = nm.toLowerCase();
	var tags = Array('input', 'select', 'textarea', 'img');
	for (i=0; i<tags.length; i++) {
		var tmp = form.getElementsByTagName(tags[i]);
		for (o=0; o<tmp.length; o++) if (tmp[o].name && tmp[o].name.toLowerCase() == name) return tmp[o];
	}
}

function simpleSubmit(rt, submitter) {
	var form = rt;
	while (form && form.tagName && form.tagName.toLowerCase() != 'form') form = form.parentNode;
	getElementByName(rt, 'sfg_submitter').value = submitter;
	form.submit();
}

function HTTPRequest(url, async, func) {
	var xmlhttp;
	try{ xmlhttp = new XMLHttpRequest(); }
	catch (e) {
		try{ xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");	}
		catch (e) {
			try{ xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }
			catch (e) { alert(e.message); return; }
		}
	}
	xmlhttp.open("GET", url , async);
	if (async == true) {
		xmlhttp.onreadystatechange= function() {
			if (xmlhttp.readyState==4) {
				if (window[func]) window[func](xmlhttp);
			}
		}
	}
	xmlhttp.send(null);
	if (async == false) {
		return xmlhttp;
	}
}

function sessionKeeper() {
	var url = document.location.href;
	if (url.indexOf('?') > -1) url += '&subtask=keepsession';
	else if (url.toLowerCase().substr(url.length-4, 4) == '.php' || url.toLowerCase().substr(url.length-4, 4) == '.htm' || url.toLowerCase().substr(url.length-5, 5) == '.html') url += '?subtask=keepsession';
	else url += '/?subtask=keepsession';
	url += '&rand='+Math.random();
	HTTPRequest(url, true, null);
	setTimeout('sessionKeeper()', 600000);
}

// ping server each 10 minutes to keep the form session
setTimeout('sessionKeeper()', 600000);

function captchaReload(rt, name) {
	var obj = getElementByName(rt, name);
	if (!obj) return;
	var src = obj.src;
	if (src) {
		var pos = src.indexOf('&action=reload');
		if (pos > -1) src = src.substring(0, pos);
		src += '&action=reload&rnd='+Math.random().toString().replace('.','');;
		obj.src = src;
	}
}

var previewWin;

function showPreview(rt, formid) {
	var form = rt;
	while (form && form.tagName && form.tagName.toLowerCase() != 'form') form = form.parentNode;
	eval('var url = SFG_RESOURCE_PREVIEW_URL_' + formid);
	var xml = HTTPRequest(url, false, null);
	var cnt = xml.responseText;
	var elms = cnt.split('|||');
	for (i=0; i<elms.length; i++) elms[i] = elms[i].split('^|^');
	var tags = Array('input', 'select', 'textarea');
	for (i=0; i<tags.length; i++) {
		var objs = form.getElementsByTagName(tags[i]);
		for (o=0; o<objs.length; o++) {
			var val = objs[o].value;
			if (objs[o].type && (objs[o].type.toLowerCase() == 'checkbox' || objs[o].type.toLowerCase() == 'radio') && !objs[o].checked) val = null;
			if (objs[o].type && objs[o].type.toLowerCase() == 'hidden') val = null;
			if (val) {
				for (p=0; p<elms.length; p++) if (elms[p][1] && elms[p][1].toLowerCase() == objs[o].name.toLowerCase()) {
					elms[p][2] = val;
					val = null;
					break;
				}
				if (val) elms[elms.length] = Array(objs[o].name, objs[o].name, val);
			} else {
				//for (p=0; p<elms.length; p++) if (elms[p][1] && elms[p][1].toLowerCase() == objs[o].name.toLowerCase()) {elms[p][2] = ''; break; }
			}			
		}
	}
	if (!previewTitle) var previewTitle = 'Form Preview';
	if (!previewHeader) var previewHeader = '<h1>Form Preview</h1>';
	if (!previewFooter) var previewFooter = '<div style="float:right"><input type="button" value="Close" onclick="window.close()"></div>';
	if (!previewFieldTitle) var previewFieldTitle = '<b style="color:blue">Form Field</b>';
	if (!previewValueTitle) var previewValueTitle = '<b style="color:blue">Field Value</b>';
	eval('var cssURL = SFG_RESOURCE_CSS_URL_' + formid);
	var tmp = '';
	var out = '<HTML><HEAD><title>'+previewTitle+'</title><link href="'+cssURL+'" rel="stylesheet" type="text/css" /><HEAD><BODY>';
	out += previewHeader+'<table class="previewTable"><tr><td class="previewTableTitleCellHeader">'+previewFieldTitle+'</td><td class="previewTableValueCellHeader">'+previewValueTitle+'</td></tr>';
	for (i=0; i<elms.length; i++) {
		if (elms[i][0] && tmp.indexOf(elms[i][1]+'|') == -1) {
			out += '<tr><td class="previewTableTitleCell">'+elms[i][0].replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br />')+'</td><td class="previewTableValueCell">'+elms[i][2].replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br />')+'</td></tr>';
			tmp += elms[i][1]+'|';
		}
	}
	out += '</table>'+previewFooter+'</BODY></HTML>';	
	if (previewWin) previewWin.close();
	previewWin = window.open('about:blank', null, "height=700,width=600,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes,resizable=yes");
	if (previewWin) previewWin.document.writeln(out);
	if (previewWin) previewWin.document.close();
}
