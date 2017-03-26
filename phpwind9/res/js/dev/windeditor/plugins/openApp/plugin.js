/*
 * PHPWind WindEditor Plugin
 * @Copyright 	: Copyright 2011, phpwind.com
 * @Descript	: 加载应用插件
 * @Author		: wengqianshan@me.com
 * @Depend		: jquery.js(1.7 or later)
 * $Id: windeditor.js  $			:
 */
;(function ( $, window, undefined ) {

	var WindEditor = window.WindEditor;
	var pluginName = 'openApp';
	//应用插件配置,写到调用编辑器的页面中
	/*var editorApp = {
		root: 'http://localhost/openApp/',
		items:[
			{
				name: 'demo',
				params: {len: 8, age: 2}
			},
			{
				name: 'map',
				params: {from: 'google'}
			},
			{
				name: 'xiuxiu',
				params: {type: 2}
			}
		]
	};*/

	WindEditor.plugin(pluginName,function() {
		if(typeof editorApp == 'undefined' || editorApp.items == undefined){
			return false;
		}
		var _self = this;
		//创建容器
		// var appWrap = $('<li id="J_app_icon_wrap" class="open_app_icons"></li>');
		// if($(".open_app_icons").length < 1){
		// 	appWrap.appendTo('.wind_editor_icons');
		// }
		_self.appsContainer = _self.toolbar.find('.plugin_icons');

		//定义方法，确保app能读到windeditor
		WindEditor.initOpenApp = {};
		//加载器,加载app的主js文件
		$.each(editorApp.items, function(key, item) {
			var name = item.name;
			//初始化应用插件
			WindEditor.initOpenApp[name] = function(callback){
				callback.call(_self, item, editorApp.root);
			}
			Wind.js(editorApp.root + name+'/editorApp.js?v=' + GV.JS_VERSION, function(){});
		});

	});
})( jQuery, window);