/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-设置-工作经历
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), ajaxForm
 * $Id$
 */
 
Wind.use('ajaxForm', function(){
	//工作经历
	var work_op_wrap = $('#J_work_op_wrap'),				//添加编辑栏
		work_form = $('#J_work_form'),							//表单
		work_company = $('#J_work_company'),				//单位
		edit_id = $('#J_edit_id'),										//编辑id
		work_add = $('#J_work_add'),
		work_none = $('#J_work_none');
	
	
	//添加工作
	work_add.on('click', function(e){
		e.preventDefault();
		work_op_wrap.insertAfter($('#J_work_list > li:eq(0)')).show().siblings(':hidden').show();
		work_form.resetForm();
		work_form.attr('action', URL_WORK_ADD);			//修改提交地址
		work_company.focus();
		edit_id.val('');
		work_add.hide();
		work_none.hide();
	});
	
	//添加工作_空
	$('#J_work_none > a').on('click', function(e){
		e.preventDefault();
		work_none.hide();
		work_add.trigger('click');
	});
	
	//工作编辑
	$('a.J_work_edit').on('click', function(e){
		e.preventDefault();
		var $this = $(this),
			parent = $this.parents('li');
		
		parent.hide();
		parent.siblings(':hidden').show();
		work_add.show();
		work_op_wrap.insertAfter(parent).show();
		work_form.attr('action', URL_WORK_EDIT);		//修改提交地址
		work_company.val($this.data('company'));		//写入公司名
		edit_id.val($this.data('id'));								//编辑id
		
		//写入年月
		$('#J_starty').val($this.data('starty'));
		$('#J_startm').val($this.data('startm'));
		$('#J_endy').val($this.data('endy'));
		$('#J_endm').val($this.data('endm'));
		
		work_company.focus();
	});
	
	//工作提交
	work_form.ajaxForm({
		dataType : 'json',
		success : function(data){
			if(data.state === 'success') {
				work_op_wrap.hide();
				window.location.reload();
			}else if(data.state === 'fail'){
				Wind.Util.resultTip({
					error : true,
					msg : data.message
				});
			}
		}
	});
	
	//删除工作 global.js
	$('a.J_work_del').on('click', function(e){
		e.preventDefault();
		Wind.Util.ajaxConfirm({
			href : $(this).attr('href'),
			elem : $(this)
		});
	});
	
	//取消
	$('#J_work_cancl').on('click', function(e){
		e.preventDefault();
		work_op_wrap.siblings(':hidden').show();
		work_op_wrap.hide();
		work_add.show();
		work_none.show();
	});
});