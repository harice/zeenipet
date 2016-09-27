function EditorEvents(){
	this.listeners = new Array();
}

EditorEvents.prototype = {
	dispatch : function(eventName, ev) {
		var methodName = eventName.camelize();
		for (var i = 0; i < this.listeners.length; i++) {
			var listener = this.listeners[i];
			if (typeof(listener[methodName]) == 'function') {
				var fn = listener[methodName];
				fn.call(listener, ev);
			}
		}
	},

	addEventListener : function(listener) {
		this.listeners.push(listener);
	}
};

var editorEvents = new EditorEvents();