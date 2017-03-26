/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-阅读页_常用交互
 * @Author	: linhao87@gmail.com, TID
 * @Depend	: jquery.js(1.7 or later), global.js
 * $Id$
 */

;
(function(){
	//图片附件显示 删除
	$('span.J_attach_img_wrap').hover(function(){
		var $this = $(this);
		$this.find('.J_img_info').show().css({
			left : $this.offset().left,
			top : $this.find('img.J_post_img').offset().top
		});
	}, function(){
		$(this).find('.J_img_info').hide();
	});

	$('a.J_read_img_del').on('click', function(e){
		e.preventDefault();
		var $this = $(this);

		//glbal.js
		Wind.Util.ajaxConfirm({
			href : this.href,
			elem : $this,
			callback : function(){
				$this.parents('.J_attach_img_wrap').fadeOut(function(){
					$(this).remove();
				});
			}
		});
	});

})();

//显示喜欢过的人
(function(){
	$('a.J_like_user_btn').on('click', function(e){
		e.preventDefault();
		var $this = $(this),
			pid = $this.data('pid'),
			like_user_pop = $('#J_like_user_pop_'+ pid);

		//是否已存在下拉
		if(like_user_pop.length) {
			//下拉是否可见
			if($('#J_like_user_pop_'+ pid +':visible').length) {
				like_user_pop.hide();
			}else{
				like_user_pop.show();
			}

		}else{
			$.post($this.attr('href'), function(data){
				if(data.state === 'success') {
					var data = data.data,
						li_arr = [],
						template = $($('#J_like_user_ta').text()),
						this_offset_top = $this.offset().top,
						this_height = $this.innerHeight(),
						this_window_top = this_offset_top - $(document).scrollTop(),				//到窗口顶部距离
						this_window_bottom = $(window).height() - this_window_top - this_height,	//到窗口底部距离
						temp_top;

					$.each(data, function(i, o){
						li_arr.push('<li><a href="'+ GV.U_CENTER + o.uid +'"><img class="J_avatar" data-type="small" src="'+ o.avatar +'" width="30" height="30" />'+ o.username +'</a></li>');
					});

					template.appendTo('body').attr('id', 'J_like_user_pop_'+ pid).find('ul.J_like_user_list').html(li_arr.join(''));

					if (this_window_bottom < template.outerHeight()) {
						//底部空间不足，显示在上面
						temp_top = this_offset_top - template.outerHeight();
					}else{
						temp_top = this_offset_top + this_height;
					}

					//写入位置
					template.css({
						top : temp_top,
						left : $this.offset().left
					});

					Wind.Util.avatarError(template.find('img.J_avatar'));

					//绑定关闭
					$('a.J_like_user_close').on('click', function(e){
						e.preventDefault();
						template.hide();
					});

				}else if(data.state === 'fail'){
					//global.js
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
				}
			}, 'json');
		}
	});

})();

//发帖下拉
(function(){
	Wind.Util.hoverToggle({
		elem : $('#J_read_post_btn'),			//hover元素
		list : $('#J_read_post_types'),			//下拉菜单
		callback : function(elem, list){
			list.css({
				left : elem.offset().left,
				top : elem.offset().top + elem.height()
			});
		}
	});

	//只看楼主
	Wind.Util.hoverToggle({
		elem : $('#J_read_moredown'),			//hover元素
		list : $('#J_read_moredown_list'),		//下拉菜单
		callback : function(elem, list) {
			list.css({
				left : elem.offset().left + elem.width() - list.outerWidth(),
				top : elem.offset().top + elem.height()
			});
		}
	});

})();

//阅读回复
(function(){
	Wind.use('localStorage',function() {
		Wind.Util.LocalStorage.remove('quickReply');
	});

	//本地存储快速回复
	function quickStorage($ele){
		Wind.use('localStorage',function() {
			var set = function() { 
				//不支持placeholder容错处理
				var val = $ele.val();
				if(document.createElement('input').placeholder !== ''){
					if(val === $ele.attr("placeholder")){
						return;
					}
				}
				
				Wind.Util.LocalStorage.set('quickReply',val);
			};
			if($.browser.msie) {
				$ele[0].onpropertychange = function(event) {
				    set();
				}
			}else {
				$ele.on('input',set);
			}
		});
	}

	//主楼快速回复
	var reply_quick_ta = $('#J_reply_quick_ta'),
		reply_quick_btn = $('#J_reply_quick_btn'),
		reply_ft = $('#J_reply_ft'),
		floor_reply = $('#floor_reply'); //回复层

	Wind.Util.buttonStatus(reply_quick_ta, reply_quick_btn);
	Wind.Util.ctrlEnterSub(reply_quick_ta, reply_quick_btn);

	//主楼回复
	$('#J_readreply_main').on('click', function(e){
		e.preventDefault();
		location.hash = $(this).data('hash');
		reply_quick_ta.focus()
	});

	//回复框聚焦
	reply_quick_ta.on('focus', function() {
		//需要记录用户的输入，点击进入高级模式时需要
		quickStorage(reply_quick_ta);
	});
	
	//楼层快速回复框自动保存数据
	$(document).on('focus', '.J_at_user_textarea', function(){
		quickStorage($(this));
	})
	
	//提交回复
	reply_quick_btn.on('click', function(e){
		e.preventDefault();
		//清除本地存储
		if(Wind.Util.LocalStorage && Wind.Util.LocalStorage.get('quickReply') !== null){
			Wind.Util.LocalStorage.remove('quickReply');
		}
		//end
		var $this = $(this);
		//global.js
		Wind.Util.ajaxBtnDisable($this);

		$.post($(this).data('action'), {
			atc_content : reply_quick_ta.val(),
			tid : $(this).data('tid')
		}, function(data){
			//global.js
			Wind.Util.ajaxBtnEnable($this, 'disabled');
			if (Wind.Util.ajaxTempError(data, $this)) {
				if(data.indexOf('审核') > 0) {
					reply_quick_ta.val('');
					$('#J_emotions_pop').hide();
				}
				return false;
			}

			if($('#J_need_reply').length) {
				//回复可见
				Wind.Util.reloadPage(window);
			}

			reply_quick_ta.val('');
			floor_reply.before(data);
			$('#J_emotions_pop').hide();
			//高亮代码start
			var highlightFunc = function(){
				var nextFloor = floor_reply.prevAll('.J_read_floor').eq(0);
				var codes = $('pre[data-role="code"]', nextFloor);
				if(codes.length) {
					codes.each(function(){
						//console.log(this)
						HighLightFloor.addCopy(this);
					});
					HighLightFloor.render();
					$(".syntaxhighlighter").each(function(){
						HighLightFloor.adjust(this);
					});
				}
			};
			var nextFloor = floor_reply.prevAll('.J_read_floor').eq(0);
			//保证当HighLightFloor存在的时候才会渲染，防止当文件变更等原因导致报错
			if(typeof HighLightFloor !== 'undefined'){
				if(HighLightFloor.active === true){
					highlightFunc();
				}else{
					HighLightFloor.init(highlightFunc);
				}
			}
			//高亮end
			
			var new_floor = floor_reply.prev();

			//回复楼的喜欢
			Wind.js(GV.JS_ROOT+ 'pages/common/likePlus.js?v='+ GV.JS_VERSION, function () {
				likePlus(new_floor.find('a.J_like_btn'));
			});
			
			//头像
			Wind.Util.avatarError(new_floor.find('img.J_avatar'));

			//积分提示
			Wind.Util.creditReward();
			location.hash = new_floor.attr('id');
		});
	});


	//查看回复
	var lock = false,
		posts_list = $('#J_posts_list');

	posts_list.on('click', 'a.J_read_reply', function(e){
		e.preventDefault();
		var $this = $(this),
			pid = $this.data('pid'),
			topped = $this.data('topped'),
			wrap = $('#J_reply_wrap_'+ pid + (topped ? '_topped' : ''));			//列表容器

		wrap.toggle();

		//锁定 或 已请求
		if(lock || $this.data('load')) {
			wrap.find('.J_at_user_textarea').val('').focus();
			return false;
		}
		lock = true;

		$.post(this.href, function(data){
			//global.js
			lock = false;
			if(Wind.Util.ajaxTempError(data))	{
				return false;
			}

			wrap.html(data);
			$this.data('load', true); //已请求标识

			replyFn(wrap);

			//ie6初次展开不聚焦
			wrap.find('textarea').focus();

			Wind.Util.avatarError(wrap.find('img.J_avatar'));
			
		});
	});

	
	posts_list.on('click', 'a.J_insert_emotions' ,function(e){
		//表情
		e.preventDefault();
		var $this = $(this);
		Wind.js(GV.JS_ROOT +'pages/common/insertEmotions.js?v='+ GV.JS_VERSION, function(){
			insertEmotions($this, $($this.data('emotiontarget')));
		});
	}).on('click', 'a.J_read_reply_single' ,function(e){
		//回复单个
		e.preventDefault();
		//var wrap = $(this).parents('div.J_reply_wrap'),
		var wrap = $(this).parents('.J_reply_wrap'),
				username = $(this).data('username'),
				textarea = wrap.find('textarea');

			textarea.focus().val('@'+ username +'：');
			if(!$.browser.msie) {
				//chrome 光标定位最后
				textarea[0].setSelectionRange(100,100);
			}
	}).on('click', 'button.J_reply_sub' ,function(e){
		//提交
		e.preventDefault();
		var $this = $(this),
			pid = $this.data('pid'),
			par = $this.parents('.J_reply_wrap'),
			textarea = par.find('textarea'),
			list = par.find('.J_reply_page_list ul');

		//global.js
		Wind.Util.ajaxBtnDisable($this);

		$.post($(this).data('action'), {
			atc_content : textarea.val(),
			tid : TID,
			pid : pid
		}, function(data){
			//global.js
			Wind.Util.ajaxBtnEnable($this, 'disabled');

			if(Wind.Util.ajaxTempError(data)) {
				/*textarea.val('');
				$this.addClass('disabled').prop('disabled', true);
				$('#J_emotions_pop').hide();*/
				if(data.indexOf('审核') > 0) {
					textarea.val('');
					$('#J_emotions_pop').hide();
				}
				return false;
			}

			if($('#J_need_reply').length) {
				//回复可见
				location.reload();
			}

			list.prepend(data);
			textarea.val('');
			$('#J_emotions_pop').hide();

			//积分奖励
			Wind.Util.creditReward();
			
		});
	}).on('click', 'div.J_pages_wrap a' ,function(e){
		//翻页
		e.preventDefault();
		var list = $(this).parents('.J_reply_page_list'),
				clone = list.clone();

		//跳楼
		
		list.html('<div class="pop_loading"></div>');

		$.post(this.href, function(data){
			if(Wind.Util.ajaxTempError(data)) {
				//失败则恢复原内容
				list.html(clone.html());
				return false;
			}

			list.html(data);
		})
	});


	//回复列表公共方法
	function replyFn(wrap){
		var btn = wrap.find('button.J_reply_sub'),
			ta = wrap.find('textarea');
		Wind.Util.buttonStatus(ta, btn);
		Wind.Util.ctrlEnterSub(ta, btn);
		ta.focus();
	}

})();

//阅读页话题
(function(){
	var tag_temp_arrow = '<div class="arrow"><em></em><span></span></div>';
	var read_tag_item = $('a.J_read_tag_item');

	read_tag_item.each(function(){
		var $this = $(this);

		Wind.Util.hoverToggle({
			elem : $this,		//hover元素
			list : $this.next('.J_tag_card'),
			callback : function(elem, list){
				//定位
				list.css({
					left : elem.offset().left,
					top : elem.offset().top + elem.innerHeight() + 5
				});

				if(!elem.data('load')) {
					//未请求内容
					elem.data('load', true);
					$.post(elem.data('url'), function(data){
						if(Wind.Util.ajaxTempError(data)) {
							elem.data('load', false);
							return;
						}

						list.html(tag_temp_arrow + data);

						//关注&取消
						var lock = false;
						list.find('a.J_read_tag_follow').on('click', function(e){
							e.preventDefault();
							var $this = $(this),
								id = $this.data('id'),
								type = $this.data('type'),
								anti_type = (type == 'add' ? 'del' : 'add'),					//操作后 类型
								anti_text = (type == 'add' ? '取消关注' : '关注该话题'),		//操作后 文本
								anti_cls = (type == 'add' ? 'core_unfollow' : 'core_follow');	//操作后 class

							if(!GV.U_ID) {
								//未登录
								Wind.Util.quickLogin();
								return;
							}

							if(lock) {
								return;
							}
							lock = true;

							$.post(this.href, {
								id : id,
								type : type
							}, function(data){
								lock = false;
								if(data.state == 'success') {
									$this.text(anti_text).data('type', anti_type).removeClass('core_follow core_unfollow').addClass(anti_cls);
									Wind.Util.resultTip({
										elem : $this,
										follow : true,
										msg : data.message
									});
								}else if(data.state == 'fail') {
									Wind.Util.resultTip({
										error : true,
										elem : $this,
										follow : true,
										msg : data.message
									});
									list.hide();
								}
							}, 'json');
						});
					}, 'html')
				}

			}
		});
	});


	var read_tag_wrap = $('#J_read_tag_wrap'),
		read_tag_edit = $('#J_read_tag_edit');
	
	//编辑话题
	$('#J_read_tag_edit_btn').on('click', function(e){
		e.preventDefault();
		var li_arr = [];

		$.each($('a.J_read_tag_item'), function(i, o){
			var text = $(this).text();
			li_arr.push('<li><a href="javascript:;"><span class="J_tag_name">'+ text +'</span><del class="J_user_tag_del" title="'+ text +'">×</del><input type="hidden" name="tagnames[]" value="'+ text +'"></a></li>');
			
			read_tag_edit.find('ul.J_user_tag_ul').html(li_arr.join(''));
			
		});
		read_tag_edit.show();
		read_tag_wrap.hide();

		Wind.use('ajaxForm');
	});

	//编辑提交
	var btn = $('#J_read_tag_sub');
	btn.on('click', function(e){
		e.preventDefault();
		var $this = $(this);

		setTimeout(function(){
			Wind.use('ajaxForm', function(){
				$('#J_read_tag_form').ajaxSubmit({
					dataType : 'json',
					beforeSubmit : function(){
						Wind.Util.ajaxBtnDisable(btn);
					},
					success : function(data){
						if(data.state === 'success') {
							btn.text(data.message)
							Wind.Util.reloadPage(window);
						}else if(data.state === 'fail'){
							Wind.Util.ajaxBtnEnable(btn);
							Wind.Util.resultTip({
								error : true,
								elem : $this,
								follow : true,
								msg : data.message
							});
						}
					}
				});
			});
			
		}, 100);
	});

})();

//楼层拷贝
(function(){
	var floor_copy = '.J_floor_copy';
	Wind.use(GV.JS_ROOT +'clipboard.js?v='+ GV.JS_VERSION, function () {
		var clipboard = new Clipboard(floor_copy, {
			text: function (trigger) {
				var item = $(trigger);
				var type = item.data('type'),
					tit = (type == 'main' ? $('#J_post_title').text()+'，' : ''), //主楼带帖子标题
					hash = (type == 'main' ? '' : '#'+item.data('hash')), //楼层带hash
					par = item.parent();

				return tit.replace(/\n/, '') + location.protocol + '//' + location.host + location.pathname + location.search + hash;
			}
		});
		clipboard.on('success', function (e) {
			if(Wind.Util.resultTip) {
				Wind.Util.resultTip({
					elem : $(e.trigger),
					follow : true,
					msg : '复制成功'
				});
			} else {
				alert('复制成功');
			}
			e.clearSelection();
		});
		clipboard.on('error', function (e) {
			if(Wind.Util.resultTip) {
				Wind.Util.resultTip({
					error : true,
					elem : $(e.trigger),
					follow : true,
					msg : '复制失败'
				});
			} else {
				alert('复制失败');
			}
		});
	});

})();

//阅读页的代码高亮
(function(){
	//代码高亮公用接口
	window.HighLightFloor = {
		active: false,
		init: function(callback){
			var _this = this;
			var syntaxHihglighter_path = window.GV.JS_ROOT + 'windeditor/plugins/insertCode/syntaxHihglighter/';
			Wind.css(syntaxHihglighter_path + 'styles/shCoreDefault.css?v=' + GV.JS_VERSION);
			Wind.js(syntaxHihglighter_path +'scripts/shCore.js?v=' + GV.JS_VERSION,function() {
				_this.active = true;
				_this.render();
				callback && callback();
			});
		},
		render: function(){
			SyntaxHighlighter.highlight();
		},
		//渲染复制按钮
		renderCopy: function(elem, text){
			//复制代码
			if(elem.data('textCopy')){
				return;
			}
			elem.data('textCopy', 'true');
			Wind.use('textCopy', function() {
				setTimeout(function(){
					elem.textCopy({
						content : text
					});
				});
			});
		},
		addCopy: function(elem){
			var  _self = this,
				html = elem.innerHTML;
			html = html.replace(/&amp;/g, '&').replace(/&lt;/g,'<').replace(/&gt;/g,'>');
			//ie下使用innerHTML会去掉所有空格
			$(elem).text(html);
			var copyElement = $('<br/><a role="button" href="javascript:;" rel="nofollow">复制代码</a>');
			copyElement.insertBefore(elem);
			copyElement.on('mouseover', function(){
				_self.renderCopy(copyElement, html);
			});
		},
		adjust: function(elem){
			if(elem){
	            var tds = elem.getElementsByTagName('td');
	            for(var i=0,li,ri;li=tds[0].childNodes[i];i++){
	                ri = tds[1].firstChild.childNodes[i];
	                if(ri) {
	                    ri.style.height = li.style.height = ri.offsetHeight + 'px';
	                }
	            }
	        }
		}
	};
	//代码高亮渲染
	var codes = $('pre[data-role="code"]');
	if(codes.length) {
		codes.each(function(){
			HighLightFloor.addCopy(this);
		});
		HighLightFloor.init(function(){
			$(".syntaxhighlighter").each(function(){
				HighLightFloor.adjust(this);
			})
		});
	}
})();

//大小图切换
;(function() {
	var attach_pics_list = $('div.read_attach_pic'),
		$doc = $(document);
	if( attach_pics_list.length ) {
		attach_pics_list.each(function() {
			var container = $(this);
			$(this).find('a.J_small_images').on('click',function(e) {
				e.preventDefault();
				$(this).removeClass('current');
				container.find('a.J_big_images').addClass('current');
				container.find('ul.big_img').hide();
				container.find('ul.small_img').show();
			});
			$(this).find('a.J_big_images').on('click',function(e) {
				e.preventDefault();
				$(this).removeClass('current');
				container.find('a.J_small_images').addClass('current');
				container.find('ul.small_img').hide();
				container.find('ul.big_img').show();
				$doc.scrollTop($doc.scrollTop()+1);
			});
		});
	}
})();

//前台管理日志
(function(){
	var inside_logs = $('#J_inside_logs');
	if(inside_logs.length) {
		Wind.use('dialog', function(){
			
			inside_logs.on('click', function(e){
				e.preventDefault();
				Wind.Util.ajaxMaskShow();
				
				$.post(this.href, function(data){
					Wind.Util.ajaxMaskRemove();
					if(Wind.Util.ajaxTempError(data, inside_logs)) {
						return;
					}

					Wind.dialog.html(data, {
						id : 'read_log',
						title : '帖子操作记录',
						isMask : false,
						isDrag : true,
						callback : function(){
							$('#J_log_close').on('click', function(e){
								e.preventDefault();
								Wind.dialog.closeAll();
							});
						}
					});
				});

			});
			
		});
	}
})();