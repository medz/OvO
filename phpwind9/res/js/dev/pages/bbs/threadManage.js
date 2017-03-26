/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-帖子列表操作
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), jquery.form, TID
 * $Id: threadForumManage.js 24143 2013-01-22 06:48:25Z hao.lin $
 */

Wind.use('dialog', function(){
	var dialog_post = $('.J_post_manage_col a:not(.J_manage_single)');		//管理操作项

	//var	iframe_poped = false,											//表示帖子面板里的具体操作未弹出
	var posts_checkbox = $('#J_posts_list input.J_check'),		//所有帖子选择框
		post_manage_main = $('#J_post_manage_main'),					//帖子操作面板
		post_checked_count = $('#J_post_checked_count'),				//帖子操作面板里的选中篇数
		is_ie6 = $.browser.msie && $.browser.version < 7,				//ie6
		checkall = $('input.J_check_all');								//全选
	
	posts_checkbox.prop('checked', false);
	checkall.prop('checked', false);

	//点击帖子框
	posts_checkbox.on('change', function() {
		var $this = $(this), checked_length = posts_checkbox.filter(':checked').length;

		//选中篇数
		post_checked_count.text(checked_length);
		$('#J_manage_checked_count').text(checked_length);
		
		//判断选择&取消复选框，帖子操作面板或面板里的具体操作是否已弹出
		if(this.checked) {
			$this.parents('tr').addClass('tr_check');

			if($('#J_post_manage_main:visible').length || $('#J_posts_manage_pop').length) {
				//已经弹出
				return;
			}

			if(dialog_post.length && dialog_post.length <=2) {
				//小于1 不弹窗(1+1)
				return;
			}

			showManMainPop();
			
		}else if(!this.checked) {
			$this.parents('tr.tr_check').removeClass('tr_check');

			if(!checked_length) {
				//取消所有复选框
				post_manage_main.hide();
				Wind.dialog.closeAll();
			}
		}
		
	});
	

	//帖子操作面板_全选&取消全选
	$('#J_post_manage_checkall').on('click', function(e) {
		e.preventDefault();
		var $this = $(this);
		
		if($this.text() === '全选') {
			var not_top = posts_checkbox.filter(':not([topped=1])');
			posts_checkbox.prop('checked', true);
			checkall.prop('checked', true);
			$this.text('取消全选');
			
			setTimeout(function(){
				//过滤置顶贴
				posts_checkbox.filter('[topped=1]').prop('checked', false);
				post_checked_count.text(not_top.length);
				not_top.parents('tr').addClass('tr_check');
			}, 0);
		}else{
			posts_checkbox.prop('checked', false);
			checkall.prop('checked', false);
			$this.text('全选');
			post_checked_count.text('0');
			posts_checkbox.parents('tr.tr_check').removeClass('tr_check');
		}
		
	});

	checkall.on('click', function() {
		$('#J_post_manage_checkall').click();
		if(!dialog_post.length || dialog_post.length >2) {
			//管理项大于1 弹窗
			if(this.checked) {
				showManMainPop();
			}else{
				post_manage_main.hide();
			}
		}
		
	});
	
	
	//关闭帖子操作面板
	$('#J_post_manage_close').on('click', function(e){
		e.preventDefault();
		post_manage_main.hide();

		closeCheck();
	});
	
	//帖子操作iframe弹窗，考虑创建前台的common.js
	dialog_post.on( 'click',function(e) {
		e.preventDefault();
		var posts_checked = posts_checkbox.filter(':checked'),
			role = $(this).parents('.J_post_manage_col').data('role'),
			type = $(this).data('type');

		//取消全选，未选择帖子时点击操作弹出提示
		if(!posts_checked.length && role !== 'readbar') {
			Wind.Util.resultTip({
				error : true,
				msg : '请至少选择一个帖子'
			});
			return false;
		}
		
		var $this = $(this),
			xid_arr = [];
			
		$.each(posts_checked, function(i, o){
			xid_arr.push($(this).val());
		});

		//区分传输数据
		var _data = {};
		if(role == "read") {
			//阅读页 楼层
			_data['tid'] = TID;
			_data['pids[]'] = xid_arr;
		}else if(role == "readbar"){
			//阅读页 帖子
			_data['tid'] = TID;
			_data['pids[]'] = 0;
		}else if(role == "list"){
			//帖子列表页
			_data['tids[]'] = xid_arr;
		}

		Wind.dialog.closeAll();
		Wind.Util.ajaxMaskShow();
		$.post($this.prop('href'), _data, function(data) {
			Wind.Util.ajaxMaskRemove();
			if(Wind.Util.ajaxTempError(data)) {
				return;
			}

			//成功
			Wind.dialog.html(data, {
				id : 'J_posts_manage_pop',
				isMask		: false,	//无遮罩
				isDrag : true,
				resize : false,
				callback	: function(){
					Wind.use('ajaxForm', function(){
						post_manage_main.hide();
						manageHandles();

						$('#J_sub_topped').data({
							'type': type,
							'role': role
						});
					});
				},
				onClose : function(){
					closeCheck();
				}
			});
			
		}, 'html');
		
	});
	
	//关闭核对
	function closeCheck(){
		if(checkall.prop('checked')) {
			$('#J_post_manage_checkall').click();
		}else{
			posts_checkbox.filter(':checked').prop('checked', false).parents('tr.tr_check').removeClass('tr_check');
		}
	}

	//显示窗口
	function showManMainPop(){
		if(is_ie6) {
			Wind.Util.popPos(post_manage_main);
		}else{
			post_manage_main.css({
				position : 'fixed',
				top : ($(window).height() - post_manage_main.height())/2,
				left : ($(window).width() - post_manage_main.width())/2
			}).show();
		}
		
		//窗口拖动
		Wind.use('draggable', function(){
			post_manage_main.draggable( { handle : '.pop_top'} );
		});
	}
	
});


function manageHandles(){
	
	//点击“置顶”等文字显示&隐藏相应内容
	$('label.J_toggle').on('click', function(e){
			$(this).parents('li').addClass('current')
				.siblings('li.current').removeClass('current');
	});
	
	$('input.J_toggle').on('change', function(){
		var $this = $(this);
		if($this.attr('checked')) {
			$this.parents('li').addClass('current')
				.siblings('li.current').removeClass('current');
		}
	});
	
	//选择“取消置顶”后禁用有效期
	
	$('#J_topped_select').on('change', function(){
		var v = $(this).val(),
			topped_time = $('#J_topped_time'),
			J_topped_forums = $('#J_topped_forums');
		if(v === '0') {
			topped_time.addClass('disabled').attr('disabled', 'disabled').val('');
			J_topped_forums.hide();
		}else if(v === '3') {
			J_topped_forums.show();
		}else{
			topped_time.removeClass('disabled').removeProp('disabled');
			J_topped_forums.hide();
		}
	});
	
	//加亮 字体
	$('a.J_font_style').on('click', function(e){
		e.preventDefault();
		var $this = $(this);
		$this.toggleClass('current');
		if($this.hasClass('current')) {
			$('#' +$this.data('id')).attr('checked', 'checked');
		}else{
			$('#' +$this.data('id')).removeAttr('checked');
		}
	});
	
	//提前时间，仅限数字，判断输入或粘贴
	var uptime = $('#J_uptime');
	if(uptime.length) {
		uptime.on('keyup', function(e){
			var $this = $(this), v = $this.val();
			v = v.replace(/[^\d]/g,'');
			$this.val(v);
		});
	
		uptime[0].onpaste = function(){
			setTimeout(function(){
				var v = uptime.val();
				v = v.replace(/[^\d]/g,'');
				uptime.val(v);
			}, 150);
		}
	}
	
	var posts_manage_pop = $('#J_posts_manage_pop'),
		date = posts_manage_pop.find('input.J_date'),
		datetime = posts_manage_pop.find('input.J_datetime');
	if(date.length || datetime.length) {
		Wind.use('datePicker',function() {
			date.datePicker({
				time : false
			});
			datetime.datePicker({
				time : true
			});
		});
	}

	var color_pick = posts_manage_pop.find('.J_color_pick');
	if(color_pick.length) {
		Wind.use('colorPicker', function() {
			color_pick.each(function(){
				var bg_elem = $(this).find('.J_bg');

				$(this).colorPicker({
					zIndex : 12,
					default_color : 'url("'+ GV.URL.IMAGE_RES +'/transparent.png")',	//默认透明背景
					callback:function(color) {
						bg_elem.css('background',color);
						$(this).next('.J_hidden_color').val(color.length === 7 ? color : '');
					}
				});
			});
		});
	}
	
	
	//点击写入操作理由
	$('#J_resson_select').on('change', function(){
		$('#J_resson_input').val($(this).val());
	});
	
	//获取所有被选择帖子的fid并提交
	var btn = $('#J_sub_topped'),
		form = $('#J_post_manage_ajaxForm');
	form.on('submit', function(e){
		e.preventDefault();
		var checks = $('#J_posts_list input.J_check:checked'),
			xid_arr = [],
			role = btn.data('role'),
			type = btn.data('type'),
			_data = {};

		$.each(checks, function(){
			xid_arr.push($(this).val());
		});

		if(role == "read") {
			//阅读页 楼层
			_data['tid'] = TID;
			_data['pids[]'] = xid_arr;
		}else if(role == "readbar"){
			//阅读页 帖子
			_data['tid'] = TID;
			_data['pids[]'] = 0;
		}else if(role == "list"){
			//帖子列表页
			_data['tids[]'] = xid_arr;
		}

		Wind.Util.ajaxBtnDisable(btn);
		form.ajaxSubmit({
			dataType	: 'json',
			data : _data,
			success		: function(data){
				Wind.Util.ajaxBtnEnable(btn);
				if(data.state === 'success') {
					Wind.dialog.closeAll();

					Wind.Util.resultTip({
						msg : data.message,
						callback : function(){
							if(data.referer) {
								location.href = decodeURIComponent(data.referer);
							}else{
								if(role == "read") {
									//阅读页 楼层
									if(type == 'delete') {
										//删除
										checks.parents('div.J_read_floor').remove();
										window.location.reload();
									}
								}else if(role == 'readbar'){
									//阅读页 帖子
									if(type !== 'norefresh') {
										window.location.reload();
									}else{
										Wind.Util.creditReward();
									}
								}else if(role == 'list'){
									//列表页
									window.location.reload();
								}
							}
						}
					});
				}else if(data.state === 'fail'){
					Wind.Util.resultTip({
						follow : btn,
						error : true,
						msg : data.message
					});
				}
			}
		});
	});
	
/*
 * 复制
*/
	var topic_data = {},
			postmanage_topictype = $('#J_postmanage_topictype');

	$('#J_postmanage_forum').on('change', function(){
		var $this = $(this);
		$.post($this.data('url'), {fid : this.value}, function(data){
			if(data.state == 'success') {
				var postmanage_topic = $('#J_postmanage_topic'),
						t_arr = [];

				topic_data = data.data;
				if(topic_data) {
					for( i in topic_data) {
						t_arr.push('<option value="'+ i +'">'+ topic_data[i]['name'] +'</option>');
					}

					postmanage_topic.show();
					postmanage_topictype.html('<option value="0">请选择分类</option>' + t_arr.join(''));

					setSubTopicType(postmanage_topictype.val(), topic_data);
				}else{
					postmanage_topic.hide();
				}

			}else if(data.state == 'fail') {

			}
		}, 'json');
	});

	postmanage_topictype.on('change', function() {
		setSubTopicType(this.value, topic_data);
	});

	$('#J_postmanage_type_topictype').on('change', function(){
		setSubTopicType(this.value, THREAD_SORT);
	});

	//显示二级分类
	function setSubTopicType(v, data) {
		var postmanage_subtopictype = $('#J_postmanage_subtopictype');
		v = parseInt(v);

		if(!v) {
			postmanage_subtopictype.hide();
			return ;
		}
		
		var sub_data = data[v]['sub_type'];
		if(sub_data) {
			var sub_arr = [];
			sub_arr.push('<option value="0">请选择分类</option>');
			for( i in sub_data) {
				sub_arr.push('<option value="'+ i +'">'+ sub_data[i]['name'] +'</option>');
			}
			postmanage_subtopictype.show().html(sub_arr.join(''));
		}else{
			postmanage_subtopictype.hide();
		}
		
	}

	//推送
	$('#J_push_select_initiative').on('change', function(){
		$.post($(this).data('url'), {
				pageid : this.value,
				fromtype : document.getElementById('J_fromtype').value
			}, function(data){
			if(data.state == 'success') {
				$('#J_push_select_passive').html(data.html);
			}else if(data.state == 'fail') {
				//global.js
				Wind.Util.resultTip({
					error : true,
					msg : data.message
				});
			}
		}, 'json');
	});
	
}