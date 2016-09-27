
document.sfdFormName = '';
document.sfdFormDescription = '';
document.formExternalAccess = Array(1,1);
document.formInternalAccess = true;

function formProperties() {
	showMask();
	var div = document.createElement('div');
	div.id = 'over_mask6';
	var div2 = document.createElement('div');
	div2.id = 'over_mask_header';
	div2.innerHTML = '<div style="float:left">'+getTranslation('Properties')+'</div>';
	var img = document.createElement('img');
	img.src = document.adminURL+'/images/cross.gif';
	img.id = 'over_maskClose';
	img.title = getTranslation('Close this Bar');
	addEvent(img, 'mousedown', closeFormProperties);
	div2.appendChild(img);
	div.appendChild(div2);
	document.body.appendChild(div);
	var div2 = document.createElement('div');
	div2.id = 'propertiesContainer';
	div.appendChild(div2);
	div2.style.width = div2.parentNode.offsetWidth - document.brc + 'px';
	div2.style.height = div2.parentNode.offsetHeight - document.getElementById('over_mask_header').offsetHeight - document.brc + 'px';
	div2.innerHTML = '<table cellpadding=0 cellspacing=2 border=0 style="width:100%;"><tr><td align="left" valign="top" width="70%"></td><td align="left" valign="top"></td></tr></table>';
	div2.getElementsByTagName('table')[0].getElementsByTagName('td')[0].innerHTML='<b style="color:blue; margin-right:5px;">'+getTranslation('Form Name')+':</b><input type="text" style="width:250px" onkeyup="document.sfdFormName=this.value"><br /><b style="color:blue; margin-right:5px;">'+getTranslation('Form Description')+':</b><br /><textarea style="width:95%; height:100px;" onkeyup="document.sfdFormDescription=this.value"></textarea><br /><div id="dbCheck"></div><div align="right"><input type="button" value="'+getTranslation('Save Form')+'" onclick="this.disabled=\'true\';saveForm();this.disabled=\'\';"></div>';
	div2.getElementsByTagName('table')[0].getElementsByTagName('td')[0].getElementsByTagName('input')[0].value = document.sfdFormName;
	div2.getElementsByTagName('table')[0].getElementsByTagName('td')[0].getElementsByTagName('textarea')[0].value = document.sfdFormDescription;
	if (document.sfgDB.name=='') {
		document.getElementById('dbCheck').innerHTML='<b style="color:red">'+getTranslation('The form is not connected with the database!')+'</b><input type="button" style="margin-left:5px;" value="'+getTranslation('Connect Now')+'" onclick="closeFormProperties(); showDBEditor();">';
	} else {
		document.getElementById('dbCheck').innerHTML='<b style="color:blue">'+getTranslation('The form is connected to DB table')+':</b> <b>'+document.sfgDB.name+'</b>';
	}
	div2.getElementsByTagName('table')[0].getElementsByTagName('td')[1].innerHTML='<b style="color:blue">'+getTranslation('External Access to the form')+':</b><br /><input type="checkbox" '+((document.formExternalAccess[0])?'checked':'')+' onclick="if (this.checked) document.formExternalAccess[0]=1; else document.formExternalAccess[0]=0;">&nbsp;<b>'+getTranslation('Site Visitors')+'</b><br /><input type="checkbox" '+((document.formExternalAccess[1])?'checked':'')+' onclick="if (this.checked) document.formExternalAccess[1]=1; else document.formExternalAccess[1]=0;">&nbsp;<b>'+getTranslation('Registered Users')+'</b>';
	div2.getElementsByTagName('table')[0].getElementsByTagName('td')[0].getElementsByTagName('input')[0].focus();
	div3 = document.createElement('div');
	div3.innerHTML = '<b style="color:blue">'+getTranslation('XML Content')+':</b>';
	div2.appendChild(div3);
	div3 = document.createElement('div');
	div3.id = 'xmlText';
	div2.appendChild(div3);
}

function closeFormProperties() {
	var div = document.getElementById('over_mask6');
	if (div) div.parentNode.removeChild(div);
	hideMask();
	hideLoadingImage();
}

function encodeXMLString(str) {
	if (!str) return '';
	if (typeof str == 'boolean' && str == true) str = 'true';
	str = str.replace(/&/g,'&amp;');
	str = str.replace(/</g,'&lt;');
	str = str.replace(/>/g,'&gt;');
	return str;
}

function addslashes(str) {
	if (!str) return '';
	if (typeof str == 'boolean' && str == true) str = 'true';
	str = encodeXMLString(str);
	str = str.replace(/\"/g,'&quot;');
	return str;
}

function saveForm() {
	if (document.sfdFormName=='') {
		if (!document.getElementById('propertiesContainer')) formProperties();
		hideLoadingImage();
		alert(getTranslation('Please specify the form name'));
		return;
	}
	//if (!document.getElementById('xmlText')) formProperties();
	showLoadingImage();
	var s='<?xml version="1.0" encoding="UTF-8"?>\n';
	s+='<form name="'+addslashes(document.sfdFormName)+'" ext_access="'+document.formExternalAccess[0]+document.formExternalAccess[1]+'">\n';
	if (document.sfdFormDescription.replace(/^\s+|\s+$/g,"")!='') {
		s+='  <description>\n';
		s+='    '+encodeXMLString(document.sfdFormDescription)+'\n';
		s+='  </description>\n';
	}
	if (document.sfgDB.name.replace(/^\s+|\s+$/g,"")!='') {
		s+='  <database name="'+addslashes(document.sfgDB.name)+'">\n';
		for (i=0; i<document.sfgDBMapping.length; i++) s+='    <map sfgfield="'+addslashes(document.sfgDBMapping[i][1])+'" dbfield="'+addslashes(document.sfgDBMapping[i][0])+'" />\n';
		s+='  </database>\n';
	}
	if (document.sfg_php.replace(/^\s+|\s+$/g,"")!='') {
		s+='  <globalphp>\n';
		s+='    '+encodeXMLString(document.sfg_php)+'\n';
		s+='  </globalphp>\n';
	}
	if (document.sfg_js.replace(/^\s+|\s+$/g,"")!='') {
		s+='  <globaljs>\n';
		s+='    '+encodeXMLString(document.sfg_js)+'\n';
		s+='  </globaljs>\n';
	}
	if (document.sfg_html.replace(/^\s+|\s+$/g,"")!='') {
		s+='  <globalhtml>\n';
		s+='    '+encodeXMLString(document.sfg_html)+'\n';
		s+='  </globalhtml>\n';
	}
	if (document.sfg_css.replace(/^\s+|\s+$/g,"")!='') {
		s+='  <globalcss>\n';
		s+='    '+encodeXMLString(document.sfg_css)+'\n';
		s+='  </globalcss>\n';
	}
	for (i=0; i<document.sfgPages.length; i++) s+='  <page name="'+addslashes(document.sfgPages[i])+'" id="'+i+'" />\n';
	for (i=0; i<document.allElements.length; i++) {
		s+='  <element sfgalias="'+addslashes(document.allElements[i].alias)+'" tag="'+addslashes(document.allElements[i].tag)+'" page="'+document.allElements[i].page+'">\n';
		if (document.allElements[i].content && document.allElements[i].content.replace(/^\s+|\s+$/g,"")!='') {
			s+='    <content>\n';
			s+='      '+encodeXMLString(document.allElements[i].content)+'\n';
			s+='    </content>\n';
		}
		if (document.allElements[i].contentPHP && document.allElements[i].contentPHP.replace(/^\s+|\s+$/g,"")!='') {
			s+='    <contentphp>\n';
			s+='      '+encodeXMLString(document.allElements[i].contentPHP)+'\n';
			s+='    </contentphp>\n';
		}
		for (o=0; o<document.allElements[i].attributes.length; o++) {
			if (document.allElements[i].attributes[o][2]) {
				s+='    <attribute name="'+addslashes(document.allElements[i].attributes[o][0])+'" value="'+addslashes(document.allElements[i].attributes[o][1])+'">\n';
				s+='      '+encodeXMLString(document.allElements[i].attributes[o][2])+'\n';
				s+='    </attribute>\n';
			} else s+='    <attribute name="'+addslashes(document.allElements[i].attributes[o][0])+'" value="'+addslashes(document.allElements[i].attributes[o][1])+'" />\n';
		}
		for (o=0; o<document.allElements[i].styles.length; o++) {
			if (document.allElements[i].styles[o][2]) {
				s+='    <style name="'+addslashes(document.allElements[i].styles[o][0])+'" value="'+addslashes(document.allElements[i].styles[o][1])+'">\n';
				s+='      '+encodeXMLString(document.allElements[i].styles[o][2])+'\n';
				s+='    </style>\n';
			} else s+='    <style name="'+addslashes(document.allElements[i].styles[o][0])+'" value="'+addslashes(document.allElements[i].styles[o][1])+'" />\n';
		}
		for (o=0; o<document.allElements[i].events.length; o++) {
			if (document.allElements[i].events[o][2]) {
				s+='    <event name="'+addslashes(document.allElements[i].events[o][0])+'" value="'+addslashes(document.allElements[i].events[o][1])+'">\n';
				s+='      '+encodeXMLString(document.allElements[i].events[o][2])+'\n';
				s+='    </event>\n';
			} else s+='    <event name="'+addslashes(document.allElements[i].events[o][0])+'" value="'+addslashes(document.allElements[i].events[o][1])+'" />\n';
		}
		for (o=0; o<document.allElements[i].params.length; o++) {
			s+='    <param name="'+addslashes(document.allElements[i].params[o][0])+'" value="'+addslashes(document.allElements[i].params[o][1])+'" />\n';
		}
		s+='  </element>\n';
	}
	for (i=0; i<document.emailTemplates.length; i++) {
		s+='  <email_template name="'+addslashes(document.emailTemplates[i].name)+'" from_name="'+addslashes(document.emailTemplates[i].fromName)+'" from_email="'+addslashes(document.emailTemplates[i].fromEmail)+'" subject="'+addslashes(document.emailTemplates[i].subject)+'" cc="'+addslashes(document.emailTemplates[i].cc)+'" bcc="'+addslashes(document.emailTemplates[i].bcc)+'" id="'+document.emailTemplates[i].id+'" format="'+document.emailTemplates[i].format+'">\n';
		s+='    '+encodeXMLString(document.emailTemplates[i].body)+'\n';
		s+='  </email_template>\n';
	}
	for (i=0; i<document.validators.length; i++) {
		s+='  <validator name="'+addslashes(document.validators[i].alias)+'">\n';
		s+='    <js>\n';
		s+='      '+encodeXMLString(document.validators[i].js)+'\n';
		s+='    </js>\n';
		s+='    <php>\n';
		s+='      '+encodeXMLString(document.validators[i].php)+'\n';
		s+='    </php>\n';
		s+='  </validator>\n';
	}
	s+='</form>\n';
	
	var xmlhttp;
	try{xmlhttp = new XMLHttpRequest();}
	catch (e) {
		try{xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");}
		catch (e) {
			try{xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");}
			catch (e) {alert(e.message);return;}
		}
	}

	var s2='content='+escape(base64encode(escape(safePost(s))));
	s2+='&name='+escape(base64encode(escape(safePost(document.sfdFormName))));
	s2+='&description='+escape(base64encode(escape(safePost(document.sfdFormDescription))));
	s2+='&length='+s.length;
	s2+='&fid='+document.form_id;
	s2+='&form_key='+document.saveFormKEY;

	var url = document.saveFormURL;
	
	xmlhttp.open("POST", url, false);
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-Length", s2.length);
	xmlhttp.setRequestHeader("Connection", "close");
	try {
		xmlhttp.send(s2);
	} catch (e) {
		hideLoadingImage();
		alert(getTranslation('Connection problem occured while saving the form\nPlease try again.'));
		return;
	}
	var response = xmlhttp.responseText;

	if (document.getElementById('xmlText')) {
		s = encodeXMLString(s);
		s = s.replace(/ /g,'&nbsp;');
		s = s.replace(/\n/g,'<br />');
		document.getElementById('xmlText').innerHTML = s;
	}

	if (document.form_id==0 && parseInt(response) > 0) document.form_id = parseInt(response);
	if (parseInt(response) > 0) response = response.substr(response.indexOf(' '));
	hideLoadingImage();
	alertSfg(response);
}

function stripslashes(str) {
	for (i=str.length-1; i>=0; i--) {
		if (str.substr(i,2)='\"') str = str.substring(0,i)+str.substr(i+1);
	}
	return str;
}

SFG_InterimElement = function(tag,page,alias,content,contentphp,attributes,styles,events,params) {
	this.tag=tag;
	this.page=page;
	this.alias=alias;
	this.content=content;
	this.contentPHP=contentphp;
	this.attributes=attributes;
	this.styles=styles;
	this.events=events;
	this.params=params;
}

function loadForm() {
	showLoadingImage();
	var xmlDoc = loadXMLFile(document.loadFormURL);//+'fid/'+document.form_id+'/tmp/'+Math.random()+'/');
	var form = xmlDoc.documentElement;
	if (!form) {hideLoadingImage();alert('Cannot load the form');document.form_id=0;return;}
	var tmp = form.getAttribute('name');
	if (tmp) document.sfdFormName = tmp;
	var tmp = form.getAttribute('ext_access');
	if (tmp) document.formExternalAccess = Array(tmp.substr(0,1)-0,tmp.substr(0,2)-0);
	var tmp = form.getElementsByTagName('description')[0];
	if (tmp) document.sfdFormDescription = tmp.text ? tmp.text.replace(/^\s+|\s+$/g,"") : tmp.textContent ? tmp.textContent.replace(/^\s+|\s+$/g,"") : '';
	var database = form.getElementsByTagName('database')[0];
	if (database) {
		var tmp = database.getAttribute('name');
		if (tmp) {
			document.sfgDB.name = tmp;
			document.sfgDB.fields = Array();
			for (i=0; i<document.bdTables.length; i++) {
				if (document.bdTables[i].toLowerCase()==document.bdPrefix.toLowerCase()+tmp.toLowerCase().replace('#__','') || document.bdTables[i].toLowerCase()==tmp.toLowerCase()) {getTableInfo(i);break;}
			}
			if (document.sfgDB.fields.length > 0) {
				var map = database.getElementsByTagName('map');
				document.sfgDBMapping = Array();
				for (i=0; i<map.length; i++) {
					var sfgfield = map[i].getAttribute('sfgfield');
					var dbfield = map[i].getAttribute('dbfield');
					document.sfgDBMapping[document.sfgDBMapping.length] = Array(dbfield, sfgfield);
					for (o=0; o<document.sfgDB.fields.length; o++) if (document.sfgDB.fields[o].field.toLowerCase()==dbfield.toLowerCase()) {
						document.sfgDB.fields[o].initialField = dbfield;
						document.sfgDB.fields[o].sfgField = sfgfield;
						break;
					}
				}
			}			
		}
	}
	var tmp = form.getElementsByTagName('globalphp')[0];
	if (tmp) document.sfg_php = tmp.text ? tmp.text.replace(/^\s+|\s+$/g,"") : tmp.textContent.replace(/^\s+|\s+$/g,"");
	var tmp = form.getElementsByTagName('globaljs')[0];
	if (tmp) document.sfg_js = tmp.text ? tmp.text.replace(/^\s+|\s+$/g,"") : tmp.textContent.replace(/^\s+|\s+$/g,"");
	var tmp = form.getElementsByTagName('globalhtml')[0];
	if (tmp) document.sfg_html = tmp.text ? tmp.text.replace(/^\s+|\s+$/g,"") : tmp.textContent.replace(/^\s+|\s+$/g,"");
	var tmp = form.getElementsByTagName('globalcss')[0];
	if (tmp) document.sfg_css = tmp.text ? tmp.text.replace(/^\s+|\s+$/g,"") : tmp.textContent.replace(/^\s+|\s+$/g,"");
	var pages = form.getElementsByTagName('page');
	document.sfgPages = Array();
	for (z=0; z<pages.length; z++) {
		var tmp = pages[z].getAttribute('name');
		if (tmp) {
			//document.sfgPages[document.sfgPages.length] = tmp;
			pagerAddPage(null);
		}
	}
	document.allElements = Array();
	var elements = form.getElementsByTagName('element');
	for (w=0; w<elements.length; w++) {
		var tag = elements[w].getAttribute('tag');
		var page = elements[w].getAttribute('page');
		var alias = elements[w].getAttribute('sfgalias');
		var content = elements[w].getElementsByTagName('content')[0];
		if (content) content = content.text ? content.text.replace(/^\s+|\s+$/g,"") : content.textContent.replace(/^\s+|\s+$/g,"");
		var contentphp = elements[w].getElementsByTagName('contentphp')[0];
		if (contentphp) contentphp = contentphp.text ? contentphp.text.replace(/^\s+|\s+$/g,"") : contentphp.textContent.replace(/^\s+|\s+$/g,"");
		var attributes = Array();
		var tmp = elements[w].getElementsByTagName('attribute');
		for (q=0; q<tmp.length; q++) {
			var vr = tmp[q].getAttribute('name');
			var val = tmp[q].getAttribute('value');
			if (val == 'true') val = true;
			var php = tmp[q].text ? tmp[q].text.replace(/^\s+|\s+$/g,"") : tmp[q].textContent ? tmp[q].textContent.replace(/^\s+|\s+$/g,"") : '';
			attributes[attributes.length] = Array(vr,val,php);
		}
		var styles = Array();
		var tmp = elements[w].getElementsByTagName('style');
		for (q=0; q<tmp.length; q++) {
			var vr = tmp[q].getAttribute('name');
			var val = tmp[q].getAttribute('value');
			var php = tmp[q].text ? tmp[q].text.replace(/^\s+|\s+$/g,"") : tmp[q].textContent ? tmp[q].textContent.replace(/^\s+|\s+$/g,"") : '';
			styles[styles.length] = Array(vr,val,php);
		}
		var events = Array();
		var tmp = elements[w].getElementsByTagName('event');
		for (q=0; q<tmp.length; q++) {
			var vr = tmp[q].getAttribute('name');
			var val = tmp[q].getAttribute('value');
			var php = tmp[q].text ? tmp[q].text.replace(/^\s+|\s+$/g,"") : tmp[q].textContent ? tmp[q].textContent.replace(/^\s+|\s+$/g,"") : '';
			events[events.length] = Array(vr,val,php);
		}
		var params = Array();
		var tmp = elements[w].getElementsByTagName('param');
		for (q=0; q<tmp.length; q++) {
			var vr = tmp[q].getAttribute('name');
			var val = tmp[q].getAttribute('value');
			if (val == 'true') val = true;
			var php = tmp[q].text ? tmp[q].text.replace(/^\s+|\s+$/g,"") : tmp[q].textContent ? tmp[q].textContent.replace(/^\s+|\s+$/g,"") : '';
			params[params.length] = Array(vr,val,php);
		}
		//var proto = new SFG_InterimElement(tag,page,alias,content,contentphp,attributes,styles,events,params);
		document.allElements[document.allElements.length] = new SFG_Element(new SFG_InterimElement(tag,page,alias,content,contentphp,attributes,styles,events,params));
		document.allElements[document.allElements.length-1].page = page;
		document.allElements[document.allElements.length-1].alias = alias;
		
	}
	var templates = form.getElementsByTagName('email_template');
	document.emailTemplates = Array();
	for (i=0; i<templates.length; i++) {
		var name=templates[i].getAttribute('name');
		var from_name=templates[i].getAttribute('from_name');
		var from_email=templates[i].getAttribute('from_email');
		var subject=templates[i].getAttribute('subject');
		var cc=templates[i].getAttribute('cc');
		var format=templates[i].getAttribute('format');
		var bcc=templates[i].getAttribute('bcc');
		var id=parseInt(templates[i].getAttribute('id'));
		var body = templates[i].text ? templates[i].text : templates[i].textContent ? templates[i].textContent.replace(/^\s+|\s+$/g,"") : '';
		document.emailTemplates[document.emailTemplates.length] = new SFG_EmailTemplate(name, body, from_name, from_email, subject, cc, bcc, format);
		document.emailTemplates[document.emailTemplates.length-1].id = id;
	}
	var validators = form.getElementsByTagName('validator');
	document.validators = Array();
	for (i=0; i<validators.length; i++) {
		var name=validators[i].getAttribute('name');
		var js = validators[i].getElementsByTagName('js')[0];
		js = js.text ? js.text.replace(/^\s+|\s+$/g,"") : js.textContent ? js.textContent.replace(/^\s+|\s+$/g,"") : '';
		var php = validators[i].getElementsByTagName('php')[0];
		php = php.text ? php.text.replace(/^\s+|\s+$/g,"") : php.textContent ? php.textContent.replace(/^\s+|\s+$/g,"") : '';
		
		document.validators[document.validators.length] = new SFG_Validator(name,js,php);
	}
	switchPage(1);
	switchPage(0);
	hideLoadingImage();
}

function base64encode (input) {
	var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", chr, enc, output = "", i = 0;
	do {
		chr = [input.charCodeAt(i++), input.charCodeAt(i++), input.charCodeAt(i++)];
		enc = [chr[0] >> 2, ((chr[0] & 3) << 4) | (chr[1] >> 4), ((chr[1] & 15) << 2) | (chr[2] >> 6), chr[2] & 63];
		if (isNaN(chr[1])) enc[2] = enc[3] = 64; else if (isNaN(chr[2])) enc[3] = 64;
		output += keyStr.charAt(enc[0]) + keyStr.charAt(enc[1]) + keyStr.charAt(enc[2]) + keyStr.charAt(enc[3]);
	} while (i < input.length);
	return output;
}

function safePost(input) {
	input = input.replace(/&/g,'#amp#');
	input = input.replace(/\+/g,'#plus#');
	input = input.replace(/\'/g,'#quot#');
	input = input.replace(/\%/g,'#pr#');
	input = input.replace(/\$/g,'#ss#');
	input = input.replace(/\|/g,'#or#');
	return input;
}
