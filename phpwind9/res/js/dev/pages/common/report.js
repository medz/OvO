/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-举报
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later), dialog, jquery.form
 * $Id$
 */

;(function(){
	
	//点击举报
	$('#J_posts_list').on('click', 'a.J_report', function(e){
		e.preventDefault();
		var $this = $(this);

		var report_pop = $('#J_report_pop');
		if(report_pop.length) {
			report_pop.find('textarea').focus();
			return false;
		}

		//global.js
		Wind.Util.ajaxMaskShow();

		$.post(this.href, {type_id: $this.data('typeid')}, function(data){
			//global.js
			Wind.Util.ajaxMaskRemove();

			//验证模板反馈 gloabl.js
			if(Wind.Util.ajaxTempError(data)) {
				return false;
			}

			Wind.dialog.closeAll();
			Wind.dialog.html(data, {
				id: 'J_report_pop',
				position: 'fixed',			//固定定位
				title: '举报',
				isMask: false,			//无遮罩
				isDrag: true,
				callback: function(){
					var report_form = $('#J_report_form'),
						textarea = report_form.find('textarea');
					//按钮状态 global.js
					Wind.Util.buttonStatus(textarea, report_form.find('button:submit'));
					textarea.focus();
					
					$('#J_report_typeId').val($this.data('pid'));
					
					//类型
					$('#J_pick_list > a').on('click', function(e){
						e.preventDefault();
						$(this).addClass('current').siblings('.current').removeClass('current');
					});
					
					//举报提交
					var btn = report_form.find('button:submit');

					Wind.use('ajaxForm', function(){
						report_form.ajaxForm({
							dataType: 'json',
							beforeSubmit: function(){
								Wind.Util.ajaxBtnDisable(btn);
							},
							success: function(data){
								Wind.Util.ajaxBtnEnable(btn);

								Wind.Util.formBtnTips({
									error : (data.state=='success' ? false : true),
									wrap : btn.parent(),
									msg : data.message,
									callback : function(){
										if(data.state=='success')
										Wind.dialog.closeAll();
									}
								});
							}
						});
					});
					
				}
			});
				
		});
	});
})();