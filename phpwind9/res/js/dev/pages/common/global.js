/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台全局功能js
 * @Author	:
 * @Depend	: wind.js、jquery.js(1.7 or later)
 * $Id: global.js 28908 2013-05-30 02:11:03Z hao.lin $
 */

/*
 * 全局公共方法
*/
Wind.Util = {
	ajaxBtnDisable : function(btn){
		//按钮提交不可用
		var textnode = document.createTextNode('中...');
		btn[0].appendChild(textnode);
		btn.prop('disabled', true).addClass('disabled').data('sublock', true);
	},
	ajaxBtnEnable : function(btn, disabled){
		//按钮提交可用
		var org_html = btn.html();
		btn.html(org_html.replace(/(中...)$/, '')).data('sublock', false);

		if(disabled == 'disabled') {
			//默认不可用
			btn.prop('disabled', true).addClass('disabled');
		}else{
			//默认可用
			btn.prop('disabled', false).removeClass('disabled');
		}
	},
	ajaxConfirm : function(options) {
		//所有的确认提交操作（删除、加入黑名单等）
		var _this = this,
			elem = options.elem,				//点击元素
			href = elem.data('uri') ? elem.data('uri') : options.href,				//ajax地址
			msg = options.msg,					//提示文字
			callback = options.callback;		//回调

		var params = {
			message : msg ? msg : '确定要删除吗？',
			type : 'confirm',
			isMask : false,
			follow : elem,
			onOk : function () {
				_this.ajaxMaskShow();
				$('body').trigger('setCustomPost', [elem]);
				$.post(href, function (data) {
					_this.ajaxMaskRemove();
					if (data.state === 'success') {
						if (callback) {
							//回调处理
							callback();
						} else {
							//默认刷新
							if (data.referer) {
								location.href = decodeURIComponent(data.referer);
							} else {
								location.reload();
							}
						}
					} else if (data.state === 'fail') {
						Wind.Util.resultTip({
							error : true,
							msg : data.message,
							follow : elem
						});
					}
				}, 'json');
			}
		}
		Wind.use('dialog', function(){
			Wind.dialog(params);
		});
	},
	ajaxMaskShow : function(zindex){
		//显示ajax操作全页遮罩
		var $maskhtml = $('<div id="J_ajaxmask" class="top_loading" style="display:none;">载入中...</div>'),
			header = $('#J_header'),
			pos,
			top,
			doc_srh = $(document).scrollTop();

		this.ajaxMaskRemove();

		$maskhtml.appendTo('body');

		if($.browser.msie && $.browser.version < 7) {
			//ie6的定位
			pos = 'absolute';
			top = header.length ? header.height() + doc_srh : doc_srh;
		}else{
			pos = 'fixed';
			top = header.length ? header.height() : 0;
		}

		$maskhtml.css({
			position : pos,
			zIndex : zindex ? zindex : 12,
			top : top
		}).show();

	},
	ajaxMaskRemove : function(){
		//移除ajax操作全页遮罩
		$('#J_ajaxmask').remove();
	},

	ajaxTempError : function(data, follow, zindex) {
		//ajax载入模板html出错判断

		//空内容
		if($.trim(data) === '') {
			return false;
		}

		try{
			var error = false,
				logout = false;

			if(data.indexOf('J_html_error') > 0) {
				//error页 模板
				var start = data.indexOf('<li id="J_html_error">'),
					end = data.indexOf('</li>');
				error = data.substring(start+22, end);
			}else if(data.indexOf('J_u_login_username') > 0) {
				//登录页
				logout  = true;
			}

			if(error) {
				//错误模板
				Wind.Util.ajaxMaskRemove();

				Wind.Util.resultTip({
					error : true,
					msg : error,
					follow : follow,
					zindex : zindex ? zindex : undefined
				});
				return true;
			}else if(logout){
				location.reload();
				return true;
			}else{
				//success
				return false;
			}
		}catch(e){
			$.error(e);
		}
	},
	avatarError : function(avatars){
		//头像的错误处理
		avatars.each(function() {
			this.onerror = function() {
				this.onerror = null;
				this.src = GV.URL.IMAGE_RES + '/face/face_' + $(this).data('type') + '.jpg';//替代头像
				this.setAttribute('alt','默认头像');
			}
			this.src = this.src;
		});
	},
	buttonStatus : function(input, btn){
		//按钮状态 可用&不可用
		var timer;

		//默认为按钮禁用状态
		if(!input.val() || ($.browser.msie && input.val() == input.attr('placeholder'))) {
			btn.addClass('disabled').prop('disabled', true);
		}

		//聚焦
		input.on('focus.b keydown.b', function(){
			var $this = $(this),
				tagname = input[0].tagName.toLowerCase(),
				type_input = false;

			//输入内容是否来自表单控件或div
			if(tagname == 'textarea' || tagname == 'input') {
				type_input = true;
			}

			timer && clearInterval(timer);

			//计时器开始
			timer = setInterval(function(){
				var trim_v = $.trim( type_input ? $this.val() : $this.text() );

				if(trim_v.length) {
					//有内容
					btn.removeClass('disabled').prop('disabled', false);
				}else{
					//空内容
					btn.addClass('disabled').prop('disabled', true);
				}
			}, 200);

		});

		//输入失焦，解除计时
		input.on('blur.b', function(){
			clearInterval(timer);
		});
	},
	clickToggle : function (options) {
		//点击显示隐藏
		var elem = options.elem,						//触发元素
			list = options.list,						//隐藏列表
			callback = options.callback,				//显示后回调
			callbackHide = options.callbackHide,		//隐藏后回调
			lock = false;								//隐藏锁定，默认否
			(function() {
				elem.on('keydown click', function(e) {
					var $this = $(this);
					//非a标签添加 tabIndex，聚焦用
					if($this[0].tagName.toLowerCase() !== 'a') {
						$this.attr('tabindex', '0');
					}
					//点击触发
					if( (e.type === 'keydown' && e.keyCode === 13) || e.type === 'click') {
						e.preventDefault();

						if(list.is(':visible')) {
							list.hide();
						}else{
							list.show();
						}
						
					}else {
						$('.J_dropDownList').hide();
						if(e.type === 'keydown' && e.keyCode === 40) {
							list.attr('tabindex','0').addClass('J_dropDownList').show();
							list.focus();
						}
					}
					//回调
					if(!list.filter(':hidden').length) {
						lock = false;
						callback && callback(elem, list);
					}
				});
				$(document.body).on('mousedown',function(e) {
					//判断点击对象 隐藏列表
					if(list.is(':visible') && e.target!=list[0] && !$.contains(list[0],e.target) && !$.contains(elem[0],e.target)) {
						list.hide();
						elem.focus();
						callbackHide && callbackHide(elem, list);
					}
				});
				list.on('keydown',function(e) {
					if(e.keyCode === 27) {
		                list.hide();
		                elem.focus();
		            }
				});
				list.on('mouseenter', function(e){
					//鼠标进入，锁定
					lock = true;
				}).on('mouseleave', function(){
					//鼠标离开，触发元素聚焦，解除锁定
					elem.focus();
					lock = false;
				});
			})();
	},
	creditReward : function(){
		//显示积分奖励, 在需要判断积分奖励的操作后加入 Wind.Util.creditReward()即可
		var _this = this;
		var reward_temp = '<div id="J_credittxt_pop" class="pop_credittxt_tips"><strong>$name$</strong>$credit$</div>';
		$.post(GV.URL.CREDIT_REWARD_DATA, function(data){
			if(data.state == 'success') {
				var _data = data.data;
				if(_data) {

					var arr = [];
					for(i=0,len=_data['credit'].length; i<len; i++) {
						arr.push('<span>'+ _data['credit'][i][0] +'<em>'+ _data['credit'][i][1] +'</em></span>');
					}

					$('body').append(reward_temp.replace('$name$', _data['name']).replace('$credit$', arr.join('')));

					var credittxt_pop = $('#J_credittxt_pop');
					_this.popPos(credittxt_pop);

					setTimeout(function(){
						credittxt_pop.fadeOut(function(){
							credittxt_pop.remove();
						});
					}, 3000);
				}
			}
		}, 'json');
	},
	cookieGet : function (name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' '){
				c = c.substring(1,c.length);
			}
			if (c.indexOf(nameEQ) == 0){
				return c.substring(nameEQ.length,c.length);
			}
		};

		return null;
	},
	cookieSet : function(name,value,days,domain) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+days*24*60*60*1000);
			var expires = '; expires='+date.toGMTString();
		}else{
			var expires = '';
		}
		document.cookie = name+"="+value+expires+"; domain="+domain+"; path=/";
	},
	ctrlEnterSub : function(elem, btn) {
		//ctrl+enter 提交
		elem.on('keydown', function(e) {
			if (e.ctrlKey && e.keyCode === 13) {
				//防止跟Wind.Util.buttonStatus按键冲突
				elem.blur();

				btn.click();
			}
		});
	},
	flashPluginTest : function(version){
		var flash_install = false,
				flash_v = null;

		if(!$.browser.msie) {
			var b_plug = navigator.plugins;
			if (b_plug) {
				for (var i=0; i < b_plug.length; i++) {
					if (b_plug[i].name.toLowerCase().indexOf("shockwave flash") >= 0) {
						flash_v = b_plug[i].description.substring(b_plug[i].description.toLowerCase().lastIndexOf("flash ") + 6, b_plug[i].description.length);
						flash_install = true;
					}
				}
				if(flash_install) {
					flash_v = parseInt(flash_v.split('.')[0]);
				}
			}
			
		}else{
			if (window.ActiveXObject) {
				var flash_install = false;
				for (var ii = 20; ii >= 2; ii--) {
					try {
						var fl = eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash." + ii + "');");
						if (fl) {
							flash_v = ii;
							flash_install = true;
							break;
						}
					}catch(e){

					}
				}
			}
		}

		if(!flash_install || flash_v < version){
			return false;
		}else{
			return true;
		}
	},
	formBtnTips : function(options){
		var error = options.error ? true : false,
				wrap = options.wrap,
				msg = options.msg,
				callback = options.callback;

		wrap.find('.J_tips_btn').remove();
		if(error) {
			//失败
			wrap.append('<span class="tips_icon_error J_tips_btn">'+ msg +'</span>');
		}else{
			//成功
			$('<span class="tips_icon_success J_tips_btn">'+ msg +'</span>').appendTo(wrap).delay(3000).fadeOut('200', function(){
				$(this).remove();

				//回调
				callback && callback();
			});
		}

	},
	getVerifyTemp : function(options){
		//验证码模板
		var _this = this,
			wrap = options.wrap,				//验证码容器
			afterClick = options.afterClick,	//点击换一个后回调
			clone = options.clone;				//获取失败后恢复内容

		if(!wrap.length) {
			return;
		}

		wrap.html('<span class="tips_loading tips_ck_loading">验证码loading</span>');

		$.ajax({
			url : GV.URL.VARIFY,
			data: { csrf_token : GV.TOKEN },
			dataType : 'json',
			success : function(data){
				if(data.state == 'success') {
					wrap.html(data.html);
				}else if(data.state == 'fail') {
					if(clone) {
						//恢复原代码
						wrap.html(clone.html());
					}else{
						//重试
						wrap.html('<a href="#" role="button" id="J_verify_update_a">重新获取</a>');
					}

					_this.resultTip({
						error : true,
						elem : $('#J_verify_update_a'),
						follow : true,
						msg : data.message
					});
				}
			},
			error : function(){
				wrap.html('验证码请求失败，<a href="#" role="button" id="J_verify_update_a">重新获取</a>');
			}
		});

		wrap.off('click').on('click', '#J_verify_update_a', function(e){
			//换一个
			e.preventDefault();

			if(wrap.find('.tips_loading').length) {
				//防多次点击
				return false;
			}

			var clone = wrap.clone();
			wrap.html('<span class="tips_loading tips_ck_loading">验证码loading</span>');
			_this.getVerifyTemp({
				wrap : wrap,
				clone : clone
			});

			afterClick && afterClick();
		}).on('click', '#J_verify_update_img', function(e){
			//点击图片
			$('#J_verify_update_a').click();
		});
	},
	hashPos : function(){
		//锚点定位 高度
		var hash = location.hash.replace('#', '');
		if(hash.indexOf('hashpos') < 0 ) {
			//不匹配
			return;
		}
		var elem = $('#'+hash),					//锚元素
			elem_ot = elem.offset().top,
			doc_st = $(document).scrollTop(),	//
			hh = $('#J_header').height();		//头部高度

		if(elem_ot - doc_st < hh) {
			//锚元素被头部遮住
			$(document).scrollTop(elem_ot - hh);
		}

	},
	hoverToggle : function(options) {
		//hover显示隐藏内容
		try{
			var elem = options.elem,																//触发元素
					list = options.list,																//隐藏列表
					delay = (options.delay ? options.delay : 200);			//延时

			var timeout;

			elem.on('mouseenter keydown', function (e) {
				//无障碍处理
				if(e.type === 'keydown' && e.keyCode !== 40) {
					//如果不是按的down键，return
					return;
				}else {
					e.preventDefault();
				}

				//清理延时
				timeout && clearTimeout(timeout);

				timeout = setTimeout(function () {
					list.show();

					//回调，传回两个元素
					options.callback && options.callback(elem, list);
				}, delay);
			}).on('mouseleave keydown', function (e) {
				//无障碍处理
				if(e.type === 'keydown' && e.keyCode !== 27) {
					//如果不是按的ESC键，return
					return;
				}else {
					e.preventDefault();
				}
				//鼠标离开
				timeout && clearTimeout(timeout);

				timeout = setTimeout(function () {
					list.hide();
				}, delay);
			});

			list.on('mouseenter', function (e) {
				//清理延时
				timeout && clearTimeout(timeout);
			}).on('mouseleave keydown', function (e) {
				//无障碍处理
				if(e.type === 'keydown' && e.keyCode !== 27) {
					//如果不是按的ESC键，return
					return;
				}else {
					e.preventDefault();
					elem.focus();
				}
				timeout = setTimeout(function () {
					list.hide();
					if(e.type === 'keydown') {
						elem.focus();
					}
				}, delay);
			});
		}catch(e){
				$.error(e);
		}
	},
	reloadPage : function (win) {
		//强制刷新
		var location = win.location;
		location.href = location.pathname + location.search;
	},
	resultTip : function (options) {
		//前台成功提示
		var elem = options.elem || options.follow,			//触发按钮, 曾经是options.follow
			error = options.error,											//正确或错误
			msg = options.msg,													//内容
			follow = options.follow,										//是否跟随显示
			callback = options.callback,								//回调
			zindex = (options.zindex ? options.zindex : 10),			//z值
			cls = (error ? 'warning' : 'success'),			//弹窗class
			_this = this;

		var html = '<span class="pop_showmsg"><span class="' + cls + '">' + msg + '</span></span>';

		//移除重复
		$('#J_resulttip').remove();
		_this.rt_timer && clearTimeout(_this.rt_timer);

		Wind.use('dialog', function(){
			Wind.dialog.html(html, {
				id : 'J_resulttip',
				className : 'pop_showmsg_wrap',
				isMask : false,
				zIndex : zindex,
				callback : function(){
					var resulttip = $('#J_resulttip');

					if(follow){
						//元素上方定位
						var elem_offset_left = elem.offset().left,
							pop_width = resulttip.innerWidth(),
							win_width = $(window).width(),
							left;

						if(win_width - elem_offset_left < pop_width) {
							left = win_width - pop_width
						}else{
							left = elem_offset_left - (pop_width - elem.innerWidth())/2;
						}

						var top = elem.offset().top - resulttip.height() - 15;

						resulttip.css({
							left: left,
							top: top > 0 ? top : elem.offset().top+elem.height() + 15
						});

					}

					_this.rt_timer = setTimeout(function(){
						resulttip.fadeOut(function () {
							resulttip.remove();

							//回调
							callback && callback();
						});
					}, 1500);
				}
			});
		})
	},
	showVerifyPop : function(subButton){
		//弹出验证码

		if(subButton.data('checked')) {
			//已经验证过
			Wind.dialog.closeAll();
			return false;
		}

		var _this = this;
		Wind.use('dialog', function(){
			Wind.dialog.html('<form method="post" action="" id="J_head_question_form">\
				<div class="pop_cont" style="width:400px;">\
					<dl>\
						<dt>验证码：</dt>\
						<dd><input type="text" name="code" class="input length_4" id="J_verify_input"><div id="J_verify_code"></div></dd>\
					</dl>\
				</div>\
				<div class="pop_bottom">\
					<button class="btn btn_submit" type="submit" id="J_verify_sub">提交</button>\
				</div>\
				</form>', {
				id : 'J_verify_pop',
				//cls : 'pop_login core_pop_wrap',
				position : 'fixed',
				isMask : true,
				isDrag : true,
				title : '验证码',
				callback : function(){
					_this.getVerifyTemp({
						wrap: $('#J_verify_code')				//验证码容器
					});

					//提交验证码
					var check_sub = $('#J_verify_sub');
					var form = $("#J_verify_pop").find('form').eq(0);
					form.submit(function(e){
						e.preventDefault();
						Wind.Util.ajaxBtnDisable(check_sub);
						$.post(GV.URL.VARIFY_CHECK, {code : $('#J_verify_input').val()}, function(data){
							if(data.state == 'success') {
								//验证通过 添加标识 触发提交
								subButton.data('checked', true).click();
								//Wind.dialog.closeAll();
							}else if(data.state == 'fail') {
								Wind.Util.ajaxBtnEnable(check_sub);
								_this.formBtnTips({
									error : true,
									wrap : check_sub.parent(),
									msg : data.message
								});
							}
						}, 'json');
					});
				}
			});

		});

		return true;
	},
	popPos : function(wrap){
		//弹窗居中定位
		var top,
			win_height = $(window).height(),
			wrap_height = wrap.outerHeight();

		if(win_height < wrap_height) {
			top = 0;
		}else{
			top = ($(window).height() - wrap.outerHeight())/2;
		}

		wrap.css({
			top : top + $(document).scrollTop(),
			left : ($(window).width() - wrap.innerWidth())/2
		}).show();
	},
	postTip : function(options){
		//发送提示 快速发帖、消息窗私信
		var elem = options.elem,			//定位元素
			msg  = options.msg,				//提示信息
			zindex = options.zindex ? options.zindex : 1,		//z值
			callback = options.callback;		//回调

		var tip = $('<div id="J_posttip_success" class="my_message_success" style="display:none;z-index:'+ zindex +';">'+ msg +'</div>');
		tip.remove();

		tip.appendTo('body').css({
			left : elem.offset().left + (elem.width() - tip.width())/2,
			top : elem.offset().top + (elem.height() - tip.height())/2
		}).fadeIn().delay(1500).fadeOut(function(){
			$(this).remove();
			//回调
			callback && callback();
		});

	},
	quickLogin : function(referer){
		var _this = this;
		//快捷登录
		if(GV.U_ID) {
			//已登录
			return;
		}

		var qlogin_pop = $('#J_qlogin_pop');
		if(qlogin_pop.length) {
			//已弹出

			//global.js
			qlogin_pop.show();

			$('#J_qlogin_username').focus();
			$('#J_qlogin_form').data('referer', referer).resetForm();		//登录后跳转地址
		}else{
			Wind.Util.ajaxMaskShow();

			//未登录，获取登录html, QUICK_LOGIN head.htm
			$.post(GV.URL.QUICK_LOGIN, function(data){
				_this.ajaxMaskRemove();

				if(Wind.Util.ajaxTempError(data)) {
					return false;
				}
				Wind.use('dialog', function(){

						Wind.dialog.html(data, {
							id : 'J_qlogin_pop',
							cls : 'pop_login core_pop_wrap',
							position : 'fixed',
							isMask : false,
							isDrag : true,
							width :350,
							callback : function(){
								var qlogin_pop = $('#J_qlogin_pop');

								if(data.indexOf('J_qlogin_username') < 0) {
									//登录后点击后退，进缓存的未登录页
									window.location.href = referer;
									return false;
								}

								$('#J_qlogin_username').focus();

								//登录后跳转地址
								$('#J_qlogin_form').data('referer', referer);

								Wind.Util.getVerifyTemp({
									wrap : $('#J_verify_code')
								});

								Wind.js(GV.JS_ROOT +'pages/common/quickLogin.js?v='+ GV.JS_VERSION);

							}
						});

				});

			}, 'html');
		}
	}
};



(function () {

	//前台post数据方法 pdata
	$('body').on('setCustomPost', function(event, elem) {
		if(elem.attr('data-pdata')) {
			try{
				var pdata = $.parseJSON(elem.attr('data-pdata').replace(/'/g, '"'));
				$.ajaxSetup({
					data: function(){
						pdata.csrf_token = GV.TOKEN;
						return pdata;
					}()
				})
			}catch(e){
				$.error(e);
			}
		}
	});

	//全局ajax处理
	$.ajaxSetup({
		data : {
			csrf_token : GV.TOKEN
		},
		beforeSend:function(jqXHR, settings) {
			//如果请求的域不一样，那么攺成同域请求，因为ajax跨子域有兼容性问题
			var url = settings.url,
				local_url = location.href,
				url_re = /^(((([^:\/#\?]+:)?(?:(\/\/)((?:(([^:@\/#\?]+)(?:\:([^:@\/#\?]+))?)@)?(([^:\/#\?\]\[]+|\[[^\/\]@#?]+\])(?:\:([0-9]+))?))?)?)?((\/?(?:[^\/\?#]+\/+)*)([^\?#]*)))?(\?[^#]+)?)(#.*)?/;
			var url_matches = url_re.exec( url ) || [];
			var local_matches = url_re.exec( local_url ) || [];

			var main_reg = /[\w-]+\.(com|net|org|gov|cc|biz|info|cn)(\.(cn|hk))*$/,
				url_main = main_reg.exec(url_matches[3]) || [],
				local_main = main_reg.exec(local_matches[3]) || [1];

			if(url_matches[3] !== local_matches[3] && url_main[0] == local_main[0]) {
				//跨子域 
				if($.browser.msie) {
					//ie 下跨域报错no transport
					$.support.cors = true;
				}
				settings.url = settings.url.replace(url_matches[3],local_matches[3]);
			}else{
				return;
			}
			jqXHR.setRequestHeader('X-Requested-With','XMLHttpRequest');
		},
		error : function(jqXHR, textStatus, errorThrown){
			//请求失败处理
			if(errorThrown) {
				//移除ajax请求遮罩
				Wind.Util.ajaxMaskRemove();

				//移除按钮提交中状态
				var btn = $('button.disabled:submit');
				for(i=0, len = btn.length; i<len; i++) {
					if($(btn[i]).data('sublock')) {
						Wind.Util.ajaxBtnEnable($(btn[i]));
						break;
					}
				}

				$.error(errorThrown);
			}
		}
	});

	if($.browser.msie) {
		//ie 都不缓存
		$.ajaxSetup({
			cache : false
		});
	}

	//不支持placeholder浏览器下对placeholder进行处理
	if(document.createElement('input').placeholder !== '') {
		$('head').append('<style>.placeholder{color: #aaa;}</style>');
		$('[placeholder]').focus(function() {
			var input = $(this);

			if(input.val() == input.attr('placeholder')) {
				input.val('');
				input.removeClass('placeholder');
			}
		}).blur(function() {
			var input = $(this);
			//密码框空
			if(this.type === 'password') {
				return false;
			}
			if(input.val() == '' || input.val() == input.attr('placeholder')) {
				input.addClass('placeholder');
				input.val(input.attr('placeholder'));
			}
		}).blur().parents('form').submit(function() {
			$(this).find('[placeholder]').each(function() {
				var input = $(this);
				if(input.val() == input.attr('placeholder')) {
					input.val('');
				}
			});
		});
	}

	//是否编辑模式
	window.DESIGN_MODE = ( document.getElementById('J_top_design') ? true : false );


	//侧栏登录
	var username = $('#J_username'),
		sidebar_login_btn = $('#J_sidebar_login');
	if (username.length) {

		Wind.use('ajaxForm', function () {

			var password = $('#J_password');

			$("#J_login_form").ajaxForm({
				dataType : 'json',
				beforeSubmit : function (arr, $form, options) {
					Wind.Util.ajaxBtnDisable(sidebar_login_btn);
					$('#J_login_tips').remove();
				},
				success : function (data, statusText, xhr, $form) {
                    if (data.state === 'success') {
                        if (data.message.check.url) {
                            data.message.check.url = (data.message.check.url).replace(/&amp;/gm,'&');
							//验证问题
							Wind.use('dialog', function(){
                                console.log(data.message.check.url);
								$.post(data.message.check.url, function (data) {
									//引入所需组件并显示弹窗
									Wind.Util.ajaxBtnEnable(sidebar_login_btn);
									if(Wind.Util.ajaxTempError(data)) {
										return;
									}

									Wind.dialog.html(data, {
										id: 'J_login_question_wrap',
										isDrag: true,
										isMask: false,
										onClose: function(){
											sidebar_login_btn.focus();
										}
									})

									//获得焦点
									var question_wrap = $('#J_login_question_set_wrap, #J_login_question_wrap');

									question_wrap.find('input:text:visible:first').focus();

									Wind.Util.getVerifyTemp({
										wrap: $('#J_verify_code') //验证码容器
									});
								}, 'html');
							});
							
						} else {
							window.location.href = decodeURIComponent(data.referer);
						}

					} else {
						Wind.Util.ajaxBtnEnable(sidebar_login_btn);
						$('<div id="J_login_tips" style="display:none;"><div class="tips"><div class="tips_icon_error">'+ data.message +'</div></div></div>').appendTo($('#J_sidebar_login_dt')).fadeIn(200).delay(3000).fadeOut();
					}
				}

			});

		});

	}

	//ie6 hover头像
	var ava_ie6 = $('#J_ava_ie6');
	if(ava_ie6.length && $.browser.msie && $.browser.version < 7) {
		ava_ie6.hover(function(){
			ava_ie6.addClass('hover');
		}, function(){
			ava_ie6.removeClass('hover');
		});
	}

	//判断触发快捷登录
	if(!GV.U_ID) {
		$(document).on('click', 'a.J_qlogin_trigger, button.J_qlogin_trigger', function(e){
			e.preventDefault();
			var referer = $(this).data('referer');					//登录后跳转还是刷新
			Wind.Util.quickLogin(referer ? this.href : '');
		});
	}

	//select控件关联日期组件
	var date_select = $('.J_date_select');
	if (date_select.length) {
		Wind.use('dateSelect', function () {
			date_select.dateSelect();
		});
	}

	//全选
	if ($('.J_check_wrap').length) {
		//遍历所有全选框
		$.each($('input.J_check_all'), function (i, o) {
			var $o = $(o),
				check_wrap = $o.parents('.J_check_wrap'), //当前操作区域所有复选框的父标签
				check_all = check_wrap.find('input.J_check_all'), //当前操作区域所有(全选)复选框
				check_items = check_wrap.find('input.J_check'); //当前操作区域所有(非全选)复选框

			//点击全选框
			$o.change(function (e) {
				if ($(this).attr('checked')) {
					//全选
					check_items.attr('checked', true);

					if (check_items.filter(':checked').length === check_items.length) {
						check_all.attr('checked', true); //所有全选打钩
					}

				} else {
					//取消全选
					check_items.removeAttr('checked');
					check_all.removeAttr('checked');
				}
			});

			//点击(非全选)复选框
			check_items.change(function () {
				if ($(this).attr('checked')) {

					if (check_items.filter(':checked').length === check_items.length) {
						check_all.attr('checked', true); //所有全选打钩
					}

				} else {
					check_all.removeAttr('checked'); //取消全选
				}
			});

		});
	}


	var header = $('#J_header'), //头部
		header_wrap = header.parent(), //头部通栏
		header_pos = header_wrap.css('position'), //头部通栏定位方式
		head_msg_pop = $('#J_head_msg_pop'); //头部消息弹窗

	//点击头部用户名
	var head_user_a = $('#J_head_user_a');
	if(head_user_a.length) {
		Wind.Util.clickToggle({
			elem : head_user_a,				//点击元素
			list : $('#J_head_user_menu'),			//下拉菜单
			callback : function(elem, list) {
				if (header_pos == 'static') {
					//默认定位 发帖页
					list.css({
						position : 'absolute',
						top : elem.offset().top + elem.height() + 15
					});
				}

				$('#J_head_pl_user').off('click').on('click', function(e){
					e.preventDefault();
					list.hide();
				});
			}
		});
	}


	//头部js
	var head_msg_btn = $('#J_head_msg_btn');//消息按钮
	if(!DESIGN_MODE && (head_msg_btn.length || GV.U_ID)) {
		//头部消息
		Wind.Util.clickToggle({
			elem : head_msg_btn,
			list : head_msg_pop,
			callback : function(elem, list){
				//定位 显示
				var _this = this;
				list.css({
					position : ($.browser.version < 7 ? 'absolute' : 'fixed'),
					left : header.width() + header.offset().left - head_msg_pop.outerWidth(),
					top : head_msg_btn.offset().top+head_msg_btn.height()+7 - $(document).scrollTop()
				});

				//headMsg是否已加载
				Wind.js(GV.JS_ROOT+ 'pages/common/headMain.js?v='+ GV.JS_VERSION);

				//点击消息遮罩的图片 隐藏列表
				$('#J_head_pl_hm').off('click').on('click', function(e){
					e.preventDefault();
					list.hide();
					
					$('#J_emotions_pop').hide();
					$('#J_hm_home').show().siblings().remove();
				});
			},
			callbackHide : function(elem, list){
				//显示消息首页列表
				$('#J_emotions_pop').hide();
				$('#J_hm_home').show().siblings().remove();
			}
		});

		//头部发帖
		var head_forum_post = $('#J_head_forum_post'),	//头部发帖按钮
			head_forum_pop = $('#J_head_forum_pop');		//头部发帖列表
		Wind.Util.clickToggle({
			elem : head_forum_post,
			list : head_forum_pop,
			callback : function(){
				//wind.js不会重复加载
				Wind.js(GV.JS_ROOT +'pages/common/headMain.js?v='+ GV.JS_VERSION);

				var position,
					top,
					header_pos = $('#J_header').parent().css('position');

				if(header_pos == 'static') {
					//发帖页
					position = 'absolute';
				}else{
					if($.browser.msie && $.browser.version < 7) {
						position = 'absolute';
					}else{
						position = 'fixed';
					}
				}

				if(position == 'absolute') {
					top = head_forum_post.offset().top + head_forum_post.height();
				}else{
					top = head_forum_post.offset().top + head_forum_post.height() - $(document).scrollTop();
				}

				$('#J_head_forum_pop').css({
					position : position,
					top : top
				});
			}
		});
	}


	//侧栏勋章滚动
	var medal_widget_ul = $('#J_medal_widget_ul');
	if(medal_widget_ul.length){
		var sidebar_medal_ta = $('#J_sidebar_medal_ta'),
				sidebar_medal_text = $(sidebar_medal_ta.text()),			//数据文本
				sidebar_medal_ul_len = medal_widget_ul.children().length,	//显示数量
				sidebar_medal_arr = [];

		sidebar_medal_text.each(function(i, o){
			//剔除空节点
			if(o.nodeType == 1) {
				var $o = $(o),
					cls = $o.attr('class') ? $o.attr('class') : '';
				sidebar_medal_arr.push('<li class="'+  cls +'">'+ $o.html() +'</li>');
			}
		});


		//总数大于列表可见数
		if(sidebar_medal_arr.length > sidebar_medal_ul_len) {
			Wind.use('lazySlide', function(){
				$('#J_medal_widget').lazySlide({
					step_length : sidebar_medal_ul_len,
					html_arr : sidebar_medal_arr
				});
			});
		}

	}

	//键盘翻页
	var page_wrap = $('.J_page_wrap');
	if(page_wrap.length && page_wrap.data('key') && !DESIGN_MODE) {
		$(document).on('keyup', function(e){
			var focus_el = $(':focus');
				
			if(focus_el.length) {
				var lowercase = focus_el[0].tagName.toLowerCase();
				if(lowercase == 'textarea' || lowercase == 'input') {
					//如果聚焦于输入框则取消翻页
					return;
				}
			}

			if(e.keyCode == 37) {
				var prev = page_wrap.find('a.J_pages_pre');
				if(prev.length) {
					location.href = prev.attr('href');
				}
			}else if(e.keyCode == 39) {
				var next = page_wrap.find('a.J_pages_next');
				if(next.length) {
					location.href = next.attr('href');
				}
			}
		});
	}

	//锚点定位
	if(!DESIGN_MODE) {
		Wind.Util.hashPos();
	}

	//登录判断 积分奖励
	if(GV.CREDIT_REWARD_JUDGE) {
		Wind.Util.creditReward();
	}

	//侧栏版块手风琴
	$('dt.J_sidebar_forum_toggle').on('click', function(){
		var this_dl = $(this).parent();
		this_dl.toggleClass('current');
		this_dl.siblings('dl.current').removeClass('current');
	});

	//侧栏模块手风琴
	$('.J_sidebar_box_toggle').on('click', function(){
		var par = $(this).parent();
		par.toggleClass('my_forum_list_cur');
	});
	
	//input只能输入数字
	$('input.J_input_number').on('keyup', function(){
		var v = $(this).val();
		$(this).val(v.replace(/\D/g,''));
	});

	//喜欢组件
	var like_btn = $('.J_like_btn');
	if (like_btn.length && GV.U_ID && !DESIGN_MODE) {
		Wind.js(GV.JS_ROOT+ 'pages/common/likePlus.js?v='+ GV.JS_VERSION, function () {
			likePlus(like_btn);
		});
	}

	//发消息_弹窗
	var send_msg_btn = $('a.J_send_msg_pop');
	if(send_msg_btn.length && GV.U_ID && !DESIGN_MODE) {
		Wind.js(GV.JS_ROOT+ 'pages/common/sendMsgPop.js?v='+ GV.JS_VERSION);
	}

	//日历组件
	var date_btns = $("input.J_date");
	if(date_btns.length) {
		Wind.use('datePicker',function() {
			date_btns.datePicker();
		});
	}

	//tab组件
	var tab_wrap = $('.J_tab_wrap');
	if(tab_wrap.length) {
		Wind.use('tabs', function(){
			tab_wrap.each(function(){
				$(this).find('.J_tabs_nav').first().tabs($(this).find('div.J_tabs_ct').first().children('div'));
			});
		});
	}

	//用户输入标签组件
	if ($('.J_user_tag_wrap').length && !DESIGN_MODE) {
		Wind.js(GV.JS_ROOT+ 'pages/common/userTag.js?v=' + GV.JS_VERSION);
	}

	//默认头像
	var avas = $('img.J_avatar');
	if(avas.length) {
		Wind.Util.avatarError(avas);
	}

	//广告管家iframe
	var ad_iframes_div = $('div.J_ad_iframes_div'),
			ad_iframes_len = ad_iframes_div.length;
	if(ad_iframes_len && !DESIGN_MODE) {
		var ad_isf = Wind.Util.flashPluginTest() ? '1' : '0';
		for(i=0; i<ad_iframes_len; i++) {
			var ad_item = $(ad_iframes_div[i]),
					ad_iframe = document.createElement('iframe');
			$(ad_iframe).attr({
				src : ad_item.data('src')+'&isf='+ad_isf,
				frameborder	: '0',
				scrolling	: 'no',
				height		: ad_item.data('height'),
				width		: ad_item.data('width')
			});

			ad_item.replaceWith(ad_iframe);
		}
	}



	//验证码模板替换
	var verify_code = $('#J_verify_code');
	if(verify_code.length) {
		Wind.Util.getVerifyTemp({wrap : verify_code});
	}

	//邮箱自动匹配
	var email_match = $('input.J_email_match');
	if(email_match.length) {
		email_match.attr('autocomplete', 'off');
		Wind.use('emailAutoMatch', function(){
			email_match.emailAutoMatch();
		});
	}

	//举报
	var report = $('a.J_report');
	if(report.length && GV.U_ID && !DESIGN_MODE) {
		Wind.js(GV.JS_ROOT+ 'pages/common/report.js?v='+ GV.JS_VERSION);
	}

	//地区组件
	var region_set = $('.J_region_set');
	if(region_set.length) {
		Wind.use('region', function(){
			$('a.J_region_change').region();
		});
	}

	//侧栏-可能认识&打卡
	if(!DESIGN_MODE && ($('#J_friend_maybe').length || $('#J_punch_mine').length)) {
		Wind.js(GV.JS_ROOT + 'pages/common/sidebarMain.js?v=' + GV.JS_VERSION);
	}

	//计划任务 全局执行请求
	if(GV.URL.CRON_AJAX) {
		var cron_img = new Image();
		cron_img.src=GV.URL.CRON_AJAX;
	}

	//表情插入
	var insert_emotions = $('a.J_insert_emotions');
	if(insert_emotions.length && !DESIGN_MODE) {
		Wind.js(GV.JS_ROOT+ 'pages/common/insertEmotions.js?v='+ GV.JS_VERSION, function(){
			insert_emotions.on('click', function(e){
				e.preventDefault();
				insertEmotions($(this), $($(this).data('emotiontarget')));
			});
		});
	}

	//textarea的 @ 功能
	if(!DESIGN_MODE) {
		$(document).on('focus', '.J_at_user_textarea', function(){
			var elem = $(this);
			Wind.js(GV.JS_ROOT + 'pages/common/userAt.js?v=' + GV.JS_VERSION, function(){
				userAutoTips({elem:elem[0]});
			});
		});
	}

	//图片上传预览
	if($("input.J_upload_preview").length) {
		Wind.use('uploadPreview',function() {
			$("input.J_upload_preview").uploadPreview();
		});
	}

	//幻灯片
	var gallery_list = $('ul.J_gallery_list');
	if(gallery_list.length && !DESIGN_MODE) {
		Wind.use('gallerySlide', function(){
			gallery_list.gallerySlide();
		});
	}

	//代码复制_表单元素
	var clipboard_input = $('a.J_clipboard_input'); //复制按钮
	if(clipboard_input.length) {
		if(!$.browser.msie && !Wind.Util.flashPluginTest(9)) {
			if(confirm('您的浏览器尚未安装flash插件，代码复制不可用！点击确定下载')) {
				location.href = 'http://get.adobe.com/cn/flashplayer/';
			};
			return;
		}

		Wind.use('textCopy', function() {
			for(i=0, len=clipboard_input.length; i<len; i++) {
				var item = $(clipboard_input[i]);
				item.textCopy({
					content : $('#' + item.data('rel')).val()
				});
			}
		});
	}

})();

$.error = function(message) {
	//重写$.error
	//TODO:改成更好体验的错误消息弹出
	console.error(message);
};

//公告滚动
(function(){
	var an_slide_auto = $('ul.J_slide_auto'),
		an_lock = false,											//滚动锁定
		an_timer,
		step_h = an_slide_auto.children().height(),
		an_h = an_slide_auto.height();								//整体高度

	an_slide_auto.hover(function(){
		//鼠标进入，锁定
		an_lock = true;
	}, function(){
		//鼠标进入，解锁 执行
		an_lock = false;
		anMove();
	});
	anMove();

	function anMove(){
		clearTimeout(an_timer);
		if(an_lock || an_h == step_h) {
			//锁定或不超过2行时不执行
			return false;
		}
		var mgtop = parseInt(an_slide_auto.css('marginTop').replace('px', '')),
			mgtop_remove = Math.abs(mgtop) + step_h;

		an_timer = setTimeout(function(){
			if(!an_lock) {
				an_slide_auto.animate({'marginTop' : -mgtop_remove}, function(){
					if(mgtop_remove >= an_h) {
						//重置
						an_slide_auto.css('marginTop', 0);
					}
					anMove();
				});
			}
		}, 5000);
	}
})();

//回到顶部
(function () {
    var back_top_btn = $('#back_top');
    if ($.browser.msie && $.browser.version < 7) {
        back_top_btn.remove();
        return; //ie6不支持回到顶部
    }
    if (back_top_btn.length) {
        var scrollTimer;
        $(window).scroll(function () {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function () {
                var scrollTop = $(this).scrollTop();
                if (scrollTop > 400) {
                    back_top_btn.fadeIn();
                } else {
                    back_top_btn.fadeOut();
                }
            }, 100);
        });
        back_top_btn.on('click', function (e) {
            e.preventDefault();
            $('body,html').animate({
                scrollTop: 0
            }, 400);
        });
    }
})();

//ios/android兼容mouse事件 by kejun https://gist.github.com/3358036
;(function ($) {
    $.support.touch = 'ontouchend' in document;

    if (!$.support.touch) {
        return;
    }

    var eventMap = {
        click: 'touchend',
        mousedown: 'touchstart',
        mouseup: 'touchend',
        mousemove: 'touchmove'
    };

    var simulateEvent = function (eventType) {
        $.event.special[eventType] = {
            setup: function () {
                var el = $(this);
                el.bind(eventMap[eventType], $.event.special[eventType].handler);
                if (this.nodeName === 'A' && eventType === 'click') {
                    this.addEventListener('click', function (e) {
                        e.preventDefault();
                    }, false);
                }
            },
            teardown: function () {
                $(this).unbind(eventMap[eventType], $.event.special[eventType].handler);
            },
            handler: function (e) {
                var touch = e.originalEvent.changedTouches[0];
                e.type = eventType;
                e.pageX = touch.pageX;
                e.pageY = touch.pageY;
                e.clientX = touch.clientX;
                e.clientY = touch.clientY;
                $.event.handle.call(this, e);
            }
        };
    };

    $.fn.delegate = function (selector, types, data, fn) {
        var params = data;
        if (typeof data === 'function') {
            fn = data;
            params = null;
        }
        var handler = function (e) {
            if (this.nodeName === 'A' && e.type === 'click') {
                this.addEventListener('click', function (e) {
                    e.preventDefault();
                }, false);
            }
            fn.apply(this, arguments);
        };
        return this.live(types, params, handler, selector);
    };

    $.each(['click', 'mousedown', 'mousemove', 'mouseup'],

    function (i, name) {
        simulateEvent(name);
    });

})(jQuery);


//小名片
(function(){
	var user_card_show = $('a.J_user_card_show'),
		card_wrap = '<div class="pop_card J_pop_card" id="_ID"><div class="arrow J_card_arrow"><em></em><span></span><strong></strong></div><div class="pop_loading J_pop_loading"></div></div>';
	
	var lock_hide = false, //隐藏锁定, true表示不隐藏
		timeout;
	
	//经过用户名或头像触发
	var i = 0;
	$(document).on('mouseenter', 'a.J_user_card_show', function(e){
		e.preventDefault();
		i += 1;
		var $this = $(this),
			uid = $this.data('uid'),
			uname = $this.data('username'),
			param = $this.data('param'); //名片标识参数

		if(uid === 0) {
			//游客
			return false;
		}

		if(!param) {
			param = uid ? uid : 'c' + i;
			$this.data('param', param);
		}
		
		var card_item = $('#J_user_card_'+ param);

		lock_hide = true;
		
		clearTimeout(timeout);
		timeout = setTimeout(function(){
			//先隐藏所有小名片
			$('div.J_pop_card').hide();
			
			if(card_item.length) {
				//已存在则显示
				card_item.show();
				cardPos($this, card_item);
			}else{
				//不存在则请求
				$('body').append(card_wrap.replace('_ID', 'J_user_card_'+ param));
				var card = $('#J_user_card_'+ param);
				cardPos($this, card);

				$.post(GV.URL.USER_CARD, {
					username : uname,
					uid : uid
				}, function(data){
					if(Wind.Util.ajaxTempError(data)) {
						card.remove();
						return;
					}

					card.find('.J_pop_loading').replaceWith(data);
					cardPos($this, card);
				}, 'html');
			
			}
			
		}, 300);
		
	}).on('mouseleave', 'a.J_user_card_show', function(e){
		//离开
		clearTimeout(timeout);		//清理ajax
		lock_hide = false;				//触发隐藏
		
		var $this = $(this),
			card = $('#J_user_card_'+ $this.data('param'));

		timeout = setTimeout(function(){
			if(!lock_hide){
				card.hide();
			}
		}, 300);
	});
	
	$(document).on('mouseenter', 'div.J_pop_card', function(){
		//进入小名片
		lock_hide = true;
	}).on('mouseleave', 'div.J_pop_card', function(){
		//离开小名片
		var $this = $(this);
		lock_hide = false;
		
		setTimeout(function(){
			if(!lock_hide){
				$this.hide();
			}
		}, 300);
	});

	//定位
	function cardPos(elem, wrap){
		var left,																			//名片水平位置
				top,
				cls = 'arrow',														//三角class，正
				_cls = 'arrow_bottom',										//三角class，反
				elem_offset_left = elem.offset().left,
				elem_offset_top = elem.offset().top,
				wrap_width = wrap.outerWidth(),						//名片宽度
				wrap_height = wrap.outerHeight() + 15,		//名片高度 15为三角高度
				win_width = $(window).width(),
				arror_left = elem.innerWidth() / 2 - 9;		//小三角水平位置
			
		//判断右侧宽度是否足够
		if(win_width - elem_offset_left < wrap_width) {
			left = win_width - wrap_width;
			arror_left = elem_offset_left - left + elem.innerWidth() / 2 - 9;
		}else{
			left = elem_offset_left;
		}
		
		//判断窗口下方高度是否足够
		var elem_window_top = elem_offset_top - $(document).scrollTop(),										//触发元素到窗口顶部距离
			elem_window_bottom = $(window).height() - elem_window_top - elem.innerHeight();			//触发元素到窗口底部距离
		
		//默认显示在上方
		top = elem.offset().top + elem.innerHeight() + 10;
		
		if(wrap_height > elem_window_bottom && wrap_height <= elem_window_top) {
			//显示在上方
			top = elem_offset_top - wrap_height;
			cls = 'arrow_bottom';
			_cls = 'arrow'
		}
		
		//小名片位置
		wrap.css({
			left : left,
			top: top
		});
		
		//小三角位置
		wrap.find('.J_card_arrow').css({
			left : arror_left
		}).removeClass(_cls).addClass(cls);
		
		//写私信
		if($('a.J_send_msg_pop').length && !DESIGN_MODE) {
			Wind.js(GV.JS_ROOT+ 'pages/common/sendMsgPop.js?v='+ GV.JS_VERSION);
		}
		
		//关注与取消
		var lock = false;
		$('a.J_card_follow').off('click').on('click', function(e){
			if(lock) {
				return false;
			}
			lock = true;
			e.preventDefault();
			var $this = $(this);
			$.post(this.href, {
				uid: $this.data('uid')
			}, function(data){
				if(data.state == 'success') {
					$this.parent('.J_follow_wrap').hide().siblings('.J_follow_wrap').show();
				}else if(data.state == 'fail') {
					//global.js
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
				}
				lock = false;
			}, 'json');
		});
	}


})();

//申请链接
(function(){
	var lock = false;
	$('a.J_link_apply').on('click', function(e){
			e.preventDefault();
			var $this = $(this);

			if($('#J_link_apply_pop').length) {
				return false;
			}

			if(lock == true) {
				return false;
			}
			lock = true;

			$.post(this.href, function(data){
				if(Wind.Util.ajaxTempError(data)) {
					return false;
				}

				Wind.use('dialog', function(){
					Wind.dialog.html(data, {
						id : 'J_link_apply_pop',
						title : '申请链接',
						//cls : 'pop_login core_pop_wrap',
						position : 'fixed',
						isMask : false,
						isDrag : true,
						width : 450,
						callback : function(){
							var link_apply_btn = $('#J_link_apply_btn');
							Wind.use('ajaxForm', function(){
								$('#J_link_apply_form').ajaxForm({
									beforeSubmit : function(){
										Wind.Util.ajaxBtnDisable(link_apply_btn);
									},
									dataType : 'json',
									success : function(data){
										if(data.state == 'success') {
											Wind.Util.formBtnTips({
												wrap : link_apply_btn.parent(),
												msg : data.message,
												callback : function(){
													Wind.dialog.closeAll();
												}
											});
										}else if(data.state == 'fail') {
											Wind.Util.formBtnTips({
												error : true,
												wrap : link_apply_btn.parent(),
												msg : data.message
											});
										}
										Wind.Util.ajaxBtnEnable(link_apply_btn);
									}
								});
							});
							

							lock = false;
						}
					});
				});
			}, 'html');
		});

})();
