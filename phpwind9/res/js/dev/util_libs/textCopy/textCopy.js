 /**
 * PHPWind util Library 
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 复制功能js（li列表或直接复制内容）
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later)、common.js、dialog.js、zeroClipboard组件
 * $Id: textCopy.js 3846 2012-01-13 02:56:43Z hao.lin $
 
 ***************************************************************
 
 * js引用代码：
	Wind.use('textCopy', 'dialog', function() {
		$('a.J_copy_clipboard').textCopy({
			content : '复制内容',
			callback : function(){
				//成功后回调
			}
		});
	});

	
 ***************************************************************
 */
;(function ( $, window, document, undefined ) {
   var pluginName = 'textCopy',
    	defaults = {
    		callback : undefined
    	};

	function Plugin(element, options) {
		this.element = element;
		this.options = $.extend( {}, defaults, options) ;
		this.init();
	}
	
    Plugin.prototype.init = function () {
		var element = this.element,
			options = this.options,
			callback = options.callback,			//回调
			mouseover = options.mouseover,
			content = options.content,				//待复制内容
			appendelem = (options.appendelem ? options.appendelem : undefined),		//添加容器
			addedstyle = (options.addedstyle ? options.addedstyle : undefined);		//手动修改样式
		
		if($.browser.msie) {
			//ie复制
			
			//点击复制按钮
			element.on('click', function(e){
				e.preventDefault();
				
				//判断内容是否为空
				if( content === '') {
					if(Wind.dialog){
						Wind.dialog.alert('复制内容为空');
					}else{
						//后台没有Wind.Util
						if(Wind.Util.resultTip) {
							Wind.Util.resultTip({
								error : true,
								elem : element,
								follow : true,
								msg : '复制内容为空'
							});
						}else{
							alert('复制内容为空');
						}
						
					}
					return false;
				}
				
				//完成复制
				if(window.clipboardData.setData("Text", content)) {
					//后台没有Wind.Util
					if(Wind.Util.resultTip) {
						Wind.Util.resultTip({
							elem : element,
							follow : true,
							msg : '复制成功'
						});
					}else{
						alert('复制成功');
					}
					

					if(callback) {
						callback(element);
					}
				}
				
			});
			
		}else{
			//非ie复制，引入zeroClipboard组件
			Wind.js(GV.JS_ROOT+ 'util_libs/textCopy/zeroClipboard/ZeroClipboard.js?v=' + GV.JS_VERSION, function(){
						
				element.clip = new ZeroClipboard.Client();
				ZeroClipboard.setMoviePath( GV.JS_ROOT + 'util_libs/textCopy/zeroClipboard/ZeroClipboard10.swf?v=' + GV.JS_VERSION); //flash文件地址
				element.clip.glue(element[0], appendelem, addedstyle); //flash定位到文字按钮上
				element.clip.setHandCursor( true ); //flash的鼠标手势
						
				//flash被点击，提交复制
				element.clip.addEventListener('mouseDown', function (client) {
						
					//判断复制内容是否为空
					if(content === '') {
						if(Wind.dialog){
							Wind.dialog.alert('复制内容为空');
						}else{
							//后台没有Wind.Util
							if(Wind.Util.resultTip) {
								Wind.Util.resultTip({
									error : true,
									elem : element,
									follow : true,
									msg : '复制内容为空'
								});
							}else{
								alert('复制内容为空');
							}
						}
						
						return false;
					}
						
					//开始复制
					element.clip.setText(content);
						
					
						
				});

				element.clip.addEventListener('mouseover', function (client) {
					if(mouseover) {
						mouseover(client);
					}
				});
				

				//完成复制
				element.clip.addEventListener('complete', function (client, text) {
					//后台没有Wind.Util
					if(Wind.Util.resultTip) {
						Wind.Util.resultTip({
							elem : element,
							follow : true,
							msg : '复制成功'
						});
					}else{
						alert('复制成功');
					}
						

					if(callback) {
						callback(element);
					}
				});
				
				//鼠标经过文字时以防被点击
				element.on('click', function(e){
					e.preventDefault();
				});
				
			});
			
		}
		
    };

		if(!$.isFunction(Wind.dialog)) {
			Wind.use('dialog');
		}

    $.fn[pluginName] = Wind[pluginName]= function (options ) {
      return this.each(function () {
				new Plugin( $(this), options );
      });
    };

})( jQuery, window ,document);
