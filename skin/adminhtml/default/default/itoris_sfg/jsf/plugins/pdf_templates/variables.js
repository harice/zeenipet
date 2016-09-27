Variables.prepareVariableRow = function(varValue, varLabel) {
    var value = (varValue).replace(/"/g, '&quot;').replace(/'/g, '\\&#39;');
    var content = '<a href="#" onclick="'+this.insertFunction+'(\''+ value +'\');return false;">' + varLabel + '</a>';
    return content;
};