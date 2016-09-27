document.write('<script language="Javascript" type="text/javascript" src="'+document.adminURL+'/plugins/editarea/edit_area/edit_area_full.js"></script>');

function showEditArea(syntax, title, content, target) {
	showMask();
	var div = document.getElementById('over_mask');
	if (!div) {
		var div = document.createElement('div');
		div.id = 'over_mask';
		var div2 = document.createElement('div');
		div2.id = 'over_mask_header';
		div2.innerHTML = '<div style="float:left">'+title+'</div>';
		var img = document.createElement('img');
		img.src = document.adminURL+'/images/cross.gif';
		img.id = 'over_maskClose';
		img.title = getTranslation('Close this Bar');
		addEvent(img, 'mousedown', closeEditor);
		div2.appendChild(img);
		div.appendChild(div2);
		var textarea = document.createElement('textarea');
		textarea.id = 'sfg_editarea';
		div.appendChild(textarea);
		document.body.appendChild(div);
		div.style.top = div.offsetTop + document.documentElement.scrollTop + 'px';
	} else {
		div.style.visibility = 'visible';
		document.getElementById('over_mask_header').getElementsByTagName('div')[0].innerHTML = title;
		textarea = document.getElementById('sfg_editarea');
	}
	textarea.style.height = div.offsetHeight - document.getElementById('over_mask_header').offsetHeight + 'px';
	textarea.style.width = div.offsetWidth - 2 - document.brc + 'px';
	textarea.value = content;
	editAreaLoader.init({
		id: "sfg_editarea"	// id of the textarea to transform	
		,start_highlight: true	
		,font_size: "8"
		,font_family: "verdana, monospace"
		,allow_resize: "n"
		,allow_toggle: false
		,language: getTranslation('en')
		,syntax: syntax	
		,toolbar: "new_document, save, |, charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
		,save_callback: target
		,EA_load_callback: "editAreaLoaded"
		,plugins: "charmap"
		,charmap_default: "arrows"
	});
}

function phpEditor() {
	showLoadingImage();
	showEditArea('php', getTranslation('PHP Editor'), document.sfg_php, 'saveGlobalPHP');
}

function saveGlobalPHP(ev) {
	closeEditor(ev);
	document.sfg_php = document.getElementById('sfg_editarea').value;
}

function htmlEditor() {
	showLoadingImage();
	showEditArea('html', getTranslation('HTML Editor'), document.sfg_html, 'saveGlobalHTML');
}

function saveGlobalHTML(ev) {
	closeEditor(ev);
	document.sfg_html = document.getElementById('sfg_editarea').value;
}

function jsEditor() {
	showLoadingImage();
	showEditArea('js', getTranslation('JavaScript Editor'), document.sfg_js, 'saveGlobalJS');
}

function saveGlobalJS(ev) {
	closeEditor(ev);
	document.sfg_js = document.getElementById('sfg_editarea').value;
}

function cssEditor() {
	showLoadingImage();
	showEditArea('css', getTranslation('CSS Editor'), document.sfg_css, 'saveGlobalCSS');
}

function saveGlobalCSS(ev) {
	closeEditor(ev);
	document.sfg_css = document.getElementById('sfg_editarea').value;
}

function closeEditor(ev) {
	editAreaLoader.delete_instance('sfg_editarea');
	var div = document.getElementById('over_mask');
	if (div) div.style.visibility = 'hidden';
	hideMask();
	hideLoadingImage();
}

function editAreaLoaded() {
	hideLoadingImage();
}

function editPropertyPHP(ev) {
	if (window.event) obj=window.event.srcElement; else obj=ev.currentTarget;
	document.propertyEdit =  obj.parentNode.parentNode;
	var prop = document.propertyEdit.getElementsByTagName('div')[0].innerHTML;
	showLoadingImage();
	if (document.propertiesTab == 0) {
		var type = document.activeObject.tag;
		var type2 = document.activeObject.getProp(document.activeObject.attributes,'type')
		if (type2) type +=','+type2;
		if (in_array(prop,document.msdn[type].params)) arr = document.activeObject.params;
		else if (in_array(prop,document.msdn[type].attributes)) arr = document.activeObject.attributes;
			else arr = document.activeObject.styles;
	}
	if (document.propertiesTab == 1) { arr = document.activeObject.attributes; }
	if (document.propertiesTab == 2) { arr = document.activeObject.styles; }
	if (document.propertiesTab == 3) { arr = document.activeObject.events; }
	if (prop == 'select-list' || prop == 'inner-content' || prop == 'inner-html' || prop == 'rich-text') var php = document.activeObject.contentPHP;
		else var php = document.activeObject.getPHP(arr, prop);
	if (!php) php = '';
	showEditArea('php', getTranslation('PHP Editor')+' - '+prop, php, 'savePropertyPHP');
}

function savePropertyPHP(ev) {
	closeEditor(ev);
	var prop = document.propertyEdit.getElementsByTagName('div')[0].innerHTML;
	if (document.propertiesTab == 0) {
		var type = document.activeObject.tag;
		var type2 = document.activeObject.getProp(document.activeObject.attributes,'type')
		if (type2) type +=','+type2;
		if (in_array(prop,document.msdn[type].params)) arr = document.activeObject.params;
		else if (in_array(prop,document.msdn[type].attributes)) arr = document.activeObject.attributes;
			else arr = document.activeObject.styles;
	}
	if (document.propertiesTab == 1) { arr = document.activeObject.attributes; }
	if (document.propertiesTab == 2) { arr = document.activeObject.styles; }
	if (document.propertiesTab == 3) { arr = document.activeObject.events; }
	var php = document.getElementById('sfg_editarea').value;
	if (prop == 'select-list' || prop == 'inner-content' || prop == 'inner-html' || prop == 'rich-text') document.activeObject.contentPHP = php;
		else document.activeObject.updateProp(arr, prop, document.activeObject.getProp(arr, prop), php);
	var img = document.propertyEdit.getElementsByTagName('img')[0];
	if (php && php.replace(/^\s+|\s+$/g,"") != '') {
		img.title = getTranslation('Edit PHP script');
		img.src = document.adminURL+'/modules/msdn/editphp.gif';
	} else {
		img.title = getTranslation('Add PHP script');
		img.src = document.adminURL+'/modules/msdn/addphp.gif';
	}
}

function editPropertyHTML(ev) {
	if (window.event) obj=window.event.srcElement; else obj=ev.currentTarget;
	document.propertyEdit = obj.parentNode.parentNode;
	var prop = document.propertyEdit.getElementsByTagName('div')[0].innerHTML;
	showLoadingImage();
	var html = document.activeObject.content;
	if (!html) html = '';
	showEditArea('html', getTranslation('HTML Editor')+' - '+prop, html, 'savePropertyHTML');
}

function savePropertyHTML(ev) {
	closeEditor(ev);
	var prop = document.propertyEdit.getElementsByTagName('div')[0].innerHTML;
	var html = document.getElementById('sfg_editarea').value;
	document.activeObject.content = html;
	document.propertyEdit.getElementsByTagName('textarea')[0].value = document.activeObject.content;
	document.activeObject.applyContent();
}