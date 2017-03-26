/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-设置-个性标签
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), global.js, TAG_DEL
 * $Id$
 */
 
Wind.use('ajaxForm', function(){
	var tag_add = $('#J_tag_add'),
			my_tags = $('#J_my_tags'),
			tag_count = $('#J_tag_count'),
			hot_tags = $('#J_hot_tags'),
			tag_form = $('#J_tag_form'),
			my_tags_none = $('#J_my_tags_none'),
			lock = false;

	//global.js
	Wind.Util.buttonStatus(tag_form.find('input:text'), tag_form.find('button:submit'));

	//添加标签
	tag_form.ajaxForm({
		dataType : 'json',
		beforeSubmit : function(arr, $form, options) {
			//global.js
			Wind.Util.ajaxBtnDisable(tag_add);
		},
		success : function(data, statusText, xhr, $form) {
			Wind.Util.ajaxBtnEnable(tag_add, 'disabled');

			if(data.state == 'success') {
				if(!my_tags.children().length) {
					my_tags.show();
					my_tags_none.hide();
				}

				my_tags.append('<span class="rel J_tag_rel" data-tag="'+ data.data.name +'"><em>'+ data.data.name +'</em><a class="del J_tag_del" href="" data-tagid="'+ data.data.id +'">×</a></span>');
				tag_form.resetForm();
				tag_count.text(tag_count.text()-1);
			}else if(data.state == 'fail') {
				Wind.Util.resultTip({
					error : true,
					elem : tag_add,
					follow : true,
					msg : data.message
				});
			}
		}
	});

	//删除
	my_tags.on('click', 'a.J_tag_del', function(e){
		e.preventDefault();
		var $this = $(this);

		if(lock) {
			lock = true;
			return false;
		}
		lock = true;

		Wind.Util.ajaxMaskShow();
		$.post(TAG_DEL, {
			tagid : $this.data('tagid')
		}, function(data){
			Wind.Util.ajaxMaskRemove();
			if(data.state == 'success') {
				tag_count.text(parseInt(tag_count.text())+1);
				$this.parent().fadeOut(function(){
					$(this).remove();

					if(!my_tags.children().length) {
						my_tags.hide();
						my_tags_none.show();
					}
					lock = false;
				});
			}else if(data.state == 'fail'){
				Wind.Util.resultTip({
					error : true,
					elem : $this,
					follow : true,
					msg : data.message
				});
				lock = false;
			}

		}, 'json');
	}).on('click', '.J_tag_rel', function(e){
		//标签跳转

		if($(e.target).hasClass('J_tag_del')) {
			//过滤删除
			return;
		}
		window.open(TAG_REL+$(this).data('tag'));
	});

	//热门标签 添加
	hot_tags.on('click', 'a.J_tag_hot_add', function(e){
		e.preventDefault();
		var $this = $(this);

		if(lock) {
			return false;
		}
		lock = true;

		$.post(this.href, function(data){
			lock = false;

			if(data.state == 'success') {
				if(!my_tags.children().length) {
					my_tags.show();
					my_tags_none.hide();
				}

				my_tags.append('<span class="rel J_tag_rel" data-tag="'+ $this.text() +'"><em>'+ $this.text() +'</em><a class="del J_tag_del" href="{" data-tagid="'+ $this.data('tagid') +'">×</a></span>');
				tag_count.text(tag_count.text()-1);

			}else if(data.state == 'fail'){
				Wind.Util.resultTip({
					error : true,
					elem : $this,
					follow : true,
					msg : data.message
				});
			}
		}, 'json');
	});
	

	//换一组
	var start = 2,
		lock = false;
	$('a.J_change_tags').on('click', function(e){
		e.preventDefault();
		var rel = document.getElementById($(this).data('rel'));		//替换对象

		if(lock) {
			return false;
		}
		lock = true;

		getGroup($(this), start);
		
	});

	function getGroup(elem, _start){
		var url = elem[0].href,
			add = elem.data('add');

		Wind.Util.ajaxMaskShow();
		$.post(url, {
			start : _start
		},function(data){
			//start++;
			lock = false;
			Wind.Util.ajaxMaskRemove();

			if(data.state == 'success') {
				var _data = data['data']['list'],
					arr = [];

				if(_data) {
					$.each(_data, function(i, o){
						arr.push('<a href="'+ add + '&tagid=' + o.tag_id +'" data-tagid="'+ o.tag_id +'" class="J_tag_hot_add">'+ o.name +'</a>');
					});

					$('#J_hot_tags').html(arr.join(''));
				}

				//新页
				start = data['data']['page'];

			}else if(data.state == 'fail'){
				Wind.Util.resultTip({
					error : true,
					follow : elem,
					msg : data.message
				});
			}
		}, 'json');
	}
});