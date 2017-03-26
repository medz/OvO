/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-新鲜事阅读（新鲜事、个人空间）
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later), FRESH_DOREPLY页面定义
 * $Id$
 */
;
(function () {
	var feed_lists = $('#J_feed_lists');
/*
 * 删除
*/
$('a.J_fresh_del').on('click', function(e){
	e.preventDefault();
	var $this = $(this);

	//global.js
	Wind.Util.ajaxConfirm({
		elem : $this,
		href : this.href,
		callback : function(){
			//global.js
			Wind.Util.ajaxMaskRemove();

			$this.parents('dl').slideUp(function(){
				$(this).remove();
			});
		}
	});
});

	//回复列表部分html
	var feed_part_html = '<div class="feed_repeat_arrow">\
									<em>◆</em>\
									<span>◆</span>\
								</div><form action="'+ FRESH_DOREPLY +'" method="post"><input name="id" type="hidden" value="_ID" /><div class="feed_repeat_textarea">\
									<div class="input_area"><textarea id="J_fresh_emotion__ID" name="content" class="J_feed_textarea J_at_user_textarea" style="overflow-y:hidden;"></textarea></div>\
									<div class="addition">\
										<a href="javascript:;" class="icon_face J_fresh_emotion" data-emotiontarget="#J_fresh_emotion__ID">表情</a>\
										<label><input type="checkbox" value="1" name="transmit">告诉我的粉丝</label>\
									</div>\
									<div class="enter"><button class="btn btn_submit J_feed_sub">回复</button></div>\
		</div></form><div class="feed_repeat_list J_feed_repeat_list">_DATA</div>';
		
	var $loading_html = $('<div class=""><span class="tips_loading">正在loading</span></div>');
	
	var id;
	//显示载入回复列表
	feed_lists.on('click', 'a.J_feed_toggle', function(e){
		e.preventDefault();
		$('#J_emotions_pop').hide();
		var $this =  $(this);
		
		id = $this.data('id');
		var	list = $('#J_feed_list_'+ id);

		if(list.children().length) {
			list.hide().empty();
		}else{
			list.html($loading_html[0]).show();
			$.post($this.attr("href"), function(data) {
				if (Wind.Util.ajaxTempError(data)) {
					list.hide();
					return false;
				}
				
				list.html(feed_part_html.replace(/_ID/g, id).replace('_DATA', data));

				var feed_ta = list.find('textarea.J_feed_textarea'),
					feed_btn = list.find('button.J_feed_sub');
					
				Wind.Util.buttonStatus(feed_ta, feed_btn);
				Wind.Util.ctrlEnterSub(feed_ta, feed_btn);
				list.find('textarea').focus();
				Wind.Util.avatarError(list.find('img.J_avatar'));

				if(!$.isFunction(window.insertEmotions)) {
					Wind.js(GV.JS_ROOT+ 'pages/common/insertEmotions.js?v='+ GV.JS_VERSION);
				}
			}, 'html');
			
		}
	});
	
	
	feed_lists.on('click', 'button.J_feed_sub', function(e){
	
		//回复提交
		e.preventDefault();
		var btn = $(this);

		btn.parents('form').ajaxSubmit({
			dataType	: 'html',
			data : {
					csrf_token : GV.TOKEN
			},
			beforeSubmit: function(arr, $form, options) {
				Wind.Util.ajaxBtnDisable(btn);
			},
			success : function(data, statusText, xhr, $form){
				
				if(Wind.Util.ajaxTempError(data, btn)) {
					if(data.indexOf('审核') > 0) {
						$form.resetForm();
						$form.find('.J_feed_textarea').removeAttr('style');
						Wind.Util.ajaxBtnEnable(btn, 'disabled');
					}else{
						Wind.Util.ajaxBtnEnable(btn);
					}
					
					return;
				}

				Wind.Util.ajaxBtnEnable(btn, 'disabled');

				var repeat_wrap = $form.siblings('.J_feed_repeat_list'),
				repeat_list = repeat_wrap.children();

				if(repeat_list.length >= 10) {
					//超过十条则删除最底下的一条
					repeat_list.last().remove();
				}

				$form.resetForm();
				$form.find('.J_feed_textarea').removeAttr('style');
				repeat_wrap.prepend(data);

				//写入转发到顶部
				var fresh_floor = $('#J_fresh_floor');
				if(fresh_floor.length) {
					fresh_floor.insertAfter('#J_news_tip').fadeIn().removeAttr('id');
					Wind.Util.avatarError($('#J_feed_lists dl').first().find('img.J_avatar'));
				}

				//统计+1
				var feed_count = $('#J_feed_count_'+ id), c = Number(feed_count.text());
				feed_count.text(c+1).parent().show();

				//积分奖励
				Wind.Util.creditReward();
			
			}
		});

		$('#J_emotions_pop').hide();
	}).on('click', 'a.J_feed_single', function(e){
	
		//回复单条
		e.preventDefault();
		var $this = $(this), user = $this.data('user'),
				textarea = $this.parents('.J_feed_list').find('textarea');

		textarea.focus().val('@'+ user +'：');
		if(!$.browser.msie) {
			//chrome 光标定位最后
			textarea[0].setSelectionRange(100,100);
		}
		
		$('#J_emotions_pop').hide();
	}).on('focus', 'textarea.J_feed_textarea', function(){
		//回复框聚焦后高度自适应
		var $this = $(this),
				_this = this,
				this_style = _this.style;
		
		//$this.on('keydown keyup', function(){
		_this.timer = setInterval(function(){
			var height,
					multiplier = Math.floor(_this.scrollHeight/18),		//乘数
					sc_height = multiplier * 18;											//高度值

				//每次都先重置高度, ff & chrome
				this_style.height =  18 + 'px';

			if (multiplier > 1) {
				//暂定180为最大高度
				if (sc_height > 180) {
					height = 180;
					this_style.overflowY = 'scroll';
				} else {
					height = Math.floor(_this.scrollHeight/18) * 18;
					this_style.overflowY = 'hidden';
				}

				this_style.height = height  + 'px';
		
			}

		}, 300);
		//});
		
	}).on('blur', 'textarea.J_feed_textarea', function(){
		//回复框失焦
		clearInterval(this.timer);
	});
	
	//阅读&收起 全部
	var lock = false;
	feed_lists.on('click', 'a.J_read_all', function(e){
		e.preventDefault();
		var $this = $(this),
			content = $('#'+ $this.data('id'));
		
		if($this.data('dir') === 'down') {
			if(lock) {
				return false;
			}
			lock = true;
			//阅读全部
			Wind.Util.ajaxMaskShow();
			$.ajax({
				url : this.href,
				dataType : 'html',
				type : 'post',
				success : function(data){
					Wind.Util.ajaxMaskRemove();
					if(Wind.Util.ajaxTempError(data)) {
						return false;
					}
					content.hide().siblings('.J_content_all').html(data).show();
					$loading_html.remove();
					$this.text('收起↑').data('dir', 'up');
				},
				complete : function(){
					lock = false;
				}
			});
		}else{
			//收起
			content.show().siblings('.J_content_all').hide().empty();
			$this.text('阅读全部↓').data('dir', 'down');
		}
		
	});

/*
 * 喜欢
*/
	feed_lists.on('click', 'a.J_fresh_like', function(e){
		e.preventDefault();
		var $this = $(this);
		$.post($this.attr('href'), function(data){
			var em = $this.find('em'),
					org_v = parseInt(em.text());

			if(data.state == 'success') {
				em.text(org_v+1).parent().show();

				//global.js
				Wind.Util.resultTip({
					msg : data.message,
					follow : $this
				});

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
	
})();
