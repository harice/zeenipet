/*******************************************************************/
// SmartFormer Gold (c) Form JS controller
//
// Product of IToris (c) 2009 http://www.itoris.com
//
/*******************************************************************/

function formSubmit8(submitter) {
	//getting list of elements
	var nodes = document.getElementById('sfg_fieldset8').childNodes;
	var objects = Array();
	for (var i=0; i<nodes.length; i++) if (nodes[i].tagName) objects[objects.length] = nodes[i];
	// perform validation
	var isFormValid = true;
	for (var vl=0; vl<sfg_validation8.length; vl++) if (window['validator8_'+sfg_validation8[vl][1]] && objects[sfg_validation8[vl][0]]) {
		var msg = window['validator8_'+sfg_validation8[vl][1]](objects[sfg_validation8[vl][0]], sfg_validation8[vl][2], sfg_validation8[vl][3] ? sfg_validation8[vl][3] : '');
		if (msg) {
			isFormValid = false;
			alert(msg);
			objects[sfg_validation8[vl][0]].focus();
			return;
		}
	}
	document.getElementById('sfg_submitter8').value = submitter;
	document.sfgForm8.submit();
}

var objs = Array();
var isUS = true;

function countrySelected(obj) {
	var stateField = document.getElementsByName('state')[0];
	var stateSpan = document.getElementById('state_span');
	if (obj.value.toLowerCase()=='united states' && !isUS) {
		isUS = true;
		stateField.style.display = 'block';
		stateSpan.style.display = 'block';
		moveElementsDown(objs, 28);
	} else if (obj.value.toLowerCase()!='united states' && isUS) {
		isUS = false;
		stateField.selectedIndex = 0;
		objs = moveElementsUp(stateField, 28);
		stateField.style.display = 'none';	
		stateSpan.style.display = 'none';	
	}
}

function onFormLoad() {
	if (!document.getElementsByName('country')[0]) {
		setTimeout('onFormLoad()', 20);
		return;
	}
	if (document.getElementsByName('country')[0].value.toLowerCase()!='united states') {
		countrySelected(document.getElementsByName('country')[0]);
	}
}

onFormLoad();