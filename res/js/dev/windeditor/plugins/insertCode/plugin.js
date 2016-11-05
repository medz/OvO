/*
 * PHPWind WindEditor Plugin
 * @Copyright 	: Copyright 2011, phpwind.com
 * @Descript	: 插入高亮代码插件
 * @Author		: chaoren1641@gmail.com
 * @Depend		: jquery.js(1.7 or later)
 * $Id: windeditor.js 4472 2012-02-19 10:41:01Z chris.chencq $			:
 */
;(function ( $, window, undefined ) {

	var WindEditor = window.WindEditor;

	var pluginName = 'insertCode',
		dialog = $('<div class="edit_menu" style="display:none;">\
					<div class="edit_menu_code">\
						<div class="edit_menu_top"><a href="" class="edit_menu_close">关闭</a><strong>插入代码</strong></div>\
						<div class="edit_menu_cont">\
							<div style="margin:10px 0">\
							请选择语言\
							<select id="language">\
								<option value="text" selected="selected">纯文本</option>\
								<option value="html">Html</option>\
								<option value="css">Css</option>\
								<option value="js">Javascript</option>\
								<option value="php">Php</option>\
								<option value="sql">Sql</option>\
		                        <option value="as3">ActionScript3</option>\
		                        <option value="bash">Bash/Shell</option>\
		                        <option value="cpp">C/C++</option>\
		                        <option value="cf">CodeFunction</option>\
		                        <option value="c#">C#</option>\
		                        <option value="delphi">Delphi</option>\
		                        <option value="diff">Diff</option>\
		                        <option value="erlang">Erlang</option>\
		                        <option value="groovy">Groovy</option>\
		                        <option value="java">Java</option>\
		                        <option value="jfx">JavaFx</option>\
		                        <option value="pl">Perl</option>\
		                        <option value="plain">Plain Text</option>\
		                        <option value="ps">PowerShell</option>\
		                        <option value="python">Python</option>\
		                        <option value="ruby">Ruby</option>\
		                        <option value="scala">Scala</option>\
		                        <option value="vb">Vb</option>\
		                        <option value="xml">Xml</option>\
	                    	</select>\
	                    	</div>\
							<textarea></textarea>\
						</div>\
						<div class="edit_menu_bot">\
							<button type="button" class="edit_menu_btn">确定</button><button type="button" class="edit_btn_cancel">取消</button>\
						</div>\
					</div>\
				</div>');

	WindEditor.plugin(pluginName,function() {
		var _self = this;
		var editorDoc = _self.editorDoc,
			editorToolbar = _self.toolbar;

		//toolbar中的icon容器
		var icon_ul = editorToolbar.find('ul');

		//自定义插入位置,插到insertBlockquote后面
		var plugin_icon = $('<div class="wind_icon" unselectable="on"><span class="'+ pluginName +'" title="插入代码"></span></div>').appendTo( _self.pluginsContainer );
		plugin_icon.on('click',function() {
			if($(this).hasClass('disabled')) {
				return;
			}
			if(!$.contains(document.body,dialog[0]) ) {
				dialog.appendTo( document.body );
			}
			var syntaxDiv = _self.getRangeNode('.syntaxhighlighter');
    		if( syntaxDiv && syntaxDiv.length ) {
    			syntaxDiv.find('div.container').each(function() {
	                for(var str = [],c = 0,ci;ci = this.childNodes[c++];){
	                    str.push($(ci).text());
	                }
	                var code = str.join('\n');
	                dialog.find('textarea').val(code);
				});
    		}else {
    			var html = _self.getRangeHTML();
    			if(!html) {
    				dialog.find('textarea').val('');
    			}else{
    				//转换选择的代码
    				var formathtml = _self.formatXHTML(html);

					_self.codeContainer.val(formathtml);
					$(_self).trigger('afterSetValue');
    				dialog.find('textarea').val(_self.codeContainer.val());
    			}
					
    		}
			_self.showDialog(dialog);

		});

		//弹窗的关闭事件
		dialog.find('a.edit_menu_close,button.edit_btn_cancel').on('click',function(e) {
			e.preventDefault();
			_self.hideDialog();
		});


		function insertHightlight(pre) {
			if(!_self.iframe[0].contentWindow.SyntaxHighlighter) {
				return;
			}
			_self.iframe[0].contentWindow.SyntaxHighlighter.config.stripBrs = true;
        	var html = _self.iframe[0].contentWindow.SyntaxHighlighter.highlight(pre,null,true);
			editorDoc.body.removeChild(pre);
			var syntaxDiv = _self.getRangeNode('.syntaxhighlighter');
    		if( syntaxDiv && syntaxDiv.length ) {
    			syntaxDiv.replaceWith(html);
    		}else {
    			_self.insertHTML(html);
    		}
			//加上一个自定义标记，在切换到源代码时，不需要这些高亮后的源代码
			setTimeout(function(){
				var div = editorDoc.getElementById(_self.iframe[0].contentWindow.SyntaxHighlighter.getHighlighterDivId());
            	div.setAttribute('highlighter',$.trim(pre.className));
            	//$('<br/>').insertAfter(div);
            	_self.focus();
			},10);

        }

		var head = editorDoc.head || editorDoc.getElementsByTagName( "head" )[0] || editorDoc.documentElement,
			syntaxHihglighter_path = _self.options.editor_path + 'plugins/insertCode/syntaxHihglighter/';

		$('<link rel="stylesheet" href="' + syntaxHihglighter_path + 'styles/shCoreDefault.css?v='+ GV.JS_VERSION +'"/>', _self.editorDoc).appendTo( $(head));

		//加载高亮需要的脚本
		function loadSyntaxHihglighter(callback) {
			if(!editorDoc.getElementById('syntaxHihglighter')) {
				var script = editorDoc.createElement( "script" );
				script.async = "async";
				script.src = syntaxHihglighter_path +'scripts/shCore.js?v=' + GV.JS_VERSION;
				script.id = 'syntaxHihglighter';
				script.onload = script.onreadystatechange = function () {
					if(!callback) {return;}
					var state = script.readyState;
		            if (!callback.done && (!state || /loaded|complete/.test(state))) {
		                callback.done = true;
		                callback();
		            }
				}
				head.insertBefore( script, head.firstChild );
			}else {
				callback && callback();
			}
		}

		//插入代码
		dialog.find('.edit_menu_btn').on('click.' + pluginName,function(e) {
			e.preventDefault();
			var code = dialog.find('textarea').val(),
				code_type = dialog.find('select').val();

			var pre = editorDoc.createElement("pre");
            pre.className = "brush:"+ code_type +";toolbar:false;";
            pre.appendChild(editorDoc.createTextNode(code));
            editorDoc.body.appendChild(pre);

            //载入代码高亮插件
            loadSyntaxHihglighter(function() {
            	insertHightlight(pre);
            	adjustHeight();
            });

			_self.hideDialog();
		});

		function HTMLEncode(html) {
			var temp = document.createElement ("div");
			(temp.textContent != null) ? (temp.textContent = html) : (temp.innerText = html);
			var output = temp.innerHTML;
			temp = null;
			return output;
		}
		function HTMLDecode(text) {
			var temp = document.createElement("div");
			temp.innerHTML = text;
			var output = temp.innerText || temp.textContent;
			temp = null;
			return output;
		}

		function wysiwyg() {
			//检查代码里有没有需要高亮的代码
			var reg = /\[code\s*([^\]]*)\]([\s\S]*?)\[\/code\]/ig,
				html = $(editorDoc.body).html();
			if(!html.match(reg)) {
				return;
			}
			//html = html.replace(/\r?\n/ig,"<br />");
			html = html.replace(reg,function(all, $1, $2) {
				$2 = $2.replace(/&lt;/ig, '<');
				$2 = $2.replace(/&gt;/ig, '>');
				$2 = $2.replace(/&amp;/ig, '&');
				$2 = $2.replace(/&nbsp;/ig, ' ');
				$2 = $2.replace(/<br>/gi,"\n");
				return '<pre class="'+ $1 +'">'+ $2 +'</pre>';
			});
			$(editorDoc.body).html(html);
			loadSyntaxHihglighter(function() {
				$(editorDoc.body).find('pre[class*=brush]').each(function() {
					var pre = document.createElement("pre"),txt,div;
	                pre.className = this.className;
	                pre.style.display = "none";
	                pre.appendChild(document.createTextNode($(this).text()));
	                document.body.appendChild(pre);

	                _self.iframe[0].contentWindow.SyntaxHighlighter.config.stripBrs = true;
	                try{
	                    txt = _self.iframe[0].contentWindow.SyntaxHighlighter.highlight(pre,null,true);
	                }catch(e) {
	                    $(pre).remove();
	                    return ;
	                }
	                div = editorDoc.createElement("div");
	                div.innerHTML = txt;
	                div.firstChild.setAttribute('highlighter',pre.className);
	                this.parentNode.insertBefore(div.firstChild,this);
	                $(pre).remove();
	                $(this).remove();
	                adjustHeight();
				});
			});
    	}
		//需要调整高度,借鉴ueditor
		function adjustHeight() {
			setTimeout(function(){
				//如果编辑器的editorDoc设置了designMode="on"，则IE下取不到contentWindow
		        var div = editorDoc.getElementById(_self.iframe[0].contentWindow.SyntaxHighlighter.getHighlighterDivId());
		        if(div){
	                var tds = div.getElementsByTagName('td');
	                for(var i=0,li,ri;li=tds[0].childNodes[i];i++){
	                	//console.log(i)
	                    ri = tds[1].firstChild.childNodes[i];
	                    if(ri) {
	                        ri.style.height = li.style.height = ri.offsetHeight + 'px';
	                    }
	                }

	            }
	    	},10);
	    }
    	//编辑器初始化时，需要检查有没有需要高亮的代码
    	$(_self).on('ready.' + pluginName,function() {
    		wysiwyg();
    	});

    	$(_self).on('afterSetContent.' + pluginName,function(event,viewMode) {
			wysiwyg();
		});

    	//控件栏按钮的控制
    	$(_self.editorDoc.body).on('mousedown.' + pluginName,function(e) {
    		var syntaxDiv = $(e.target).closest('.syntaxhighlighter');
    		if( syntaxDiv.length ) {
    			setTimeout(function() {
    				_self.disableToolbar();
	    			plugin_icon.removeClass('disabled').addClass('activate');
    			},10);
    		}else {
    			_self.enableToolbar();
    			plugin_icon.removeClass('activate');
    		}
    	});
    	//删除代码增强
    	$(_self.editorDoc.body).on('keyup.' + pluginName, function(e){
    		var key = e.keyCode;
			if(key === 8 || key === 46){
				var element = _self.getRangeNode();
				element = element && element[0];//转为dom对象
				if(!element || !element.nodeType) { return; }
				var container = $(element).closest('.container');
				var text = container.text();
				if(text.replace(/\s+/,'') === ''){
					$(element).closest(".syntaxhighlighter").remove();
					_self.enableToolbar();
				}
			}
    	});
    	//html转[code]
    	$(_self).on('beforeGetContent.' + pluginName,function(event,viewMode) {
			//如果有高亮的代码，则转为原本的形式
			$(editorDoc.body).find('div.container').each(function() {

                for(var str = [],c = 0,ci;ci = this.childNodes[c++];){
                    str.push($(ci).text());
                }
                var container = $(this).closest('.syntaxhighlighter');
                 var code = str.join('\n'),
                	 className = container.attr('highlighter');
				code = code.replace(/[<>]/ig,function(s) {
					return {'<':'&lt;','>':'&gt;'}[s];
				});
				code = code.replace(/&nbsp;/ig,' ');
				container.replaceWith(editorDoc.createTextNode('[code '+ $.trim(className) +']'+ code +'[/code]'));
			});
		});

		/*$(_self).on('afterSetValue.' + pluginName,function(event,viewMode) {
			var val = _self.codeContainer.val();
			val = val.replace(/\[code\s*([^\]]*)\]([\s\S]*?)\[\/code\]/ig,function(all,$1){
				all = all.replace(/&lt;/ig, '<');
				all = all.replace(/&gt;/ig, '>');
				all = all.replace(/&nbsp;/ig, ' ');
				all = all.replace(/&amp;/ig, '&');
				return all;
			});
			setTimeout(function(){
				_self.codeContainer.val(val)
			},0);
		});*/

	});

})( jQuery, window);