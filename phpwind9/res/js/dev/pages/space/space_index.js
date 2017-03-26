/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-个人空间
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), dialog, jquery.form, tabs, jquery.draggable
 * $Id$
 */
 
Wind.use('ajaxForm', function(){
	var body = $('body'),
			style_orgin = body.attr('style'),		//原样式
			bg_saved = body.css('backgroundImage');
	if(bg_saved.indexOf('images/bg.png') > 0) {
		//过滤默认背景
		bg_saved = '';
	}
	//阅读全部
		
	//空间设置
	$('#J_space_set').on('click', function(e){
		e.preventDefault();
		var $this = $(this);
		$.post($this.attr('href'), function(data){
			if(Wind.Util.ajaxTempError(data)) {
				return false;
			}
			Wind.dialog.html(data, {
				id : 'J_space_pop',
				position	: 'fixed',	//固定定位
				//isMask		: false,	//无遮罩
				onClose : function(){
					//移除自定义背景样式
					body.attr('style', style_orgin ? style_orgin : '');
					
					//移除模板预览iframe
					$('iframe.J_space_preview').remove();
				},
				callback	: function(){
					//拖拽 jquery.draggable
					Wind.use('draggable', function(){
						$('#J_space_pop').draggable( { handle : '.pop_top'} );
					});
					
					//tab
					Wind.use('tabs', function(){
						$('#J_space_pop_nav').tabs('#J_space_pop_content > div');
					});
					
					//个性域名校验
					var domain = $('#J_domain'),
						root = $('#J_root'),
						check_domain = $('#J_check_domain');
					Wind.Util.buttonStatus(domain, check_domain);
					var domain_pass = false;
					check_domain.on('click', function(e){
						e.preventDefault();
						var $this = $(this);
						
						$.post($this.data('url'), {domain : domain.val(), root : root.val()}, function(data){
							if(data.state == 'success') {
								//提示 global.js
								Wind.Util.resultTip({
									msg : '校验通过',
									follow : $this
								});
							}else if(data.state == 'fail'){
								Wind.Util.resultTip({
									error : true,
									msg : data.message,
									follow : $this
								});
							}
						}, 'json');
					});
					
					//模板设置_翻页
					var temp_list = $('#J_temp_list'),													//模板列表
						temp_page = $('#J_temp_page'),												//模板翻页列表
						page_total = temp_page.children().length,								//总页数
						temp_prev = $('#J_temp_prev'),												//上一组
						temp_next = $('#J_temp_next'),												//下一组
						li_height = temp_list.children().outerHeight(true),
						step_height = li_height * 2,														//一次移动高度
						lock = false;																			//移动锁定 默认否
					
					//模板_点击
					temp_list.on('click', 'a', function(e){
						e.preventDefault();
						var id = $(this).data('id'),
							url = $(this).attr('href');
							
						if(!$(this).hasClass('current')) {
							$(this).parent().addClass('current').siblings().removeClass('current');
							$('#J_styleid').val(id);
							
							var space_preview = $('#J_iframe_preview_'+ id);
							$('iframe.J_space_preview').hide();
							
							if(space_preview.length) {
								//iframe已存在 显示
								space_preview.show();
							}else{
								//iframe不存在请求 创建
								$.post(url, function(data){
									//global.js
									if(Wind.Util.ajaxTempError(data)) {
										return false;
									}
										
									$('<iframe class="J_space_preview" id="J_iframe_preview_'+ id +'" frameborder="0" src="'+ url +'" style="position:absolute;left:0;top:0;z-index:8;border:0;width:100%;height:100%;padding:0;margin:0;display:none;" scrolling="no" />').appendTo(body);
							
									var $iframe = $('#J_iframe_preview_'+ id);
									$iframe[0].contentWindow.location.href = url;
									$iframe.load(function(){
										$iframe.show();
										var body = $iframe[0].contentWindow.document.body;
										$iframe.css({
											height : $(body).height()
										});
									});
									
								});
							}

						}
					});
					
					//模板_点击页数
					temp_page.children('a').on('click', function(e){
						e.preventDefault();
						var $this = $(this);
							
						if(!$this.hasClass('current')) {
							var index = $this.index();

							tempMove(index);
						}
					});
					
					//模板_上下页
					$('a.J_temp_pn').on('click', function(e){
						e.preventDefault();
						if(lock) {
							return;
						}
						lock = true;
						var $this = $(this),
							role = $this.data('role');
							current_index = temp_page.children('.current').index();			//当前页索引

						if(role === 'next') {
							//判断是否为最后一页
							if(current_index !== page_total-1) {
								tempMove(current_index + 1);
							}else{
								lock = false;
							}
						}else{
							//判断是否为第一页
							if(current_index !== 0) {
								tempMove(current_index - 1);
							}else{
								lock = false;
							}
						}
					});
					//模板滚动
					function tempMove(page_index){
						
						//当前的最后一个项索引
						var li_index = 6*(page_index+1) - 1;

						//判断此项src是否存在
						if(!temp_list.children(':eq('+ li_index +')').find('img').attr('src')) {
							
							//不存在循环写入地址
							var li_lt = temp_list.children(':lt('+ parseInt(li_index+1) +')');
							$.each(li_lt, function(i, o){
								var img = $(this).find('img')
								if(img.data('src')) {
									img.attr('src', img.data('src')).data('src', false);
								}
							});
						}
						
						//移动
						temp_list.css({
							marginTop : -step_height * page_index
						});

						lock = false;
						
						//current状态
						temp_page.children(':eq('+ page_index +')').addClass('current').siblings().removeClass('current');
					}

					
					//背景图片
					var input_repeat = $('#J_bg_repeat_input'),					//背景平铺 input
						input_attachment = $('#J_bg_attachment_input'),		//背景固定 input
						input_position = $('#J_bg_position_input');				//背景对齐 input
					
					//背景设置
					$('#J_bg_position > a').on('click', function(e){
						e.preventDefault();
						$(this).addClass('current').siblings().removeClass('current');
						
						//返回设置背景设置css
						var css_arr = getBgCss($(this).data('val'));
						
						//写入input
						input_repeat.val(css_arr[0]);
						input_attachment.val(css_arr[1]);
						
						body.css({
							backgroundRepeat : css_arr[0],
							backgroundAttachment : css_arr[1]
						});
					});
					
					//对齐方式
					$('#J_bg_align > a').on('click', function(e){
						e.preventDefault();
						$(this).addClass('current').siblings().removeClass('current');
						
						//写入input
						input_position.val($(this).data('val'));
						
						body.css({
							backgroundPosition : $(this).data('val') +' top'
						});
					});
					
					//上传自定义图
					var custom_thumb = $('#J_custom_thumb'),
							space_bg_preview = $('#J_space_bg_preview'),
							space_bg_cancl = $('#J_space_bg_cancl');		//取消背景图片
					Wind.use('uploadPreview', function(){
						custom_thumb.uploadPreview({
							maxWidth : 160,
							maxHeight : 500,
							message : '上传的图片大小不能超过'
						});
					});
					

					//已有背景显示取消按钮

					if(bg_saved) {
						space_bg_cancl.show();
					}
					
					//上传控件切换
					custom_thumb.on('change', function(){
						setTimeout(function(){
							//返回设置背景设置css
							var css_arr = getBgCss($('#J_bg_position > a.current').data('val'));
							body.css({
								backgroundImage : 'url(' +space_bg_preview.attr('src')+ ')',
								backgroundPosition : $('#J_bg_align > a.current').data('val') +' top',
								backgroundRepeat : css_arr[0],
								backgroundAttachment : css_arr[1]
							});
						}, 100);
						
					});

					//取消背景
					space_bg_cancl.on('click', function(e){
						e.preventDefault();
						//背景恢复
						//body.attr('style', style_orgin ? style_orgin : '');
						body.css({
							backgroundImage : ''
						});

						//表单清空
						$('#J_space_bg_saved').val('');

						//图片移除
						space_bg_preview.removeAttr('src');
						
						$(this).hide();
					});
					
					//提交基本信息
					$('#J_editspace_sub').on('click', function(e){
						e.preventDefault();
						var spacename = $('#J_spacename'),
							descrip = $('#J_descrip'),
							spacename_length = $.trim(spacename.val()).length,
							descrip_length = $.trim(descrip.val()).length,
							edit_tip = $('#J_edit_tip');
						
						$('#J_spacename, #J_descrip').on('keyup', function(){
							edit_tip.hide();
						});
						
						if(spacename_length > 20) {
							edit_tip.html('<div class="tips red" style="margin-top:5px;margin-bottom:-8px;">空间名称最多20字，已超出'+ (spacename_length - 20) +'字</div>').show();
							spacename.focus();
							return false;
						}
						
						if(descrip_length > 250) {
							edit_tip.html('<div class="tips red" style="margin-top:5px;margin-bottom:-8px;">空间简介最多250字，已超出'+ (descrip_length - 250) +'字</div>').show();
							descrip.focus();
							return false;
						}
						
						$(this).parents('form').ajaxSubmit({
							dataType : 'json',
							success : function(data){
								if(data.state == 'success') {
									Wind.Util.resultTip({
										msg : '设置成功',
										callback : function(){
											window.location.reload();
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
					});
					
					//提交
					$('form.J_space_pop_form').ajaxForm({
						dataType : 'json',
						success : function(data){
							if(data.state == 'success') {
								Wind.Util.resultTip({
									msg : '设置成功',
									callback : function(){
										window.location.reload();
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
					
				}
			});

		}, 'html');
		
	});
	
	//判断返回背景设置css
	function getBgCss(v){
		var repeat, atta
		
		if(v == 'repeat') {
			//平铺
			repeat = 'repeat';
			atta = 'scroll';
		}else if(v == 'fixed'){
			//锁定
			repeat = 'no-repeat';
			atta = 'fixed';
		}else{
			//正常
			repeat = 'no-repeat';
			atta = 'scroll';
		}
		return [repeat, atta];
	}


	//关注 取消
	var lock = false;
	$('a.J_space_follow').on('click', function(e){
		if(!GV.U_ID) {
			return;
		}
		e.preventDefault();
		var $this = $(this),
			role = $this.data('role'),
			url = (role == 'follow' ? SPACE_FOLLOW : SPACE_UNFOLLOW);

		if(lock) {
			return false;
		}
		lock = true;

		Wind.Util.ajaxMaskShow();
		$.post(url, {uid : $this.data('uid')}, function(data){
			Wind.Util.ajaxMaskRemove();
			if(data.state == 'success') {
				if(role == 'follow') {
					$this.html('取消关注').data('role', 'unfollow');
					$this.addClass('unfollow');
				}else{
					$this.html('<em></em>加关注').data('role', 'follow');
					$this.removeClass('unfollow');
				}

				$('#J_user_card_'+ $this.data('uid')).remove();
			}else if(data.state == 'fail') {
				Wind.Util.resultTip({
					elem : $this,
					error : true,
					msg : data.message,
					follow : true
				});
			}
			lock = false;
		}, 'json');
	});

});