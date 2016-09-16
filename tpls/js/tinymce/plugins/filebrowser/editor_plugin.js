var TinyMCE_FileBrowserPlugin = {
	
	options : {},
	
	openServerBrowser : function(field_name, current_url, link_type, win){
		
		this.options['target'] = win;
		
		var url = tinyMCE.activeEditor.getParam("plugin_filebrowser_width", '850')+"?type="+link_type+"&lang="+tinyMCE.activeEditor.getParam("language", 'default');
		
		tinyMCE.activeEditor.windowManager.open({
		   'url' : tinyMCE.activeEditor.getParam("plugin_filebrowser_src", false),
		   'width' : url,
		   'height' : tinyMCE.activeEditor.getParam("plugin_filebrowser_height", '850'),
		   'inline' : "yes",
			'resizable' : "no",
			'close_previous' : "no"
		}, {
			'window' : win,
			'input' : "src"
		});
		
	}
	
};

function start_file_browser(field_name, current_url, link_type, win){
	TinyMCE_FileBrowserPlugin.openServerBrowser(field_name, current_url, link_type, win);
}