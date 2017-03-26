/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-投票帖
 * @Author	: linhao87@gmail.com, TID
 * @Depend	: jquery.js(1.7 or later), global.js
 * $Id$
 */


;(function(){
	//投票提交
	Wind.use('ajaxForm', function(){
		$('#J_read_vote_form').ajaxForm({
			dataType : 'json',
			success : function(data){
				if(data.state === 'success') {
					Wind.Util.resultTip({
						msg : data.message,
						callback : function(){
							Wind.Util.reloadPage(window);
						}
					});
				}else if(data.state === 'fail'){
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
				}
			}
		});
	});
	


	//查看参与人员
	var vote_temp = '<div tabindex="0" class="core_pop_wrap J_vote_u" id="_ID" style="position:absolute;">\
	<div class="core_pop">\
		<div class="pop_vote_member">\
			<div class="pop_top">\
				<a href="" class="pop_close J_vote_u_close">关闭</a>\
				<strong>参与人员</strong>\
			</div>\
			<div class="pop_cont">\
				<div class="pop_loading J_loading"></div>\
				<ul class="cc J_vote_u_list" style="display:none;"></ul>\
			</div>\
			<div class="pop_bottom">\
				<button type="button" class="btn J_vote_u_close">关闭</button>\
			</div>\
		</div>\
	</div>\
</div>';
	var vote_lock = false;
	$('a.J_vote_list_show').on('click', function(e){
		e.preventDefault();
		var $this = $(this),
			item = $('#J_vote_u_'+ $this.data('key'));
		if(item.length) {

			//列表已存在则显示
			item.show().focus();

			//定位
			uListPos($this, item);

		}else{
			//列表不存在
			$('body').append(vote_temp.replace('_ID', 'J_vote_u_'+ $this.data('key')));
			var _item = $('#J_vote_u_'+ $this.data('key'));

			//定位
			uListPos($this, _item);

			_item.focus().on('click', '.J_vote_u_close', function(e){
				//关闭
				e.preventDefault();
				_item.hide();
			}).on('mouseenter', function(){
				vote_lock = true;
			}).on('mouseleave', function(){
				vote_lock = false;
				_item.focus();
			}).on('blur', function(){
				//失焦隐藏
				if(!vote_lock) {
					_item.hide();
				}
			});

			//获取数据
			$.getJSON($this.attr('href'), function(data){
				if(data.state == 'success') {
					var _data = data.data, u_arr = [];

					if(_data) {
						$.each(_data, function(i, o){
							u_arr.push('<li><a href="'+ GV.U_CENTER + '&uid=' + i +'" target="_blank" title="'+ o +'">'+ o +'</a></li>');
						});
						_item.find('.J_loading').hide().siblings('.J_vote_u_list').show().html(u_arr.join(''));
					}else{
						_item.find('.J_loading').parent().html('<div class="not_content_mini"><i></i>暂无参与人员</div>');
					}
				}else if(data.state == 'fail'){
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
				}
			});
		}
	});

	function uListPos(elem, list){
		list.css({
			left : elem.offset().left,
			top : elem.offset().top + elem.innerHeight()
		});
	}

	//投票多选限制
	var vote_item = $('ul.J_vote_item'),										//投票区
		vote_checkbox = vote_item.find('input:checkbox'),			//投票框
		vote_max = parseInt(vote_item.data('max'));						//多选数
	if(vote_max) {
		//存在最多项限制
		vote_checkbox.on('change', function(){

			//选中数是否等于多选数
			if(vote_checkbox.filter('input:checkbox:checked').length === vote_max) {
				$.each(vote_checkbox, function(){
					if(!$(this).prop('checked')) {
						//未选中项不可用
						$(this).prop('disabled', true);
					}
				});
			}else{
				vote_checkbox.filter(':disabled').prop('disabled', false);
			}
		});
	}
})();