/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-喜欢组件
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later), global.js, jquery.form
 * $Id$
 */
;
(function () {
	var html_like = '<div class="pop_read_like J_pop_like" style="">\
								<div class="hd"><a id="J_like_close" href="" class="pop_close">关闭</a><span id="J_like_m"><a href="index.php?m=like&c=mylike">管理喜欢</a></span></div>\
								<!--点击前-->\
								<div class="ct" id="J_like_trigger">\
									<input type="text" class="input" placeholder="说点什么吧～" />\
								</div>\
								<!--点击后-->\
								<form id="J_like_forwarding_form" method="post" action="'+ GV.URL.LIKE_FORWARDING +'&fid=_FID">\
								<div id="J_like_enter" style="display:none;">\
									<div class="ct">\
										<textarea name="atc_content"></textarea>\
									</div>\
									<div class="ft">\
										<input type="hidden" value="_PID" name="pid" />\
										<input type="hidden" value="_TID" name="tid" />\
										<input type="hidden" value="like" name="from_type" />\
										<button class="btn" type="submit" id="J_like_forwarding_sub">确认</button><label><input type="checkbox" name="isfresh" value="1" />告诉我的粉丝</label>\
									</div>\
								</div>\
								</form>\
								<!--结束-->\
								<div class="pop_read_like_arrow"></div>\
							</div>';

	var likeUtil = {
		forwarding : function(){
			//转发
			var _this = this,
				btn = $('#J_like_forwarding_sub');
			 $('#J_like_forwarding_form').ajaxForm({
				dataType : 'json',
				beforeSubmit : function(){
					Wind.Util.ajaxBtnDisable(btn);
				},
				data : {
					csrf_token : GV.TOKEN
				},
				success : function(data){
					Wind.Util.ajaxBtnEnable(btn);
					if(data.state == 'success') {
						_this.close();
						Wind.Util.resultTip({
							msg : data.message,
							callback : function(){
								window.location.reload()
							}
						});
					}else if(data.state == 'fail'){
						Wind.Util.resultTip({
							error : true,
							msg : data.message
						});
					}
				}
			}); 
		},
		close : function(){
			//隐藏输入
			$('div.J_pop_like').remove();
		},
		plus : function(elem, avatar){
			//加1
			var c = Number(elem.text());
			elem.slideUp('fast', function(){
				$(this).text(c+1).slideDown(function(){
					$(this).parent().show();
				});
			});
			
			//主楼显示最近喜欢
			if(avatar) {
				var read_like_list = $('#J_read_like_list');
				read_like_list.show().find('.J_read_like_tit').after('<a class="J_user_card_show" data-uid="'+ GV.U_ID +'" href="'+ GV.U_CENTER +'"><img height="50" width="50" src="'+ GV.U_AVATAR +'" class="J_avatar" data-type="small"><span>'+ GV.U_NAME +'</span></a>');
				Wind.Util.avatarError(read_like_list.find('img.J_avatar'));
			}
		}
	};
	var lock = false;
	window.likePlus = function(elem) {
		$(elem).on('click', function (e) {
				e.preventDefault();

				if(!GV.U_ID) {
					//未登录
					Wind.Util.quickLogin();
					return;
				}

				if(lock) {
					return;
				}
				lock = true;
				
				var $this = $(this),
					fid = $this.data('fid'),
					pid = $this.data('pid'),
					tid = $this.data('tid'),
					$wrap = $(html_like.replace('_FID', fid).replace('_PID', pid).replace('_TID', tid)),
					role = $this.data('role'); //区分喜欢按钮
				
				likeUtil.close();
				var url;
				
				Wind.Util.ajaxMaskShow();
				$.post(this.href, {
					typeid: $this.data('typeid'),
					fromid: $this.data('fromid')
				},function (data) {
					lock = false;
					Wind.Util.ajaxMaskRemove();
					if (data.state === 'success') {
					
						if(role == 'main') {
							//喜欢主楼
							likeUtil.plus($this.children('.J_like_count'), true);
						}else if(role == 'hot'){
							//热门喜欢
							likeUtil.plus($this.children('.J_like_count'), false);
						}else{
							//喜欢楼层
							likeUtil.plus($this.parent().find('a.J_like_user_btn'), false);
						}
					
						$wrap.appendTo($('body')).css({
							left : $this.offset().left - ($wrap.innerWidth() - $this.innerWidth()) / 2,
							top : $this.offset().top - $wrap.innerHeight() - 25
						});
						
						if(data.data > 3) {
							$('#J_like_m').hide();
						}
						
						timer = setTimeout(function(){
							likeUtil.close();
						}, 3000);
						
						var like_enter = $('#J_like_enter');
						
						//点击输入
						$('#J_like_trigger').on('click', function () {
							var $this = $(this),
							top_origin = $wrap.css('top');
							
							//global.js
							Wind.Util.buttonStatus(like_enter.find('textarea'), like_enter.find('button:submit'));
							
							$this.hide();
							like_enter.show().find('textarea').focus();
							
							//重新计算垂直距离
							$wrap.css({
								top : Number(top_origin.replace('px', '')) - (like_enter.innerHeight() - $this.innerHeight())
							});
							clearTimeout(timer);
							
							//转发提交
							Wind.use('ajaxForm', function(){
								likeUtil.forwarding();
							});
						});
						
						$('#J_like_close').on('click', function (e) {
							e.preventDefault();
							likeUtil.close();
						});
						
					}else if(data.state === 'fail'){
						likeUtil.close();
						Wind.Util.resultTip({
							follow : $this,
							msg : data.message,
							error : true
						});
					}
					
				}, 'json');
				
		});
	}

})();
