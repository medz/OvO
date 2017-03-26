/*!
 * PHPWind UI Library
 * @Copyright 	: Copyright 2011, phpwind.com
 * @Descript	: dialog 对话框组件
 * @Author		: chaoren1641@gmail.com
 * @Depend		: core.js、jquery.js(1.7 or later)
 * $Id: dialog.js 23900 2013-01-17 03:48:52Z hao.lin $			:
 */
;(function ( $, window, undefined ) {
    var pluginName = 'dialog';
    var	empty = $.noop;
    var is_ie6 = ($.browser.msie && $.browser.version < 7) ? 1 : 0;
    var defaults = {
            id              : '',                           //id
            type            : 'alert',						// 默认弹出类型
			className		: 'wind_dialog core_pop_wrap',	//弹出容器默认class
			position		: 'absolute',
            message			: '',							// 弹出提示的文字
            autoHide		: 0,							// 是否自动关闭
            zIndex			: 10, 							// 层叠值
            width			: '',							// 弹出内容的宽度
            height			: '',							// 高度
            isDrag			: false,							// 是否允许拖拽
			callback		: undefined,					//回调
            onShow			: undefined,					// 显示时执行
            onOk			: undefined,
            onCancel		: undefined, 					// 点击取消时执行
            onClose			: undefined,					// 如果是iframe或者html,则有一个关闭的回调
            left			: undefined,					// 默认在中间
            top				: undefined,
            follow			: undefined,
            title			: '',							// 提示标题
            okText			: '确定',						// 确定按钮文字
            cancelText		: '取消',						// 取消文字，确认时用
            closeText		: '关闭',						// 关闭文字
            isMask			: 1,							// 是否显示背景遮罩
            opacity			: 0.6,							// 遮罩的透明度
            backgroundColor	: '#fff',						// 遮罩的背景色
            url				: '',							// 弹出来的iframe url
            resize			: true							// 监听窗口变化
    };
    var template = '\
				<div class="core_pop">\
					<% if(type === "iframe" || type === "html") {%>\
						<div class="pop_top J_drag_handle" style="display:none;overflow:hidden;">\
							<a role="button" href="#" class="pop_close J_close" title="关闭弹出窗口">关闭</a>\
							<strong><%=title%></strong>\
						</div>\
					<% } %>\
					<% if(type === "iframe") { %>\
							<div class="pop_loading J_loading fl"></div>\
							<div class="J_dialog_iframe">\
                        		<iframe src="<%=url%>" frameborder="0" style="border:0;height:100%;width:100%;padding:0;margin:0;display:none;" scrolling="no"/>\
                        	</div>\
                    <% } else if(type === "confirm" || type === "alert"){ %>\
						<div class="pop_cont">\
	                    	<%=message%>\
						</div>\
						<div class="pop_bottom">\
							<% if(type === "confirm" || type === "alert") { %>\
								<button type="button" class="btn btn_submit mr10 J_btn_ok"><%=okText%></button>\
							<% } %>\
							<% if(type === "confirm") { %>\
								<button type="button" class="btn J_btn_cancel"><%=cancelText%></button>\
							<% } %>\
						</div>\
					<% } else if(type === "html") { %>\
						<%=message%>\
					<% } %>\
			</div>';

    function Plugin( options ) {
        //this.element = element;
        this.options = $.extend( {}, defaults, options) ;
        this.elem = null;
        this.init();
    }

    Plugin.prototype.init = function () {
    	var options = this.options;
        var html = Wind.tmpl(template,options);//替换模板
        if(options.type === 'confirm' && options.id === '') {
            options.id = 'wind_dialog_confirm';
        }
        var elem = (options.id ? $('#' + options.id) : '');     //TODO: id为空&存在<input name="">时，ie6下会报错
        var _this = this;
        if(elem.length) {
            //有设置id，只弹出一个
            elem.html(html).show();

            if(options.isDrag) {
                //去掉保存过的插件信息并重新绑定
                elem.removeData('plugin_draggable');
                elem.draggable( { handle : '.J_drag_handle'} );
            }
        }else {
            elem = $( '<div tabindex="0" id="'+ options.id +'" class="'+ options.className +'" aria-labelledby="alert_title" role="alertdialog" style="display:none"/>' ).appendTo( 'body' ).html(html);
        }
        this.elem = elem;
        var pop_top = elem.find('.pop_top'),//标题
        	ok_btn = elem.find('.J_btn_ok'),//确定按钮
        	calcel_btn = elem.find('.J_btn_cancel'),//取消按钮
        	close_btn = elem.find('.J_close');//关闭按钮

        if(options.isMask) {//遮罩
    		var style = {
				width			: '100%',
				height			: $(window.document).height() + 'px',
				opacity			: options.opacity,
				backgroundColor	: options.backgroundColor,
				zIndex			: options.zIndex-1,
				position		: 'absolute',
				left			: '0px',
				top				: '0px'
			};
    		_this.mask = $('<div class="wind_dialog_mask"/>').css(style).appendTo('body');
            //ie6则调用bgiframe组件
            if (is_ie6) {
                Wind.use('bgiframe',function() {
                    _this.mask.bgiframe();
                });
            }
    	}
        //宽度
        if(options.width) {
            elem.css('width',options.width+'px');
        }
        if(options.height) {
            elem.css('height',options.height+'px');
        }
        //高度
        //options.autoHide
        if(options.autoHide) {
        	setTimeout(function() {
        		_this.close();
        	},autoHide);
        }


        //点击确定
        ok_btn.on('click',function(e) {
        	e.preventDefault();
			if(options.onOk) {
		        options.onOk();
		   }
		   _this.close();
        });

        //confirm取消按钮点击
        calcel_btn.on('click',function(e) {
        	e.preventDefault();
        	if(options.onCancel) {
                options.onCancel();
           	}
           _this.close();
        });

        if(options.type === 'iframe' || options.isDrag) {
        	Wind.use('draggable',function() {
        		elem.draggable( { handle : '.J_drag_handle'} );
        	});
        }

        //关闭按钮
        close_btn.on('click',function(e) {
        	e.preventDefault();
        	if(options.onClose) {
                options.onClose();
           	}
           _this.close();
        });

        //按ESC关闭
        $(document.body).on('keydown',function(e) {
            if(e.keyCode === 27) {
                if(options.onClose) {
                    options.onClose();
                }
               _this.close();
            }
        });
        //如果是iframe，则监听onload，让展示框撑开
        if(options.type === 'iframe' && options.url) {
        	var iframe = elem.find('iframe')[0],
        		loading = elem.find('.J_loading');
        	try {
        		$(iframe).load( function() {
        			/*var body;
					if ( iframe.contentDocument ) { // FF
						body = iframe.contentDocument.getElementsByTagName('body')[0];
					} else if ( iframe.contentWindow ) { // IE
						body = iframe.contentWindow.document.getElementsByTagName('body')[0];
					}*/

					//firefox下，iframe隐藏的情况下取不到文档的高度
					$(iframe).show();
					loading.hide();
					pop_top.show();
                    try{
    					var body = iframe.contentWindow.document.body;
        				var width = $(body).width(),
        					height = $(body).height();

        				//小于200证明没有取到其宽度，宽度需要在页面的body中定义
        				if(width < 200) {
        					width = 700;
        				}
        				if( height > 600 ) {
    	        			height = 600;
    	        			iframe.scrolling = 'yes';
    	        		}
                        //在chorme下，iframe高度默认为150

                        /* 防止ie6的宽度过大*/
                        elem.find('.J_drag_handle').css({width : Math.max(width,300) + 'px'});

                        elem.find('.J_dialog_iframe').css( {width : Math.max(width,300) + 'px', height : Math.max(height,150) + 'px' });

                    }catch(e) {
                        $(iframe).css( {width : '800px', height : '600px' });elem.find('.J_drag_handle').css( width, 435);
                        loading.hide();
                        pop_top.show();
                        iframe.scrolling = 'yes';
                        $(iframe).show();
                    }
        			show();
	        	});
        	} catch(e) {
                throw e;
        	}

        }
        if(options.type === 'html' && options.title) {
            var width = elem.width();
            elem.css('width',width + 'px')
            pop_top.show();
        }
        //ie6则调用bgiframe组件
        if (is_ie6) {
    		Wind.use('bgiframe',function() {
    			elem.bgiframe();
    		});
    	}

        function show() {
        	var follow_elem = options.follow,
	        	top,
	        	left,
	        	position = (is_ie6 ? 'absolute' : options.position),	//ie6 绝对定位
	        	zIndex = options.zIndex;
        	if(options.follow) {
        		var follow_elem = typeof options.follow === 'string' ? $(options.follow) : options.follow,
        			follow_elem_offset = follow_elem.offset(),
	        		follow_elem_width = follow_elem.width(),
	        		follow_elem_height = follow_elem.height() ,
	        		win_width = $(window).width(),
					body_height = $(document.body).height(),
	        		win_height = $(window).height(),
        			pop_width = elem.outerWidth(true), //计算边框
        			pop_height = elem.outerHeight(true); //计算边框
        		//如果是跟随某元素显示，那么计算元素的位置，并不能超过显示窗口的区域
        		if((follow_elem_offset.top + follow_elem_height + pop_height) > body_height) {
        			top = follow_elem_offset.top - pop_height;	//高度超出
        		} else {
        			top = follow_elem_offset.top + follow_elem_height;
        		}
        		if((follow_elem_offset.left + follow_elem_width + pop_width) > win_width) {
					left = win_width - pop_width - 1; //多减1px IE保险
        		} else {
        			left = follow_elem_offset.left + follow_elem_width;
        		}
        	} else {
                top = options.top ? options.top : ( $(window).height() - elem.height() ) / 2 + (position=='absolute' ? $(window).scrollTop() : 0);
                left = options.left ? options.left : ( $(window).width() - elem.width() ) / 2 + $(window).scrollLeft() ;
	    	}
	    	//设置最终位置
	    	elem.css( {position:position, zIndex:zIndex, left:left + 'px', top:top + 'px'} ).show();
    	}

        //init event
        if(options.onShow) {
            options.onShow();
        }

    	//如果是确认框，则让确定按钮取得焦点
        if(options.type === 'confirm') {
            if( is_ie6 ) {
                elem.css({width:'200px',height:'90px'});
            }
        	ok_btn.focus();
        }else{//非confirm监听窗口变化，重新定位窗口位置
            var resizeTimer;
            $(window).on('resize scroll',function() {
            	if(!options.resize) {
            		return;
            	}
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if(elem.is(':visible')) {
                        show();
                    }
                },100);
            });
        	elem.focus();//显示以后让其取得焦点
        }

        show();

        //载入后回调
        if(options.callback) {
            options.callback();
        }

    };

	Plugin.prototype.close = function() {
		this.elem.remove();
		this.mask && this.mask.remove();
	};

    /*$.fn[pluginName] = Wind[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin( this, options ));
            }
        });
    }*/
   	var Wind = window.Wind || {};
	var dialog = Wind[pluginName] = function(options) {
		return new Plugin( options );
	};

	dialog['alert'] = Wind['alert'] = function(message,callback) {//兼容api
		return new Plugin( { message:message, type:'alert', onOk:callback } );
	};
	dialog['confirm'] = Wind['confirm'] = function(message,okCallback,cancelCallback) {
		if(arguments.length === 1 && $.isPlainObject(arguments[0])) {
			return new Plugin( arguments[0] );
		}
		return new Plugin( { message:message, type:'confirm',onOk:okCallback ,onCancel:cancelCallback} );
	};
	dialog['open'] = Wind['showUrl'] = function(url,options) {
        options = options || {};
		options['type'] = 'iframe';
		options['url'] = url;
		return new Plugin( options );
	};
	dialog['html'] = Wind.showHTML = function(html,options) {
        options = options || {};
		options['type'] = 'html';
		options['message'] = html;
		return new Plugin( options );
	};
	dialog['closeAll'] = function() {
		$('.wind_dialog').remove();
		$('.wind_dialog_mask').remove();
	};
})( jQuery, window);
