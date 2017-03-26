/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-发投票帖
 * @Author	: linhao87@gmail.com, TID
 * @Depend	: jquery.js(1.7 or later), global.js
 * $Id$
 */


;(function(){
	
	var max_val = $('#J_vote_max_size').val(),			//设定的总图片大小
		max_size = '',									//转换后的总图片大小，单位kb
		max_limit = $('#J_max_file_uploads').val(),		//数量限制
		post_vote_list = $('#J_post_vote_list'),
		post_vote_add = $('#J_post_vote_add'),
		vote_img = $('input.J_vote_img'),
		change = false;

	if(max_limit) {
		max_limit = parseInt(max_limit);
	}

	//页面载入初始化
	$('input.J_vote_img').val('');

	if($('#J_menu_vote').hasClass('current')) {
	
	}
	var vote_item_html = '<li data-id="_ACTION__IMGINDEX">\
			<input name="_ACTION_option[_IMGINDEX]" type="text" class="input length_5 fl" />\
			<div class="icon_image_up fl">\
				<a href="javascript:;" tabindex="-1">上传图片</a>\
				<input class="J_vote_img" data-preview="#J__ACTION_preview_IMGINDEX" name="_ACTION_optionpic__IMGINDEX" type="file" title="可插入图片">\
			</div>\
			<a class="icon_del J_post_vote_del" href="#">删除</a>\
			<div class="c"></div>_IMGWRAP\
		</li>',
		img_not_ie = '<div class="vote_preview" style="display:none;">\
				<a href="" class="icon_del J_vicon_loc_del">删除</a>\
				<img id="J__ACTION_preview_IMGINDEX" class="J_vote_preview_img" style="display:none;" width="120" height="120" />\
			</div>',
		img_ie = '<div class="vote_preview_ie J_ie_imgfile" style="display:none;"><span class="fl J_name"></span><a href="" class="icon_del J_vicon_loc_del">删除</a></div></span>';

	//ie6 hover
	if($.browser.msie && $.browser.version < 7) {
		post_vote_list.children('li').hover(function(e){
			$(this).addClass('hover');
		}, function(e){
			$(this).removeClass('hover');
		});
	}
	
	//增加投票项
	var vote_i = -1;
	post_vote_add.on('click', function(e){
		e.preventDefault();
		var $this = $(this),
			li = $this.parent('li'),
			last_id = parseInt(li.prev().data('id')),
			input_index = '';
		vote_i++;

		if($.browser.msie) {
			vote_item_html = vote_item_html.replace('_IMGWRAP', img_ie);
		}else{
			vote_item_html = vote_item_html.replace('_IMGWRAP', img_not_ie);
		}

		if($this.data('role') !== 'add') {
			//编辑
			vote_item_html = vote_item_html.replace(/_ACTION_/g, 'new');
			li.before(vote_item_html.replace(/_IMGINDEX/g, vote_i));
		} else {
			//添加
			vote_item_html = vote_item_html.replace(/_ACTION_/g, '');
			li.before(vote_item_html.replace(/_IMGINDEX/g, last_id + 1));
		}

		$('input.J_vote_img').uploadPreview({
			maxWidth : 150
		});

		//超出限制 隐藏
		if(post_vote_list.children().length >= max_limit+1 && max_limit) {
			post_vote_add.parent().hide();
		}
	});
	
	
	//删除投票项
	var del_lock = false;
	post_vote_list.on('click', 'a.J_post_vote_del', function(e){
		e.preventDefault();
		var $this = $(this),
			saved = $this.data('saved');
			
		if(post_vote_list.children().length > 3) {
			if(saved) {
				//删除已保存的投票项
				if(del_lock) {
					return false;
				}
				del_lock = true;

				$.post(this.href, function(data){
					del_lock = false;
					if(data.state == 'success') {
						$this.parent('li').remove();
						post_vote_add.parent().show();
					}else if(data.state == 'fail'){
						Wind.Util.resultTip({
							error : true,
							elem : $this,
							follow : true,
							msg : data.message
						});
					}

				}, 'json');

			}else{
				$this.parent('li').remove();
				post_vote_add.parent().show();
			}
			
		}else{
			//global.js
			Wind.Util.resultTip({
				error : true,
				elem : $this,
				follow : true,
				msg : '至少要有2个投票项'
			});
		}
	}).on('click', 'a.J_vicon_loc_del', function(e){
		e.preventDefault();
		var li = $(this).parents('li');
		$(this).parent().hide();

		var input = li.find('input.J_vote_img')[0],
			_form=document.createElement('form'),
			org_pos=input.nextSibling;

		//清空input值
		document.body.appendChild(_form);
		_form.appendChild(input);
		_form.reset();
		org_pos.parentNode.insertBefore(input, org_pos);
		document.body.removeChild(_form);
	}).on('click', 'a.J_vicon_saved_del', function(e){
		//删除已保存的图片
		e.preventDefault();
		var $this = $(this);

		$.post(this.href, function(data){
			if(data.state == 'success') {
				$this.parent().hide();
			}else if(data.state == 'fail'){
				Wind.Util.resultTip({
					error : true,
					follow : $this.parent(),
					msg : data.message
				});
			}
		}, 'json');

	});

	
	if(max_val) {
		if(RegExp(/M$/).test(max_val)) {
			//M
			max_size = Number(max_val.replace('M', '')) * 1024;
		}else{
			//KB
			max_size = parseInt(max_val.val());
		}
	}
	
	
	//预览组件
	var vote_img = $('input.J_vote_img');
	if (vote_img.length) {
		Wind.use('uploadPreview', function(){
			vote_img.uploadPreview({
				maxWidth : 150
			});
		})
	};
	
	
	post_vote_list.on('change', 'input.J_vote_img', function(){
		//选择图片
		var $this = $(this);
		if(!$.browser.msie) {
			change = true;
			$this.attr('title', '重新选择图片');
			
			var total_size = 0;
				$.each($('input.J_vote_img'), function(i, o){
					if(o.value) {
						total_size += o.files[0].size;
					}
				});
				
				var preview = $( $this.data('preview') );
				if(total_size/1024 > max_size) {
					Wind.Util.resultTip({
						error : true,
						msg : '图片总体大小超出限制，超出'+ parseInt(total_size/1024 - max_size) +'KB'
					});
					this.value = '';
					preview.css('visibility', 'hidden');
					$this.attr('title', '可插入图片');
				}else{
					preview.fadeIn().parent().show();
					var saved_del = preview.siblings('a.J_vicon_saved_del');
					if(saved_del.length) {
						saved_del.removeClass('J_vicon_saved_del').addClass('J_vicon_loc_del');
					}
				};
		
		}else{
			//ie 文件名显示
			var IMG_TYPE_PATTERN = /^png|gif|jpg|jpeg|bmp/i;
			var li = $(this).parents('li'),
				ie_imgfile = li.find('.J_ie_imgfile'),
				val = this.value,
				lastindex = val.lastIndexOf('\\'),
				filename = val.substring(lastindex+1, val.length),		//过滤路径 留下文件名
				type_arr = filename.split('.'),
				type = type_arr[type_arr.length-1];

			if(!IMG_TYPE_PATTERN.test(type)) {
				alert('您上传的不是图片格式的文件！');
				this.value = '' ;
				return false;
			}
			if(ie_imgfile.length) {
				//替换
				ie_imgfile.show().children('.J_name').text(filename);
			}else{
				//写入
				li.append('<div class="vote_preview_ie J_ie_imgfile"><span class="fl J_name">'+ filename +'</span><a href="" class="icon_del J_vicon_loc_del">删除</a></div></span>').find('.vote_preview').remove();
			}
		}
	});


	//是否多选
	var post_vote_mcount = $('#J_post_vote_mcount');
		$('input.J_post_vote_radio').on('change', function(){
			var $this = $(this);
			if($this.data('type') === 'multiple') {
				post_vote_mcount.show();
			}else{
				post_vote_mcount.hide();
			}
		});
		
		if($('input.J_post_vote_radio:checked').data('type') === 'multiple') {
			post_vote_mcount.show();
		}else{
			post_vote_mcount.hide();
		}

})();