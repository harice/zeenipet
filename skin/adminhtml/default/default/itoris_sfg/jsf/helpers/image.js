document.imageSelectorBasePath = '';

function showImageSelector(ev) {
	if (window.event) obj=window.event.srcElement; else obj=ev.currentTarget;
	document.propertyEdit =  obj.parentNode.parentNode;
	showMask();
	var div = document.getElementById('imageSelectorBack');
	if (div) {
		div.style.visibility = 'visible';
		div.style.left = Math.floor((document.documentElement.scrollWidth - div.offsetWidth)/2) + 'px';
		return;
	}
	var div = document.createElement('div');
	div.id = 'imageSelectorBack';
	document.body.appendChild(div);
	var div2 = document.createElement('div');
	div2.id = 'imageSelectorHeader';
	div2.innerHTML = '<div style="float:left">'+getTranslation('Image Selector')+'</div>';
	var img = document.createElement('img');
	img.src = document.adminURL+'/images/cross.gif';
	img.id = 'imageSelectorClose';
	img.title = getTranslation('Close this Bar');
	addEvent(img, 'mousedown', closeImageSelector);
	div2.appendChild(img);
	div.appendChild(div2);
	var div2 = document.createElement('div');
	div2.id = 'imageSelector';
	div.appendChild(div2);
	div.style.left = Math.floor((document.documentElement.scrollWidth - div.offsetWidth)/2) + 'px';
	getImagesList();
}

function updateImagesList(htm) {
	var div = document.getElementById('imageSelector');
	div.innerHTML = htm;
	var trs = div.getElementsByTagName('tr');
	for (i=0; i<trs.length; i++) {
		var img = trs[i].getElementsByTagName('img')[1];
		if (img) trs[i].title = img.src;
	}
	hideLoadingImage();	
}

function getImagesList() {
	showLoadingImage();
	var loc = document.location.href;
	loc += '?isAjax=true&task=getimageslist&start=' + document.imageSelectorBasePath;
	var xmlhttp = HTTPRequest(loc, false, updateImagesList);
	updateImagesList(xmlhttp.responseText);
}

function imagesListChangeLevel(path) {
	document.imageSelectorBasePath = path;
	getImagesList();
}

function applyImage(path) {
	closeImageSelector();
	var input = document.propertyEdit.getElementsByTagName('input')[0];
	var type = document.activeObject.tag;
	var type2 = document.activeObject.getProp(document.activeObject.attributes,'type')
	if (type2) type +=','+type2;
	var prop = document.propertyEdit.getElementsByTagName('div')[0].innerHTML;
	if (document.propertiesTab == 2 || in_array(prop,document.msdn[type].styles)) input.value = 'url(' + path + ')'; else input.value = path;
	input.focus();
	input.blur();
}

function closeImageSelector(ev) {
	document.getElementById('imageSelectorBack').style.visibility = 'hidden';
	hideMask();
	hideLoadingImage();
}