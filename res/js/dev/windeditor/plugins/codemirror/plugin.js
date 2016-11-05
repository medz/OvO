/*
 * PHPWind WindEditor Plugin
 * @Copyright 	: Copyright 2011, phpwind.com
 * @Descript	: 代码可视化编辑插件
 * @Author		: chaoren1641@gmail.com
 * @Depend		: jquery.js(1.7 or later)
 * $Id: windeditor.js 4472 2012-02-19 10:41:01Z chris.chencq $			:
 */
;(function ( $, window, undefined ) {
	
	var WindEditor = window.WindEditor;
	
	var pluginName = 'codemirror';

	WindEditor.plugin(pluginName,function() {
		var _self = this;
		var editorDoc = _self.editorDoc = _self.iframe[0].contentWindow.document;
		var head = document.head || document.getElementsByTagName( "head" )[0] || document.documentElement;
		var	codemirror_path = _self.options.editor_path + 'plugins/codemirror/codemirror/';
		$('<link rel="stylesheet" href="' + codemirror_path + 'codemirror.css?v='+ GV.JS_VERSION +'"/>', _self.editorDoc).appendTo( 'head');
		var editor;
		
		//CodeMirror的一些逻辑处理
		function codemirrorFormat() {
			setTimeout(function() {
				CodeMirror.commands["selectAll"](editor);
				var range = { from: editor.getCursor(true), to: editor.getCursor(false) };
				editor.autoFormatRange(range.from, range.to);
			},16);
		}

		//监听切换编辑器模式事件，当切换成代码模式时，调用codemirror插件
		$(_self).on('afterSetValue',function(){
			if(!document.getElementById('codemirror')) {
				var script = document.createElement( "script" );
				script.async = "async";
				script.src = codemirror_path +'codemirror.js?v=' + GV.JS_VERSION;
				script.id = 'codemirror';
				script.onload = script.onreadystatechange = function() {
					if(!window.CodeMirror) {
						return;
					}
					editor = window.CodeMirror.fromTextArea(_self.textarea[0], {
						mode: "htmlmixed",
		                lineNumbers: true,
		                lineWrapping:true,
		                onCursorActivity:function(e) {
		                	//_self.setValue( editor.getValue() );
		                }
					});
					var dom = editor.getWrapperElement();
					console.log(dom)
		            dom.style.cssText = 'width:100%;height:'+ _self.textarea.height() +'px;font-family:consolas,"Courier new",monospace;font-size:13px;';
		            editor.getScrollerElement().style.cssText = 'width:100%;height:'+ _self.textarea.height() +'px';
		            editor.refresh();
					//替换代码显示容器，也就是textarea
					_self.textarea.hide();
					_self.codeContainer = $(editor.getWrapperElement()).show();
					//重写codeContainer，模拟textarea
					_self.codeContainer.val = function(val) {
						if(val) {
							_self.textarea.val(val);
							editor.setValue(val);
							return _self;
						} else {
							return editor.getValue();
						}
					}
					codemirrorFormat();
				}
				head.insertBefore( script, head.firstChild );
			}else {
				codemirrorFormat();
			}
		});		
	});
	
	
})( jQuery, window);