/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-头部发帖&消息
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later), global
 * $Id: headMsg.js 24161 2013-01-22 07:55:30Z yanchixia $
 */

 //消息
;(function(){
	var $hm_wrap = $('#J_head_msg'), //消息窗父容器
			hm_home = '#J_hm_home', //消息窗首页列表
			hm_list = '.J_hm_list', //消息窗各页面列表区
			hm_max_height = 400, //消息窗高
			hm_loading = $('<div class="pop_loading" style="position:absolute;left:50%;top:50%;margin:-40px 0 0 -25px;"></div>'),
			lock = false, //消息窗请求锁定
			postlock = false;

	//获取消息窗首页
	var headMsgUtil = {
		init : function(){

			//请求锁定
			if(lock) {
				return;
			}
			lock = true;

			var _this = this;

			//请求消息窗首页
			$.post(GV.URL.HEAD_MSG.LIST)
			.done(function(data){
				//global.js
				if(Wind.Util.ajaxTempError(data, $('#J_head_msg_btn'))) {
					$('#J_head_msg_pop').hide();
					$hm_wrap.html('<div class="not_content_mini"><i></i>出错啦，请稍后刷新再试</div>');
					return false;
				}

				Wind.use('ajaxForm', function(){
					$hm_wrap.html(data);

					var $hm_list = $(hm_list);
					_this.IE6Height();

					if($.support.getSetAttribute) {
						//ie6 7 不引入
						Wind.use('scrollFixed', function(){
							$hm_list.scrollFixed();
						});
					}

					if($.browser.msie && $.browser.version < 7) {
						$hm_list.on('mouseenter', 'li', function(){
							$(this).addClass('current');
						}).on('mouseleave', 'li', function(){
							$(this).removeClass('current');
						});
					}

					$('#J_hm_home li').attr('tabindex','0').on('click', function (e) {
						if(e.target.tagName.toLowerCase() == 'a') {
							return;
						}
						_this.getPage($(this).data('url'), $(this));
					});

				});

			});
		},
		IE6Height : function() {
			//ie6 高度判定
			if ($.browser.msie && $.version === '6.0') {
				if ($(hm_list).height() > this.max_height) {
					$(hm_list).css('height', this.max_height);
				} else {
					//list.css('height', 'auto');
				}
			}
		},
		getPage : function(url, elem) {
			//更换页面
			var _this = this;
			$('#J_emotions_pop').hide();
			hm_loading.appendTo($hm_wrap);

			$.post(url)
			.done(function(data){
				if(Wind.Util.ajaxTempError(data)) {
					return false;
				}

				$(hm_home).hide().siblings().remove();
				$hm_wrap.append(data).find(hm_loading).remove();

				//绑定发私信
				if($hm_wrap.find('a.J_send_msg_pop').length) {
					Wind.js(GV.JS_ROOT+ 'pages/common/sendMsgPop.js?v='+ GV.JS_VERSION);
				}

				//表情
				if($hm_wrap.find('a.J_insert_emotions').length) {
					Wind.use(GV.JS_ROOT+ 'pages/common/insertEmotions.js?v='+ GV.JS_VERSION);
				}

				_this.IE6Height();

				if(elem.length) {
					//统计
					_this.readCount(elem);
				}
				
				if($.support.getSetAttribute) {
					$(hm_list).scrollFixed();
				}
			});
		},
		readCount : function(elem){
			//未读统计
			if(!elem.hasClass('unread')) {
				return;
			}

			var hm_num = $('.J_hm_num'),	//总体未读统计
				org_num = parseInt(hm_num[0].innerHTML),
				multi = elem.find('.J_unread_multi'),
				result_num;
			if(multi.length) {
				//私信
				result_num = org_num - multi.data('unread');
				multi.remove();
			}else{
				//通知
				result_num = org_num - 1;

				unread_icon = elem.find('.J_unread_icon');
				if(unread_icon.length) {
					unread_icon.attr('class', unread_icon.attr('class').replace('_new', '')).removeClass('J_unread_icon');
				}
			}
			hm_num.text(result_num);
			elem.removeClass('unread');

			if(result_num <= 0) {
				hm_num.parent().addClass('header_message_none');
			}
		},
		topTipsAdd : function(html){
			//添加消息顶部提示
			$('#J_hm_top').after('<div class="tips">'+ html +'</div>');
		},
		topTipsDel : function(){
			//移除消息顶部提示
			$('#J_hm_top').next('.tips').remove();
		}
	};
	headMsgUtil.init();


	//消息窗内操作绑定

	//绑定所有返回按钮
	$hm_wrap.on('click', 'a.J_hm_back', function (e) {
		e.preventDefault();
		$('#J_emotions_pop').hide();
		$('#J_hm_home').show().siblings().remove();
	});

	//
	$hm_wrap.on('click', 'a.J_hm_ajaxlink', function (e) {
		e.preventDefault();
		var $this = $(this);
		$.getJSON($this.attr('href'), function(data){
			if(data.state === 'success') {
				Wind.Util.resultTip({
					msg : data.message
				});
			}else if(data.state === 'fail'){
				Wind.Util.resultTip({
					error : true,
					msg : data.message
				});
			}
		});
	});

	//加入黑名单&屏蔽 带操作提示
	$hm_wrap.on('click', 'a.J_hm_ajaxtip', function (e) {
		e.preventDefault();
		var $this = $(this),
				role = $this.data('role'),				//类型
				name = $this.data('name'),				//操作对象
				referer = $this.data('referer');	//跳转地址

		$.getJSON($this.attr('href'), function(data){
			if(data.state === 'success') {
				var tip_text, btn_text;

				if(role == 'blacklist') {
					tip_text = '已把 '+ name +' 列入黑名单，您不会再收到Ta的私信。';
					btn_text = '查看黑名单';
				}else if(role == 'app'){
					tip_text = '您将不会再收到 '+ name +' 通知';
					btn_text = '查看通知设置';
				}

				headMsgUtil.topTipsAdd(tip_text);

				//修改按钮状态，移除绑定class
				$this.text(btn_text).removeClass('J_hm_ajaxtip').attr('href', referer);

			}else if(data.state === 'fail'){
				Wind.Util.resultTip({
					error : true,
					msg : data.message
				});
			}
		});
	}).on('click', 'a.J_notice_ignore', function(e){
		//忽略
		e.preventDefault();
		var $this = $(this),
			role = $this.data('role'),
			ignore = $this.data('ignore'),
			anti_ignore = (ignore == '0' ? '1' : '0'),
			anti_text;

		if(role == 'reply') {
			anti_text = (ignore == '0' ? '关闭回复提醒' : '开启回复提醒');
		}else{
			anti_text = (ignore == '0' ? '忽略' : '取消忽略');
		}

		if(postlock) {
			return false;
		}
		postlock = true;

		$.post(this.href, {ignore : ignore}, function(data){
			if(data.state == 'success') {
				$this.text(anti_text).data('ignore', anti_ignore);

				if(ignore == '1') {
					headMsgUtil.topTipsAdd('您不会再收到 '+ $this.data('type') +' 通知');
				}else{
					headMsgUtil.topTipsDel();
				}

			}else if(data.state == 'fail') {
				Wind.Util.resultTip({
					error : true,
					elem : $this,
					follow : true,
					msg : data.message
				});
			}
			postlock = false;
		}, 'json');
	});



	//表情
	$hm_wrap.on('click', 'a.J_insert_emotions', function(e){
		e.preventDefault();
		var head_msg_pop = $('#J_head_msg_pop'),
			$this = $(this);

		insertEmotions($this, $('#J_head_msg_textarea'), head_msg_pop);
	}).on('click', 'a.J_msg_follow', function(e){
		//加关注
		e.preventDefault();
		var $this = $(this);
		$.post(this.href, {
			uid: $this.data('uid')
		}, function(data){
			if(data.state == 'success') {
				$this.replaceWith('<span class="core_unfollow">已关注</span>');
			}else if(data.state == 'fail'){
				//global.js
				Wind.Util.resultTip({
					error : true,
					msg : data.message,
					follow : $this
				});
			}
		}, 'json');
	});

	//发送
	$hm_wrap.on('click', '#J_hm_reply_placeholder', function (e) {
		//显示输入
		$(this).hide();
		$('#J_message_reply').fadeIn();
		var head_msg_textarea = $('#J_head_msg_textarea'),
			message_reply_btn = $('#J_message_reply_btn');

		//global.js
		Wind.Util.buttonStatus(head_msg_textarea, message_reply_btn);
		Wind.Util.ctrlEnterSub(head_msg_textarea, message_reply_btn);

		$('#J_head_msg_textarea').focus();
	}).on('click', '#J_message_reply_btn', function (e) {
		e.preventDefault();
		var $this = $(this),
				textarea = $('#J_head_msg_textarea');

		$('#J_emotions_pop').hide();

		$this.parents('form').ajaxSubmit({
			dataType : 'json',
			beforeSubmit : function(){
				//global.js
				Wind.Util.ajaxBtnDisable($this);
			},
			success : function(data){
				Wind.Util.ajaxBtnEnable($this, 'disabled');

				if(data.state === 'success') {
					var dialog_list = $('#J_msg_dialog_list');
					dialog_list.prepend('<div class="my cc">\
		<div class="face"><a href=""><img height="25" width="25" data-type="small" src="'+ GV.U_AVATAR +'" class="J_avatar"></a></div>\
		<div class="bubble">\
			<div class="arrow"><em></em><span></span></div>\
			<a class="b" href="http://www.phpwind.dev/index.php?m=space&amp;uid=2">我</a>：'+ $.trim(textarea.val()) +'<div class="io"><span class="time">刚刚</span></div>\
		</div>\
	</div>');
					textarea.val('');
					Wind.Util.postTip({
						elem : textarea,
						msg : '发送成功',
						zindex : 11,
						callback : function(){
							$('#J_message_reply').hide();
							$('#J_hm_reply_placeholder').fadeIn();
						}
					});

					//global.js
					Wind.Util.avatarError(dialog_list.find('img.J_avatar'));
				}else if(data.state === 'fail'){
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
				}
			}
		});
	});

	//统一处理所有ajax页面请求
	$hm_wrap.on('click', 'a.J_hm_page', function (e) {
		e.preventDefault();
		headMsgUtil.getPage($(this).attr('href'));
	});

})();



//发帖
(function(){
	var forum_data = {}, //版块数据
		head_forum_ct = $('#J_head_forum_ct'), //版块弹窗列表区
		post_to_forum = $('#J_post_to_forum'), //发帖到_版块
		head_forum_sub = $('#J_head_forum_sub'), //确定
		forum_ul,
		cur_cid = head_forum_ct.data('cid'), //当前cid
		cur_fid = head_forum_ct.data('fid'), //当前fid
		fid = '';

	if(!forum_data.data) {
		//请求版块数据
		$.post(GV.URL.FORUM_LIST, {
				'withMyforum' : 1
			}, function(data){
			if(data.state == 'success') {
				forum_data.data = data.data;

				//循环写入分类数据
				var cate_data = forum_data.data['cate'],		//分类数据
						cate_arr = [];
				for(i=0, len=cate_data.length;i<len;i++) {
					cate_arr.push('<li tabindex="0" role="option" class="J_cate_item" data-cid="'+ cate_data[i][0] +'" aria-label='+ cate_data[i][1] +'>'+ cate_data[i][1] +'</li>');
				}
				head_forum_ct.html('<div class="source_forum" tabindex="0" role="combobox" aria-owns="J_forum_list" aria-label="选择要发帖版块的分类，按回车键选定，按tab键盘进行切换"><h4>选择分类</h4><ul id="J_forum_list">'+ cate_arr.join('') +'</ul></div><div class="target_forum" tabindex="0" role="combobox" aria-owns="J_forum_ul" aria-label="选择要发帖的版块，按回车键选定，按tab键盘进行切换"><h4>选择版块</h4><ul id="J_forum_ul"></ul></div>');
				forum_ul = $('#J_forum_ul');

				if(cur_cid) {
					$('#J_forum_list li[data-cid='+cur_cid+']').trigger('click');
				}
			}else if(data.state == 'fail') {
				head_forum_ct.html(data.message);
			}
		}, 'json');
	}


	//点击分类
	head_forum_ct.on('click keydown', 'li.J_cate_item', function(e) {
		if(e.type === 'keydown' && e.keyCode !== 13) {
			return;
		}
		var current_cid = $(this).data('cid');

		$(this).addClass('current').siblings().removeClass('current');
		post_to_forum.text('');																								//发帖到_版块
		head_forum_sub.addClass('disabled').prop('disabled', 'disabled');		//确定按钮不可用

		//循环写入版块数据

		var data_forum = forum_data.data['forum'][current_cid],
				forum_arr = [];
		for(i=0,len=data_forum.length;i<len;i++) {
			forum_arr.push('<li tabindex="0" role="option" class="J_forum_item" data-fid="'+ data_forum[i][0] +'" aria-label='+ data_forum[i][1] +'>'+ data_forum[i][1] +'</li>');
		}
		forum_ul.html(forum_arr.join(''));
		forum_ul.parent().focus();

		if(cur_fid) {
			forum_ul.find('li[data-fid='+ cur_fid +']').trigger('click');
		}
		
	});

	//点击版块
	head_forum_ct.on('click keydown', 'li.J_forum_item', function(e) {
		if(e.type === 'keydown' && e.keyCode !== 13) {
			return;
		}else {
			e.preventDefault();
		}
		fid = $(this).data('fid');
		$(this).addClass('current').siblings('.current').removeClass('current');
		post_to_forum.text($(this).text().replace(/-/g, ''));								//发帖到_版块
		head_forum_sub.removeClass('disabled').removeProp('disabled');		//确定按钮可用
		if(e.type === 'keydown') {
			$('#head_forum_join').focus();
		}
	});

	//跳转发帖页
	head_forum_sub.on('click', function(e) {
		e.preventDefault();
		var $this = $(this),
			href = $this.data('url') +'&fid='+ fid,
			head_forum_join = $('#J_head_forum_join');

		if(head_forum_join.prop('checked')) {
			//加入版块
			$.post(head_forum_join.data('url'), {fid : fid}, function(data){
				location.href = href;
			}, 'json');
		}else{
			location.href = href;
		}
	});

	//关闭
	$('#J_head_forum_close').on('click', function(e){
		e.preventDefault();
		$('#J_head_forum_pop').hide();
	});

})();