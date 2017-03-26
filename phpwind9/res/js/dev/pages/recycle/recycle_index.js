/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-前台管理
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), 由页面定义
 * $Id$
 */
 
;(function(){
	//复选框重置
	$('input.J_check_all, input.J_check').removeAttr('checked');

	//提交&是否选择项
	$('button.J_form_sub_check').on('click', function(e){
		e.preventDefault();
		var $this = $(this),
			form = $this.parents('form.J_form_ajax'),
			checked = form.find('input.J_check:checked');		//选中的项

		if(!checked.length) {
			//选择为空
			Wind.Util.resultTip({
				error : true,
				follow : $this,
				msg : '请选择要操作的项'
			});
		}else{
			//提交
			var action = $this.data('action');
			form.ajaxSubmit({
				url : action ? action : form.attr('action'),			//按钮是否自定义提交地址
				dataType : 'json',
				success : function(data){
					if(data.state == 'success') {
						Wind.Util.resultTip({
							msg : '操作成功',
							follow : $this,
							callback : function(){
								window.location.reload();
							}
						});
					}else if(data.state == 'fail') {
						Wind.Util.resultTip({
							error : true,
							follow : $this,
							msg : data.message
						});
					}
				}
			});
		
		}
	});
	
	//举报筛选
	$('#J_report_select').on('change', function(){
		$(this).parent().submit();
	});
	
})();