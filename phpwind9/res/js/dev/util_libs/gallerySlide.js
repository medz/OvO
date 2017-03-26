/*
 * PHPWind util Library
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-幻灯片
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later)
 * @Example	: 首页焦点图
 * $Id: jquery.slidePlayer.js 6032 2012-03-15 09:43:30Z hao.lin $
 */

;(function($, window, document, undefined) {
	var pluginName = 'gallerySlide',
		defaults = {},
		win = $(window),
		body = $('body'),
		body_height = body.height();

	var template = '<div id="J_gallery_mask" class="pImg_bg" style="height:' + body_height + 'px;"></div>\
<div id="J_gallery_pop" tabindex="0" class="pImg_wrap" style="display:none;"><table border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="bcr1"></td><td class="pibg1"></td><td class="bcr2"></td></tr><tr><td class="pibg4"></td><td><div class="pImg tac">\
<div class="cc w" style="padding:0 5px;">\
  <div class="fl mr10" id="J_gallery_page">第<span id="J_gallery_count_now"></span>张/共<span id="J_gallery_count_total"></span>张</div><!--a href="javascript:;" class="fl mr20" onclick="readImg.viewAll()">原图</a-->\
  <a href="" class="pImg_close" id="J_gallery_close">关闭</a>\
</div>\
<div id="J_gallery_wrap" class="imgLoading" style="margin:auto;">\
<div class="aPre" id="J_gallery_prev" title="上一张"></div>\
<div class="aNext" id="J_gallery_next" title="下一张"></div>\
</div>\
</div><img style="display:none;" id="J_gallery_clone" /></td><td class="pibg2"></td></tr><tr><td class="bcr4"></td><td class="pibg3"></td><td class="bcr3"></td></tr></tbody></table></div>';
	var imgReady = (function () {
		var list = [], intervalId = null,
	
		// 用来执行队列
		tick = function () {
			var i = 0;
			for (; i < list.length; i++) {
				list[i].end ? list.splice(i--, 1) : list[i]();
			};
			!list.length && stop();
		},
	
		// 停止所有定时器队列
		stop = function () {
			clearInterval(intervalId);
			intervalId = null;
		};
	
		return function (url, ready, load, error) {
			var onready, width, height, newWidth, newHeight,
				img = new Image();
			
			img.src = url;
	
			// 如果图片被缓存，则直接返回缓存数据
			if (img.complete) {
				ready.call(img);
				load && load.call(img);
				return;
			};
			
			width = img.width;
			height = img.height;
			
			// 加载错误后的事件
			img.onerror = function () {
				error && error.call(img);
				onready.end = true;
				img = img.onload = img.onerror = null;
			};
			
			// 图片尺寸就绪
			onready = function () {
				newWidth = img.width;
				newHeight = img.height;
				if (newWidth !== width || newHeight !== height ||
					// 如果图片已经在其他地方加载可使用面积检测
					newWidth * newHeight > 1024
				) {
					ready.call(img);
					onready.end = true;
				}

			};
			onready();
			
			// 完全加载完毕的事件
			img.onload = function () {
				// onload在定时器时间差范围内可能比onready快
				// 这里进行检查并保证onready优先执行
				!onready.end && onready();
			
				load && load.call(img);
				
				// IE gif动画会循环执行onload，置空onload即可
				img = img.onload = img.onerror = null;
			};
	
			// 加入队列中定期执行
			if (!onready.end) {
				list.push(onready);
				// 无论何时只允许出现一个定时器，减少浏览器性能损耗
				if (intervalId === null) intervalId = setInterval(tick, 40);
			};
		};
	})();
	function Plugin(element, options) {
		this.element = element;
		this.options = $.extend({}, defaults, options);
		this.total = element.children('.J_gallery_items').length, this.init();
	}

	Plugin.prototype = {
		init: function() {
			var element = this.element,
				options = this.options,
				_this = this;

			//点击相册
			element.on('click', 'a[data-big]', function(e) {
				e.preventDefault();
				var $this = $(this),
					index = $this.parent().index(),
					win_height = $(window).height();

				//图片不存在
				if ($this.children().attr('class') == 'J_error') {
					return;
				}

				body.append(template);

				var gallery_pop = $('#J_gallery_pop');

				//gallery_pop
				//添加预览图
				var preview = $('<img id="J_gallery_preview" src="' + $this.data('big') + '" align="absmiddle" data-index="' + index + '" />');
				preview.appendTo("#J_gallery_wrap");
				_this.sizeReset(preview);
				var gallery_prev = $('#J_gallery_prev'),
					//上一张
					gallery_next = $('#J_gallery_next'),
					//下一张
					gallery_page = $('#J_gallery_page'); //张数
				//只有一张
				if (_this.total === 1) {
					gallery_prev.hide();
					gallery_next.hide();
					gallery_page.hide();
				} else {
					gallery_prev.show();
					gallery_next.show();
					gallery_page.show();
				}

				gallery_prev.on('click', function() {
					if (_this.total > 1) {
						_this.showSibling(preview, 'prev');
					}
				});

				gallery_next.on('click', function() {
					if (_this.total > 1) {
						_this.showSibling(preview, 'next');
					}
				});
				gallery_pop.on('keydown', function(e) {
					var key = e.keyCode;
					if (_this.open && key === 27) {
						_this.popHide(gallery_pop);
						return;
					}
					if (_this.open && key === 37) {
						gallery_prev.click();
					} else if (_this.open && key === 39) {
						gallery_next.click();
					} else {
						return;
					}
				});

				$('#J_gallery_count_now').text(index + 1);
				$('#J_gallery_count_total').text(_this.total);

				_this.popClose();

			});

			//窗口改变
			win.resize(function() {
				_this.sizeReset($('#J_gallery_preview'));
			});
		},
		sizeReset: function(preview) {
			if (!preview.length) {
				return;
			}
			var gallery_wrap = $('#J_gallery_wrap'),
				win_height = win.height(),
				max_height = win.height() - 100,
				max_width = win.width() - 100,
				_this = this,
				imgUrl = preview.attr('src');
			//初始化图片尺寸
			imgReady(imgUrl, function(){
				var w = this.width,
					h = this.height,
					ratio = w / h;

				if (h > max_height) {
					gallery_wrap.height(max_height);
					gallery_wrap.width(max_height * ratio);
				}else if (w > max_width) {
					gallery_wrap.width(max_width);
					gallery_wrap.height(max_width / ratio);
				}else{
					//重置不符合上面判断图片的尺寸，防止受到上一张图片影响
					gallery_wrap.width(w);
					gallery_wrap.height(h);
				}
				_this.imgpos();
			});
		},
		imgpos: function() {
			this.open = true;
			//定位
			var ie6 = false,
				gallery_pop = $('#J_gallery_pop'),
				win_height = win.height(),
				wrap_height = gallery_pop.outerHeight();

			if ($.browser.msie && $.browser.version < 7) {
				ie6 = true;
			}

			var top = ($(window).height() - gallery_pop.outerHeight()) / 2;

			gallery_pop.css({
				top: top + (ie6 ? $(document).scrollTop() : 0),
				left: ($(window).width() - gallery_pop.innerWidth()) / 2
			}).show().focus();

			$('#J_gallery_mask').css('height', $(document).height());
		},
		showSibling: function(preview, type) {
			//前后
			var index = preview.data('index'),
				_index, element = this.element,
				total = this.total,
				_this = this,
				item;

			if (type == 'next') {
				_index = ((index + 2) > total ? 0 : (index + 1));
			} else {
				_index = (index === 0 ? (total - 1) : (index - 1));
			}

			$('#J_gallery_count_now').text(_index + 1);
			item = element.children(':eq(' + _index + ')').find('a[data-big]');
			window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty();
			preview.attr('src', item.data('big')).data('index', _index);
			_this.sizeReset(preview);
		},
		popClose: function() {
			//关闭
			var gallery_pop = $('#J_gallery_pop'),
				mouse_in = false,
				_this = this;

			gallery_pop.hover(function() {
				mouse_in = true;
			}, function() {
				mouse_in = false;
				gallery_pop.focus();
			});

			//幻灯片失焦 隐藏
			gallery_pop.blur(function() {
				if (!mouse_in) {
					_this.popHide(gallery_pop);
				}
			});

			//点关闭
			$('#J_gallery_close').on('click', function(e) {
				e.preventDefault();
				_this.popHide(gallery_pop);
			});
		},
		popHide: function(pop) {
			this.open = false;
			pop.remove();
			$('#J_gallery_mask').remove();
			$('#J_gallery_clone').remove();
		}
	};

	$.fn[pluginName] = function(options) {
		return this.each(function() {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName, new Plugin($(this), options));
			}
		});
	}

})(jQuery, window, document);