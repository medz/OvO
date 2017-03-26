/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-私信&消息
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later),
 * $Id: message_index.js 28797 2013-05-24 05:46:01Z hao.lin $
 */
 
;(function(){
	//ie6 hover
	if($.browser.msie && $.browser.version < 7) {
		$('#J_notice_list > .J_notice_item').hover(function(){
			$(this).addClass('ct_ie6');
		}, function(){
			$(this).removeClass('ct_ie6');
		});
	}

	var checkbox = $('input.J_check_all, input.J_check'),
		check_op = $('.J_check_op');			//批量操作项

	var op_bar = $('.J_op_bar'),				//管理操作上下栏
		op_manage = $('.J_op_manage'),			//批量管理容器
		checks = $('.J_notice_item input.J_check');		//

	//显示批量管理
	$('a.J_msg_manage_show').on('click', function(e) {
		e.preventDefault();
		op_bar.show();
		checks.show();

		op_manage.hide();
	});

	//取消批量管理
	$('a.J_msg_manage_hide').on('click', function(e){
		e.preventDefault();
		op_bar.hide();
		checks.hide();
		check_op.css('visibility', 'hidden');
		checkbox.prop('checked', false);

		op_manage.show();
	});


	//选择后显示操作
	
	
	checkbox.removeAttr('checked');
	$('input.J_check_all, input.J_check').on('change', function(){
		//延时
		setTimeout(function(){
			var checkboxes = $('input.J_check:checked');
			if(checkboxes.length) {
				check_op.css('visibility', 'visible');
				var unreads = [];
				checkboxes.each(function(){
					var unread = $(this).closest('.J_notice_item').find('.J_unread');
					if(unread.length){
						unreads.push(unread);
					}
				})
				if(unreads.length){
					check_op.find('a:eq(1)').show();
				}else{
					check_op.find('a:eq(1)').hide();
				}
			}else{
				check_op.css('visibility', 'hidden');
			}
		}, 0);
		
	});

	//删除
	$('a.J_msg_del').on('click',function(e) {
		e.preventDefault();
		var $this = $(this);
		Wind.Util.ajaxConfirm({
			elem : $this,
			href : $this.prop('href'),
			msg : $this.data('msg') ? $this.data('msg') : '确定删除选中的通知吗？',
			callback : function(){
				var item = $this.parents('.J_notice_item');
				if($this.data('type') == 'msg') {
					//私信页
					unReadCount(item.find('a.J_unread'), $('#J_unread_count'));
				}

				item.fadeOut(function(){
					//清空了
					if(!item.siblings('.J_notice_item').length) {
						location.reload();
					}

					$(this).remove();
				});


			}
		});
	});

	//黑名单
	$('a.J_addblack').on('click',function(e) {
		e.preventDefault();
		var $this = $(this),
			type = $this.data('type'),
			type_text = (type == 'msg' ? '通知' : '私信');

		$.post(this.href, function(data){
			if (data.state === 'success') {
				Wind.Util.resultTip({
					elem : $this,
					follow : true,
					msg : '已把 '+ $this.data('user') +' 列入黑名单，您不会再收到Ta的' + type_text
				});

				//更改跳转
				$this.replaceWith('<a href="'+ $this.data('referer') +'">查看黑名单</a>');
			}else if(data.state === 'fail') {
				Wind.Util.resultTip({
					error : true,
					elem : $this,
					follow : true,
					msg : data.message
				});
			}
		}, 'json');

	});


	//数量统计
	function unReadCount(items, count){
		var read_c = parseInt(count.text());		//原未读总数

		var c = 0;
		for(i=0, len=items.length; i<len; i++) {
			c = c + $(items[i]).data('count');
		};

		var result = read_c - c;
		if(result == 0) {
			count.remove();
		}else{
			count.text(result);
		}
						
		items.remove();

		Wind.Util.resultTip({
			msg : '操作成功'
		});
	}

	//私信搜索
	var search_btn = $('#J_msg_search_btn');

	Wind.Util.buttonStatus($('#J_msg_search_input'), search_btn);

	Wind.use('ajaxForm', function(){
		$('#J_msg_search_form').ajaxForm({
			dataType : 'json',
			success : function(data){
				if(data.state == 'success') {
					location.href = decodeURIComponent(data.referer);
				}else if(data.state == 'fail') {
					Wind.Util.formBtnTips({
						error : true,
						wrap : search_btn.parents('.content_nav'),
						msg : data.message
					});
				}
			}
		});
	})

})();