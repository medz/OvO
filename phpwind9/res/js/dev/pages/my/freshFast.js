/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-快速发帖
 * @Author	: linhao87@gmail.com, TID
 * @Depend	: jquery.js(1.7 or later), global.js
 * $Id$
 */


;(function(){
	//快速发帖
	var fresh_post_wrap = $('#J_fresh_post_wrap'),														//快速发帖区域
		fresh_post_ta = $('#J_fresh_post_ta'),																//默认显示的输入框
		fresh_post_op = $('#J_fresh_post_op'),																//操作容器
		fresh_post_sub = $('#J_fresh_post_sub'),															//发布按钮
		fresh_forum_btn = $('#J_fresh_forum_btn'),														//选择版块按钮
		fresh_post_forum_wrap = $('#J_fresh_post_forum_wrap'),								//其他版块窗
		fresh_upload_queue = $('#J_fresh_upload_queue'),											//队列
		fresh_post_fid = $('#J_fresh_post_fid'),															//版块提交表单
		cateforum_json = '',																									//版块json数据 
		fresh_cate = $('#J_fresh_cate'),																			//其他版块_分类列表
		fresh_forum = $('#J_fresh_forum'),																		//其他版块_版块列表
		fresh_post_to_forum = $('#J_fresh_post_to_forum'),										//发帖到-版块
		fresh_post_pop_close = $('#J_fresh_post_pop_close'),									//确定按钮
		lock = false;

	var swfu;

	var feed_lists = $('#J_feed_lists');
	
	//点击发帖区
	fresh_post_ta.on('click', function(){
		$(this).attr('style', '').addClass('textarea').removeAttr('placeholder');
		fresh_post_op.fadeIn('fast');
		
		if(!cateforum_json) {
			//获取分类版块json
			$.post(FORUM_LIST, {
				'withMyforum' : 1
			}, function(data){
				if(data.state == 'success') {
					cateforum_json = data.data;
				}
			}, 'json');
		}

		if(!swfu) {
			swfHandle();
		}
		
	});

	//版块选择
	Wind.Util.clickToggle({
		elem : fresh_forum_btn,
		list : fresh_post_forum_wrap,
		callback : function(){
			if(!fresh_cate.children().length) {
				//还没有写入列表数据
				try {
				
					//循环写入分类列表
					var cate_arr = [];
					$.each(cateforum_json.cate, function(i, o){
						cate_arr.push('<li class="J_fresh_cate_item" data-fid="'+ o[0] +'">'+ o[1] +'</li>');
					});
					fresh_cate.html(cate_arr.join(''));
				}catch(e) {
					$.error(e);
				};
			}
		}
	});

	//关闭版块
	$('#J_fresh_forum_close').on('click', function(e){
		e.preventDefault();
		fresh_post_forum_wrap.hide();
	});
	
	//点击分类
	fresh_cate.on('click', 'li.J_fresh_cate_item', function(){
		var current_fid = $(this).data('fid');

		$(this).addClass('current').siblings('.current').removeClass('current');
		fresh_post_to_forum.text('');	//面包屑_版块
		fresh_post_pop_close.addClass('disabled').prop('disabled', 'disabled');		//确定按钮不可用
		try {
			//循环写入版块列表
			var forum_arr = [];
			$.each(cateforum_json['forum'][current_fid], function(i, o){
				forum_arr.push('<li class="J_fresh_forum_item" data-fid="'+ o[0] +'">'+ o[1] +'</li>');
			});
			fresh_forum.html(forum_arr.join(''));
		}catch(e) {};
	});
	
	//点击版块
	fresh_forum.on('click', 'li.J_fresh_forum_item', function(){
		$(this).addClass('current').siblings('.current').removeClass('current');
		
		fresh_post_to_forum.text($(this).text().replace(/-/g, ''));								//面包屑
		fresh_post_pop_close.removeClass('disabled').removeProp('disabled');		//确定按钮可用
	});
	
	//版块确定
	fresh_post_pop_close.on('click', function(e){
		//e.preventDefault();
		var current_li = fresh_forum.children('li.current'),								//选中版块
				current_fid = current_li.data('fid');														//选中的版块id

		if(document.getElementById('J_forum_join').checked) {
			//加入版块
			$.post(FORUM_JOIN, {fid : current_fid}, function(data){
				fresh_post_forum_wrap.hide();
			}, 'json');
		}else{
			fresh_post_forum_wrap.hide();
		}

		fresh_forum_btn.find('.J_text').text(current_li.text().replace(/-/g, ''));		//显示已选版块
		fresh_post_fid.val(current_fid);												//input值

		//图片上传fid传参
		try{
			swfu.settings.post_params.fid = current_fid;
			swfu.setPostParams(swfu.settings.post_params);
		}catch(e){
			$.error(e);
		}
		
	});
	
	//发布
	fresh_post_sub.on('click', function(e){
		e.preventDefault();
		var fid = fresh_post_fid.val();
		if(!fid) {
			fresh_forum_btn.click();
			return false;
		}

		$('#J_fresh_post_form').ajaxSubmit({
			dataType : 'html',
			beforeSubmit : function(){
				Wind.Util.ajaxBtnDisable(fresh_post_sub);
			},
			success : function(data, statusText, xhr, $form) {
				$("input[name=topictype]").remove();
				$("input[name=sub_topictype]").remove();
				if(data.indexOf("请选择主题分类") > 0){
					Wind.Util.ajaxBtnEnable(fresh_post_sub);
					//加载主题分类
					Wind.js(GV.JS_ROOT + 'pages/bbs/topicType.js?v=' + GV.JS_VERSION, function(){
						var url = GV.URL.TOPIC_TYPIC;
						var topic = new ShowTopicPop({url: url, fid: fid, callback: function(data){
								if(data){
									$(data).each(function(i, item){
									if(i === 0){
										item.name = 'topictype';
									}
									if(i === 1){
										item.name = 'sub_topictype';
									}
									$('#J_fresh_post_form').append('<input type="hidden" name = "'+item.name+'" value="'+item.val+'" />');
								});
								//模拟提交
								fresh_post_sub.click();
								}
							}});
							topic.init();
					});
					return false;
				}
				Wind.Util.ajaxBtnEnable(fresh_post_sub);
				if(Wind.Util.ajaxTempError(data, fresh_post_sub)) {
					if(data.indexOf('审核') > 0) {
						$form.resetForm();
						fresh_post_fid.val('');		//ff 隐藏不能reset
						fresh_forum_btn.find('.J_text').text('选择版块');
						fresh_post_sub.prop('disabled', true).addClass('disabled');
						fresh_upload_queue.hide();
						$('a.J_fresh_upload_del').click();
					}
					return;
				}

				$form.resetForm();
				fresh_post_fid.val('');		//ff 隐藏不能reset
				fresh_forum_btn.find('.J_text').text('选择版块');
				fresh_post_sub.prop('disabled', true).addClass('disabled');
				fresh_upload_queue.hide();
				$('a.J_fresh_upload_del').click();

				//提示 global.js
				Wind.Util.postTip({
					elem : fresh_post_ta,
					msg : '发送成功',
					callback : function(){
						if(!feed_lists.children().length) {
							window.location.reload();
						}
					}
				});

				if(!feed_lists.children().length) {
					return;
				}

				//积分奖励
				Wind.Util.creditReward();

				$('#J_news_tip').after(data);

				Wind.Util.avatarError($('#J_feed_lists dl').first().find('img.J_avatar'));

				//幻灯片
				var gallery_list = $('ul.J_gallery_list');
				if(gallery_list.length) {
					if($.fn.gallerySlide) {
						gallery_list.gallerySlide();
					}else{
						Wind.use('gallerySlide', function(){
							gallery_list.gallerySlide();
						});
					}
				}

			}
		})
	});
	

	//按钮禁用状态 global.js
	Wind.Util.buttonStatus(fresh_post_ta, fresh_post_sub);
	Wind.Util.ctrlEnterSub(fresh_post_ta, fresh_post_sub);

	//回复表情
	feed_lists.on('click', 'a.J_fresh_emotion', function(e){
		e.preventDefault();
		insertEmotions($(this), $($(this).data('emotiontarget')));
	});

	function swfHandle(){
		//图片上传
		Wind.js(GV.JS_ROOT +'util_libs/swfupload/plugins/swfupload.pluginMain.js?v='+ GV.JS_VERSION, function(){
			var fresh_upload_ul = $('#J_fresh_upload_queue > ul'), //队列ul
				fresh_upload_info = $('#J_fresh_upload_info'), //信息
				fresh_count = fresh_upload_info.children('.J_count'), //统计
				fresh_continue = fresh_upload_info.children('a.J_continue'), //继续上传
				PIC_LIMIT = parseInt(fast_upload_config.num_limit); //图片上传数量限制

			swfu = new SWFUpload({
				//debug : true,
				upload_url : IMG_UPLOAD,		//上传地址

				flash_url : GV.JS_ROOT+ 'util_libs/swfupload/Flash/swfupload.swf', 
				post_params: {
					uid : GV.U_ID,
					csrf_token : GV.TOKEN
				},
				/*custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},*/
				file_size_limit : fast_upload_config.size_limit,
				file_types : fast_upload_config.types,
				file_upload_limit : PIC_LIMIT,
				button_placeholder_id : "J_fresh_swfupload", 
				button_image_url: GV.URL.IMAGE_RES+'/blank.gif',
				button_width: "65",
				button_height: "29",
				button_cursor : -2,
				button_text: '',
				button_text_style: ".icon_photo{ font-size: 14;}",
				button_text_left_padding: 22,
				button_text_top_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				requeue_on_error : true,
				swfupload_load_failed_handler: function(){
					$('#J_fresh_post_wrap').on('click', '.a_photo', function(){
						alert("您还没有安装Flash插件,不能上传图片");
					});
				},
				file_dialog_start_handler : function(){
					fresh_upload_queue.show();

					if(!fresh_post_fid.val()) {
						Wind.Util.resultTip({
							error : true,
							msg : '请选择版块'
						});
					}
					
					if(PIC_LIMIT) {
						//有数量限制

						if(fresh_upload_ul.children().length == (PIC_LIMIT+1)) {
							return;
						}

						fresh_upload_info.children('.J_count').text(PIC_LIMIT);

						var pic_li_arr = [];
						for(i=1; i <= PIC_LIMIT; i++) {
							pic_li_arr.push('<li class="J_pic_empty">'+ i +'</li>');
						}

						fresh_upload_ul.prepend(pic_li_arr.join(''));
					}else{
						fresh_upload_info.remove();
					}
				},
				file_queued_handler : function (file) {

					//填充图片显示位置
					var empty_box = fresh_upload_queue.find('li.J_pic_empty:eq(0)');
					if(!PIC_LIMIT && !empty_box.length) {
						//数量不限且无空位
						fresh_upload_ul.append('<li class="J_pic_empty"></li>');
						empty_box = fresh_upload_queue.find('li.J_pic_empty:eq(0)');
					}

					if(empty_box.length) {
						empty_box.replaceWith('<li id="'+ file.id +'" data-pos="'+ empty_box.text() +'"><div class="schedule"><em>0%</em><span style="width:0%;"></span></div></li>');
					}else {
						this.cancelUpload(file.id);//超出则取消上传
						fresh_upload_info.text('数量超出限制');
					}

				},
				file_queue_error_handler : function(file, errorCode, message) {
					try {
						var er;
						switch (errorCode) {
							case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
								er = "您上传的图片\""+ file.name +"\"太大了"+ "，单张最大限制为: " + this.settings.file_size_limit;
								break;
							case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
								er = "请不要上传0字节的文件";
								$.error("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
								break;
							case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
								er = "错误的文件类型";
								break;
							case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
								er = '最多只能上传'+ this.settings.file_upload_limit +'张图片';
								break;
							default:
								$.error("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
								break;
						}
						if(er) {
							Wind.Util.resultTip({
								error: true,
								msg: er,
								follow: $('.a_photo_flash')
							});
						}
					} catch (ex) {
		       		 $.error(ex);
		   		 }
				},
				file_dialog_complete_handler : function (numFilesSelected, numFilesQueued) {
					//开始上传
					if(numFilesSelected > 1) {
					}
					
					this.startUpload();
				},
	   		//file_size_limit : "20480",
				upload_start_handler : function (file) {
					//开始上传文件前触发的事件处理函数
					try {
						//up_tip.html('<span class="tips_loading">正在上传并校验</span>');
					}
					catch (ex) {}
			
					return true;
				},
				upload_progress_handler : function(file, bytesLoaded, bytesTotal) {
					try {
						var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
						var file_detail = $('#'+file.id);
						file_detail.find('em').text(percent + '%');//显示进度
						file_detail.find('span').css('width',percent + '%');//使用宽度来显示进度条
					} catch (ex) {
						$.error(ex);
					}
				},
				upload_success_handler : function(file, serverData) {
					try {
						var file_detail = $('#'+file.id);
						var data = $.parseJSON(serverData);//console.log(this.settings.post_params);console.log(fresh_post_fid.val());
						//console.log(file);
						if(data.state == 'success') {
							var _data = data.data;

							file_detail.data({
								//'serverData': _data,
								//'aid' : _data.aid
							}).addClass('uploaded');
							file_detail.html('<a data-id="'+ file.id +'" class="del J_fresh_upload_del" href="">删除</a><img alt="上传完成" data-id="'+ _data.aid +'" src="'+ _data.path +'" width="100" height="100" alt="" /><input type="hidden" name="flashatt['+ _data.aid +'][desc]" value="'+ file.name +'">');

							//数量
							var count = fresh_upload_info.children('.J_count');					//统计
							count.text(count.text() - 1);
						}else{
							file_detail.replaceWith('<li class="J_pic_empty">'+ file_detail.data('pos') +'</li>');
							//restLimit();
							Wind.Util.resultTip({
								error : true,
								msg : data.message
							});

							//出错 队列-1
							var stats = swfu.getStats();
							stats.successful_uploads--;
							swfu.setStats(stats);
							return;
						}
						
						//上传成功后，点击可改描述
						//显示可上传数量
					} catch (ex) {
						$.error(ex);
					}
				},
				upload_complete_handler : function (file) {
					//完成
					try {
						//如果上传完成后，还有未上传的队列，那和继续自动上传
						if (this.getStats().files_queued === 0) {
						} else {	
							this.startUpload();
						}
					} catch (ex) {
						$.error(ex);
					}
				},
				queue_complete_handler : function(){
				}

			});

			//删除
			fresh_upload_queue.on('click', 'a.J_fresh_upload_del', function(e){
				e.preventDefault();
				var li = $(this).parents('li'),
					pos = li.index()+1,
					uploaded_last = fresh_upload_queue.find('li.uploaded:last');

				if((uploaded_last.index()+1) !== pos) {
					//删除的不是最后一张
					li.insertAfter(uploaded_last);
					pos = li.index()+1;
				}
				li.replaceWith('<li class="J_pic_empty" data-pos="'+ pos +'">'+ pos +'</li>');

				restLimit();
			});

			//关闭上传
			$('#J_fresh_upload_close').on('click', function(e){
				e.preventDefault();
				fresh_upload_queue.hide();
			});
			
			//队列数减1
			function restLimit() {
				var stats = swfu.getStats();
				stats.successful_uploads--;
				swfu.setStats(stats);

				var count = fresh_upload_info.children('.J_count');					//统计
				count.text(parseInt(count.text()) + 1);
			}

		});
	}

	

})();