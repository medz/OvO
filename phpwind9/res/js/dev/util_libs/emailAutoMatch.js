/**
 * PHPWind ui_libs Library
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 电子邮件自动匹配
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later)
 * $Id: emailAutoMatch.js 21812 2012-12-13 10:03:22Z hao.lin $
 */
 
;(function ($, window, document, undefined) {
	var pluginName = 'emailAutoMatch';
	var defaults = {
		 //未输入@前，部分匹配项
		emailDefaultArr : ['@aliyun.com', '@qq.com', '@163.com', '@yahoo.com.cn', '@hotmail.com', '@gmail.com'],
		
		//输入@后，全部匹配项
		emailAllArr : ['qq.com', '163.com', 'yahoo.com.cn', 'hotmail.com', 'gmail.com', 'yahoo.com', 'yahoo.cn', '126.com', 'yeah.com', 'live.com', 'aliyun.com'],
		
		//列表容器
		listWrapper : '#J_email_list'
	};
	
	var list_wrapper = $('<div style="position:absolute;background:#fff;z-index:10;" class="mail_down" id="J_email_list"></div>');
	
	function Plugin(element, options) {
		this.element = element;
		this.options = $.extend({}, defaults, options);
		this.emailDefault = this.options.emailDefaultArr;
		this.emailAll = this.options.emailAllArr;
		this.init();
	}
	
	Plugin.prototype = {
		init : function () {
			var _this = this,
				element = _this.element,
				options = _this.options,
				wrapper = $(_this.options.listWrapper),
				current_index;

			element.attr('autocomplete', 'off');
			
			//匹配项hover状态
			wrapper.on('mouseenter', 'li', function(){
				$(this).addClass('current');
			}).on('mouseleave', 'li', function(){
				$(this).removeClass('current');
			});
			
			
			//点击匹配项
			wrapper.on('click', 'a', function(e){
				e.preventDefault();
				element.val($(this).text());
			});
			
			
			element.on('focus click', function () {
				//输入聚焦，显示已匹配列表
				if(list_wrapper.children() && !list_wrapper.is(':visible') && $.trim(element.val()).length >= 2) {
					list_wrapper.show();
					_this.wrapPos(list_wrapper, element);
				}
			})
			.on('keyup', function (e) {
				//正在输入
			
				var v = $.trim(element.val()); //输入值
				
				//不足两个字符
				if (v.length < 2) {
					list_wrapper.hide();
					return;
				}
				
				//输入中文
				if(RegExp(/[^\x00-\xff]/).test(v)) {
					list_wrapper.hide();
					return;
				}
				
				//计算@符号出现次数
				var k = 0;
				$.each(v, function (i, o) {
					if (o === '@') {
						k++;
					}
				});
				
				//@符号出现两次及以上 则不匹配
				if (k >= 2) {
					list_wrapper.hide();
					return;
				}

				
				var item_length = list_wrapper.find('ul > li').length; //匹配项的总数
					current_index = list_wrapper.find('li.current').data('index'); //current项的index值
				
				if (e.keyCode === 38) {
					//按键向上
					
					if(!current_index || current_index <= 1) {
						//没有选中项
						current_index = item_length;
					}else{
						//有选中项
						current_index--;
					}
					
				}else if(e.keyCode === 40){
					//按键向下
					
					if(!current_index || current_index >= item_length) {
						current_index = 1;
					}else{
						current_index++;
					}
					
				}else{
					var li_arr = [];
					
					if (!/@/.test(v) || /@$/.test(v)) {
						//还没输入@或刚输入@
						
						$.each(_this.emailDefault, function (i, o) {
							li_arr.push('<li id="J_match_'+ (i+1) +'" data-index="'+ (i+1) +'"><a href="#">' + v.replace(/@/, '') + o + '</a></li>');
						});
						
					} else {
						//输入@后
						
						var atText = /@.*/.exec(v), //输出的@符号以后的内容，包括@
							reg = atText[0].toLowerCase().replace(/@/, ''); //替换@后的文本
						
						
						var j = 0;
						
						//循环匹配邮箱后缀
						$.each(_this.emailAll, function (i, o) {
						
							if (RegExp('^'+ reg).test(o)) {
								//匹配成立
								j++;
								li_arr.push('<li id="J_match_'+ Number(j) +'" data-index="'+ Number(j) +'"><a href="#">' + /.*@/.exec(v) + o + '</a></li>');
							}
							
						});
						
					}
					
					
					//有匹配结果，显示列表
					if (li_arr.length) {
						list_wrapper.html('<ul>' + li_arr.join('') + '</ul>').appendTo('body').show();
						list_wrapper.on('click', 'a', function(e){
							e.preventDefault();
							element.val($(this).text());
						});
						_this.wrapPos(list_wrapper, element);
					} else {
						list_wrapper.remove();
					}
				}
				
				//上下键移动选中项
				if(current_index) {
					$('#J_match_' +current_index).addClass('current').siblings().removeClass('current');
				}
				
			})
			.on('keypress', function(e){
				//按回车且有current项
				if(e.keyCode === 13 && current_index) {
					e.preventDefault();
					element.val($('#J_match_' +current_index).text());
					element.blur();
				}
			})
			.on('blur', function(){
				setTimeout(function(){
					list_wrapper.hide();
				}, 150);
			});

			//滚动隐藏列表，防止定位问题
			$(document).on('scroll', function(){
				if(list_wrapper.is(':visible')) {
					list_wrapper.hide();
				}
			});
			
		},
		wrapPos : function(wrap, elem){
			wrap.css({
				left : elem.offset().left,
				top : elem.offset().top + elem.innerHeight() +2,
				width : elem.innerWidth() + 2
			});
		}
		
	};
	
	
	$.fn[pluginName] = Wind[pluginName] = function (options) {
		return this.each(function () {
			new Plugin($(this), options);
		});
	};
	
})(jQuery, window, document);
