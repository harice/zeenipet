/*******************************************************************/
// SmartFormer Gold (c) Form JS controller
//
// Product of IToris (c) 2009 http://www.itoris.com
//
/*******************************************************************/

function formSubmit15(submitter) {
	//getting list of elements
	var nodes = document.getElementById('sfg_fieldset15').childNodes;
	var objects = Array();
	for (var i=0; i<nodes.length; i++) if (nodes[i].tagName) objects[objects.length] = nodes[i];
	// perform validation
	var isFormValid = true;
	for (var vl=0; vl<sfg_validation15.length; vl++) if (window['validator15_'+sfg_validation15[vl][1]] && objects[sfg_validation15[vl][0]]) {
		var msg = window['validator15_'+sfg_validation15[vl][1]](objects[sfg_validation15[vl][0]], sfg_validation15[vl][2], sfg_validation15[vl][3] ? sfg_validation15[vl][3] : '');
		if (msg) {
			isFormValid = false;
			alert(msg);
			objects[sfg_validation15[vl][0]].focus();
			return;
		}
	}
	document.getElementById('sfg_submitter15').value = submitter;
	document.sfgForm15.submit();
}