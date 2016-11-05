/*
 * PHPWind WindEditor Plugin
 * @Copyright 	: Copyright 2011, phpwind.com
 * @Descript	: 表情插入插件
 * @Author		: chaoren1641@gmail.com
 * @Depend		: jquery.js(1.7 or later)
 * $Id: windeditor.js 4472 2012-02-19 10:41:01Z chris.chencq $			:
 */
;(function ( $, window, undefined ) {

	var WindEditor = window.WindEditor;

	var pluginName = 'insertEmoticons';
		dialog = $('<div class="edit_menu">\
						<div class="edit_show_face">\
							<div class="edit_menu_top">\
								<a href="" class="edit_menu_close">关闭</a>\
								<ul>\
								</ul>\
							</div>\
							<div class="edit_menu_cont cc">\
								<div class="edit_show_loading">表情加载中...</div>\
							</div>\
						</div>\
					</div>');
	WindEditor.plugin(pluginName,function() {
		var _self = this, swfu, emotionsData;
		var editorDoc = _self.editorDoc = _self.iframe[0].contentWindow.document,
			plugin_icon = $('<div class="wind_icon" data-control="'+ pluginName +'" unselectable="on"><span class="'+ pluginName +'" title="插入表情" unselectable="on"></span></div>').appendTo( _self.pluginsContainer );

		var emotionData = null;
		plugin_icon.on('mousedown',function() {
			if($(this).hasClass('disabled')) {
				return;
			}
			if(!$.contains(document.body,dialog[0]) ) {
				dialog.appendTo( document.body );
				dialog.find('.edit_menu_top li').on('click',function(e) {
					e.preventDefault();
					var index = $(this).index();
					$(this).addClass('current').siblings().removeClass('current');
					var contents = dialog.find('div.edit_menu_cont');
					contents.hide();
					contents.eq(index).show();
				});

				//填充表情数据
				$.getJSON(EMOTION_URL,function(data) {
					emotionData = data;
					if(data.state !== 'success') { return; }
					if(!emotionData.data) {
						dialog.find('.edit_show_loading').text('没有获取到任何表情！');
						return;
					}
					var index = 0;
					dialog.find('.edit_menu_cont').remove();
					$.each(data.data,function(key,obj) {
						if(!obj) { return ;}
						index ++;
						var category = obj.category;
						var emotions = obj.emotion;
						var pageSize = 30;
						var pageCount = Math.ceil(emotions.length/pageSize);
						dialog.find('.edit_menu_top ul').append('<li class="'+ (index === 1?'current':'') +'"><a href="javascript:;">'+ category +'</a></li>');
						var emotion_box = $('<div class="edit_menu_cont cc" style="display:'+ (index === 1?'':'none') +'"><ul class="cc">'+ emotionsHtmlForPage(1) +'</ul></div>');
						emotion_box.appendTo(dialog.find('.edit_show_face'));

						if(pageCount > 1) {
							var page_box = $('<div class="edit_show_page"></div>').appendTo(emotion_box);
							for(var i = 1,j = pageCount;i <= j;i++) {
								var page_break = $('<a href="#" class="'+ (i===1?'current':'') +'">'+ i +'</a>').appendTo(page_box);
								//分页事件
								(function(i,page_break,emotion_box){
									page_break.on('click',function(e) {
										e.preventDefault();
										$(this).addClass('current').siblings().removeClass('current');
										emotion_box.find('ul').html(emotionsHtmlForPage(i));
										requestEmotionImg(emotion_box);
									});
								})(i,page_break,emotion_box);
							}
						}

						//点击表情插入
						emotion_box.on('click','li',function(e) {
							e.preventDefault();
							_self.insertHTML(this.firstChild.innerHTML);
							_self.hideDialog();
						});

						//填充表情分页图片
						function emotionsHtmlForPage(pageIndex) {
							var html = [],index = (pageIndex-1)*pageSize;//初始表情索引
							for(var i = index,j = emotions.length,k = 0;i < j && k < pageSize; i++, k++) {
								html.push('<li><a href="javascript:void(0);"><img class="J_emotion" data-src="'+ emotions[i].url +'" alt="'+ emotions[i].name +'" data-bbcode="'+ emotions[i].sign +'"></a></li>');
							}
							return html.join('');
						}

					});

					//表情图片的懒加载，需要的时候才发生请求
					function requestEmotionImg(box) {
						box.find('img[data-src]').prop('src',function () {
							return $(this).attr('data-src');
						}).removeAttr('data-src');
					}

					requestEmotionImg(dialog.find('div.edit_menu_cont').eq(0));

					//tab选项卡
					var navList = dialog.find('.edit_menu_top li');
						contentList = dialog.find('.edit_menu_cont');
					navList.attr('role','tab').parent().attr('role','tablist');
			        contentList.attr({'role':'tabpanel','aria-hidden':'true'});
					navList.on('click',function(e) {
						e.preventDefault();
						var index = $(this).index();
						var selected_element = navList.eq(index);
			    		selected_element.addClass('current').siblings().removeClass('current');
			    		contentList.hide().attr('aria-hidden','true');
			    		contentList.eq(index).show().attr('aria-hidden','false');
			    		requestEmotionImg(contentList.eq(index));
					});
				});
			}
			_self.showDialog(dialog);
		});

		//弹窗的关闭事件
		dialog.find('a.edit_menu_close').on('click',function(e) {
			e.preventDefault();
			_self.hideDialog();
		});

		function wysiwyg() {
			//var reg = /\[s:(\d{1,3})\]/ig;
			var reg= /\[s:([^\][]*)\]/ig; //仅过滤[]符号

			//把表情相关的bbcode变为图片
			function showEmotions() {
				if(!emotionData.data) { return; }
				var index = 0,allEmotionsArr = [];
				//把多个分里的表情放在一个数组里来，以便通过bbcode查找它的图片链接地址
				$.each(emotionData.data,function(key,obj) {
					if(!obj) { return; }
					allEmotionsArr = allEmotionsArr.concat(obj.emotion);
				});
				//替换ubb到图片
				var	html = $(editorDoc.body).html();
				if(!reg.test(html)) {
					return;
				}
				html = html.replace(reg,function(all, $1) {
					var bbcode = '[s:'+ $1 +']';
					var result;
					$.each(allEmotionsArr,function(id,emotion) {
						if(emotion['sign'] === bbcode) {
							result =  '<img class="J_emotion" src="'+ emotion.url +'" alt="'+ emotion.name +'" data-bbcode="'+ $1 +'" />';
						}
					});
					return result ? result : all;
				});
				$(editorDoc.body).html(html);
			}

			//如果数据没有加载进来，则加载，否则直接渲染
			if(emotionData) {
				showEmotions();
			}else {
				$.getJSON(EMOTION_URL,function(data) {
					emotionData = data;
					showEmotions();
				});
			}
		}

		//加载插件时把ubb转换成可见即所得
		$(_self).on('ready.' + pluginName,function() {
			wysiwyg();
		});

		$(_self).on('afterSetContent.' + pluginName,function(event,viewMode) {
			wysiwyg();
		});

		$(_self).on('beforeGetContent.' + pluginName,function() {
			$(editorDoc.body).find('img.J_emotion').each(function() {
				var bbcode = $(this).attr('data-bbcode'),
					reg = /(^\[s:)|(\]$)/g; //匹配带[s:  ]符号的表情
				
				if(!reg.test(bbcode)) {
					$(this).replaceWith('[s:'+ bbcode +']');
				}else{
					$(this).replaceWith(bbcode);
				}
			});
		});
	});


})( jQuery, window);