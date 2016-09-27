function validator18_0(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Field is required';
msg = msg.replace('%s', sfgName); 
if (obj.type && (obj.type.toLowerCase()=='checkbox' || obj.type.toLowerCase()=='radio') && !obj.checked) return msg; 
if (obj.tagName.toLowerCase()=='select' && (obj.selectedIndex==-1 || obj.selectedIndex==0)) return msg; 
if (obj.value=='') return msg;
}

function validator18_1(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Group: %s. Please select a value';
msg = msg.replace('%s', sfgName); 
var objs = document.getElementsByName(obj.name); 
var checked = false; 
for (i=0; i<objs.length; i++) if (objs[i].checked) { checked = true; break; } 
if (!checked) return msg;
}

function validator18_2(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object, obj2 - another HTML element for comparing
var msg = 'Field: %s. Entities are not identival';
msg = msg.replace('%s', sfgName);
var el2 = document.getElementsByName(obj2)[0];
if (el2 && obj.value != el2.value) return msg;
}

function validator18_3(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Invalid email. Please specify a valid value';
msg = msg.replace('%s', sfgName);
var RegExp=/^((([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+(\.([a-z]|[0-9]|!|#|$|%|&|'|\*|\+|\-|\/|=|\?|\^|_|`|\{|\||\}|~)+)*)@((((([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.))*([a-z]|[0-9])([a-z]|[0-9]|\-){0,61}([a-z]|[0-9])\.)[\w]{2,4}|(((([0-9]){1,3}\.){3}([0-9]){1,3}))|(\[((([0-9]){1,3}\.){3}([0-9]){1,3})\])))$/;
if (obj.value!='' && !RegExp.test(obj.value.toLowerCase())) return msg;
}

function validator18_4(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Please specify a correct zip/postal';
msg = msg.replace('%s', sfgName);
var letters='qwertyuiopasdfghjklzxcvbnm -1234567890';
var ps=false;
for(i=0;i<obj.value.length;i++) if (letters.indexOf(obj.value.toLowerCase().substr(i,1))<0) ps=true;
if (ps || obj.value.length>10) return msg;
}

function validator18_5(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Invalid US Zip. Please specify a valid value';
msg = msg.replace('%s', sfgName);
var zipPattern1=/^(\d{5})$/;
var zipPattern2=/^(\d{5})\-(\d{4})$/;
if ((obj.value.match(zipPattern1)==null)&&(obj.value.match(zipPattern2)==null)&&obj.value.length>0) return msg;
}

function validator18_6(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Please specify a correct phone number';
msg = msg.replace('%s', sfgName);
var letters='1234567890 -.()+';
var ps=false;
for(i=0;i<obj.value.length;i++) if (letters.indexOf(obj.value.toLowerCase().substr(i,1))<0) ps=true;
if (ps || obj.value.length>10) return msg;
}

function validator18_7(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Invalid value! Please, provide a phone number in format XXX-XXX-XXXX, where X is a digit.';
msg = msg.replace('%s', sfgName);
var phonePattern=/^(\d{3})\-(\d{3})\-(\d{4})$/;
if ((obj.value.match(phonePattern)==null)&&obj.value.length>0) return msg;
}

function validator18_8(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. The password should contain 6 to 12 symbols';
msg = msg.replace('%s', sfgName);
if ((obj.value.length<6 || obj.value.length>12) && obj.value.length!=0) return msg;
}

function validator18_9(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Please specify a correct credit card number';
msg = msg.replace('%s', sfgName);
var ccPattern=/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/;
if ((obj.value.match(ccPattern)==null)&&obj.value.length>0) return msg;
}

function validator18_10(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Please specify a correct credit card CVV';
msg = msg.replace('%s', sfgName);
var letters='1234567890';
var ps=false;
for(i=0;i<obj.value.length;i++) if (letters.indexOf(obj.value.toLowerCase().substr(i,1))<0) ps=true;
if (ps || (obj.value.length<3 && obj.value.length!=0) || obj.value.length>4) return msg;
}

function validator18_11(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Please specify currency correctly';
msg = msg.replace('%s', sfgName);
var letters='1234567890.';
var ps=false; for(i=0;i<obj.value.length;i++) if (letters.indexOf(obj.value.toLowerCase().substr(i,1))<0) ps=true;
if (ps || obj.value.length>10 || Math.floor(obj.value*100)/100!=obj.value-0) return msg;
}

function validator18_12(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Please specify a valid URL';
msg = msg.replace('%s', sfgName);
var RegExp=/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;
if (obj.value!='' && !RegExp.test(obj.value)) return msg;
}

function validator18_13(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Please specify a correct IP';
msg = msg.replace('%s', sfgName);
var RegExp=/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;
var a=obj.value.split('.');
if (obj.value!='' && !RegExp.test(obj.value) || a[0]>255 || a[1]>255 || a[2]>255 || a[3]>255) return msg;
}

function validator18_14(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Invalid value! Please, provide a SSN in format XXX-XX-XXXX or XX-XXXXXXX, where X is a digit.';
msg = msg.replace('%s', sfgName);
var ssnPattern1 = /^(\d{3})\-(\d{2})\-(\d{4})$/;
var ssnPattern2 = /^(\d{2})\-(\d{7})$/;
if ((obj.value.match(ssnPattern1)==null)&&(obj.value.match(ssnPattern2)==null)&&obj.value.length>0) return msg;
}

function validator18_15(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Invalid value! Field value should be in MM/DD/YYYY format.';
msg = msg.replace('%s', sfgName);
var a=obj.value.split('/');
if (a[0]-0>0 && a[0]-0<=12 && a[1]-0>0 && a[1]-0<=31 && a[2]-0>1000 && a[2]-0<=9999 && a[3]==null ||obj.value.length==0) return null; else return msg;
}

function validator18_16(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Invalid value! Field value should be in MM/DD/YY format';
msg = msg.replace('%s', sfgName);
var a=obj.value.split('/');
if (a[0]-0>0 && a[0]-0<=12 && a[1]-0>0 && a[1]-0<=31 && a[2]-0>=0 && a[2]-0<=99 && a[3]==null ||obj.value.length==0) return null; else return msg;
}

function validator18_17(obj, sfgName, obj2) {
// incoming variables: obj - HTML element firing the event, sfgName - system name of the object 
var msg = 'Field: %s. Invalid value! Field value should be in HH:MM format.';
msg = msg.replace('%s', sfgName);
var a=obj.value.split(':');
if (a[0]-0>=0 && a[0]-0<24 && a[1]-0>=0 && a[1]-0<60 && a[2]==null ||obj.value.length==0) return null; else return msg;
}

