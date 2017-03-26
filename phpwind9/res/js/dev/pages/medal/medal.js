/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-勋章
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), MEDAL_JSON由页面定义
 * $Id: medal.js 21606 2012-12-11 11:33:10Z hao.lin $
 */
 
$(function(){
	//弹窗模板
	var template = '<div role="alertdialog" aria-labelledby="alert_title" class="pop_deep J_medel_pop" id="J_medel_pop__ID" tabindex="0" style="display:none;">\
	<div style="min-width:200px" class="core_pop">\
		<div class="hd J_drag_handle" style="cursor: move;">\
			<a class="close J_close" href="#">关闭</a>\
			<strong>勋章说明</strong>\
		</div>\
		<div class="ct J_content"><div class="pop_loading"></div></div>\
	</div>\
</div>';
		
	//查看&领取勋章
	$('#J_medal_card_wrap').on('click', 'a.J_medal_card', function(e){
		e.preventDefault();
		var $this = $(this),
			role = $this.data('role'),
			id = $this.data('id'),
			//data = medel_data[id],
			pop_item = $('#J_medel_pop_'+id);
			//logid = (data.logid !== '' ? data.logid : '');		//勋章中心 领取提交参数
		$('.J_medel_pop').hide();
		
		if(pop_item.length) {
			//内容已存在
			//global.js
			Wind.Util.popPos(pop_item);
		}else{
			//请求内容
			$('body').append(template.replace('_ID', id));
			var pop_item = $('#J_medel_pop_'+id);

			//global.js
			Wind.Util.popPos(pop_item);
			Wind.use('draggable', function(){
				pop_item.draggable({handle : '.J_drag_handle'});
			});

			$.post(this.href, function(data){
				if(Wind.Util.ajaxTempError(data)) {
					pop_item.remove();
					return false;
				}

				pop_item.find('.J_content').html(data);
				Wind.Util.popPos(pop_item);
				var ta = pop_item.find('textarea:visible');
				if(ta.length) {
					Wind.Util.buttonStatus(ta, pop_item.find('button:submit'));
				}

				//绑定关闭
				pop_item.find('.J_close').on('click', function(e){
					e.preventDefault();
					$(this).parents('.J_medel_pop').hide();
				});

				//绑定提交
				var medal_pop_form = $('form.J_medal_pop_form');
				medal_pop_form.on('submit', function(e){
					e.preventDefault();
					var btn = $(this).find('button:submit');
					
					$(this).ajaxSubmit({
						url : btn.data('action'),
						/*data : {
							id : $this.data('subid'),														//申请
						logid : (logid ? logid : $this.data('logid')),			//领取
						csrf_token : GV.TOKEN
					},*/
						dataType	: 'json',
						beforeSubmit: function(arr, $form, options) {
							Wind.Util.ajaxBtnDisable(btn);
						},
						success		: function(data, statusText, xhr, $form) {
							if(data.state === 'success') {
								Wind.Util.resultTip({
									msg : data.message,
									callback : function(){
										window.location.reload();
									}
								});
							}else if(data.state === 'fail'){
								Wind.Util.ajaxBtnEnable(btn);
								Wind.Util.resultTip({
									error : true,
									msg : data.message
								});
							}
						}
					});
			
				});

			});
			
		}

	});
	
});