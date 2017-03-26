/*

 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 后台-权限复制 提交
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later), ajaxForm
 * $Id: forumTree_table.js 15724 2012-08-10 10:20:09Z hao.lin $
 */
;(function () {
	var tabs_nav = $('ul.J_tabs_nav'),
		loc_s = location.search,
		start = loc_s.indexOf('tab'),
		tab = loc_s.substring(start+4).replace('param', '');		//过滤出url的tab参数

	var tips_bubble = $('#J_tips_bubble');				//提示

	//触发当前tab
	if(start > 0 && tabs_nav.length >= 1) {
		$('#J_' + tab).click();
	}
	

	var rights_form_btn = $('#J_rights_form_btn'),
		rigths_form = $('#J_rigths_form');

	//权限复制
	var copy_select_all = $('#J_copy_select_all'),
		copy_select_sub = $('#J_copy_select_sub');

	//选中tr高亮
	var input_highlight = $('input.J_line_highlight');
	input_highlight.on('change', function(){
		var tr = $(this).parents('tr');
		if(this.checked) {
			tr.addClass('tr_checkbox');
		}else{
			tr.removeClass('tr_checkbox');
		}
	});

	var copy_pop = $('#J_copy_pop');

	//显示复制窗
	$('#J_copy_rights').on('click', function(e){
		e.preventDefault();

		input_highlight.toggle();
		copy_pop.toggle();

		if(copy_pop.is(':hidden')) {
			input_highlight.prop('checked', false);
			$('tr.tr_checkbox').removeClass('tr_checkbox');
			tips_bubble.hide();
		}else{
			if(!getCookie('tipsBubble')) {
				$(document).scrollTop(0);
				tips_bubble.show();
			}
		}
	});

	//关闭复制窗
	$('#J_copy_close').on('click', function(e){
		e.preventDefault();
		//触发
		$('#J_copy_rights').click();
	});

	//添加
	$('#J_copy_add').on('click', function(e){
		e.preventDefault();
		var selected = copy_select_all.children(':selected');
			

		if(selected.length) {
			//选择多个
			selected.each(function(i, o){
				var v = $(this).val();

				//剔除已存在的
				if(!$('#J_option_' + v).length) {
					copy_select_sub.append('<option id="J_option_'+ v +'" value="'+ v +'">'+ $(this).text().replace(/[^A-Za-z0-9\u4e00-\u9fa5]/g, '') +'</option>');
				}
			});
		}
	});

	//移除
	$('#J_copy_del').on('click', function(e){
		e.preventDefault();
		if(copy_select_sub.val()) {
			copy_select_sub.children(':selected').remove();
		}
	});

	rigths_form.on('submit', function(e) {
		e.preventDefault();

		copy_select_sub.children().prop('selected', true);

		rigths_form.ajaxSubmit({
			dataType	: 'json',
			beforeSubmit: function(arr, $form, options) {
				var text = rights_form_btn.text();
				//按钮文案、状态修改
				rights_form_btn.text(text +'中...').prop('disabled',true).addClass('disabled');
			},
			success		: function(data, statusText, xhr, $form) {
				var text = rights_form_btn.text();
						
				//按钮文案、状态修改
				rights_form_btn.removeClass('disabled').text(text.replace('中...', '')).parent().find('span').remove();
						
				if( data.state === 'success' ) {
					$( '<span class="tips_success">' + data.message + '</span>' ).appendTo(rights_form_btn.parent()).fadeIn('slow').delay( 1000 ).fadeOut(function() {
						//加入当前tab参数
						window.location.href = decodeURIComponent(data.referer)+'&tab='+tabs_nav.children('.current').data('tab');
					});
				}else if( data.state === 'fail' ) {
					$( '<span class="tips_error">' + data.message + '</span>' ).appendTo(rights_form_btn.parent()).fadeIn( 'fast' );
					rights_form_btn.removeProp('disabled').removeClass('disabled');
				}
			}
		});
	});

	//我知道了
	$('#J_tips_bubble_close').on('click', function(e){
		e.preventDefault();
		tips_bubble.remove();

		//common.js
		setCookie('tipsBubble','closed','365',document.domain);
	});

})();