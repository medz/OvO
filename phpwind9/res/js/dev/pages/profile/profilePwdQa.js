/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-设置-修改密码
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), global.js
 * $Id$
 */
 
;(function(){
	var new_pwd = $('#J_newPwd'),
			old_pwd = $("#J_old_pwd"),
			tip_new_pwd = $('#J_tip_newPwd'),
			pw_edit = $("#J_pw_edit"),
			btn = pw_edit.find('button:submit');
		
	//聚焦时默认提示
	var focus_tips = {
		passwd: '请输入原密码',
		oldPwd : '请输入原密码',
		newPwd : new_pwd.data('tips'),
		rePwd : '请再输入一遍您上面填写的密码',
		email : '',
		myquestion : '',
		answer : '请输入答案'
	};
	
	//密码强度
	var passwordRank = {
		1 : '<span class="pwd_strength_1"></span>弱',
		2 : '<span class="pwd_strength_2"></span>弱',
		3 : '<span class="pwd_strength_3"></span>中',
		4 : '<span class="pwd_strength_4"></span>强'
	};
	
	pw_edit.resetForm();
	pw_edit.validate({
		//debug : true,
		errorPlacement: function(error, element) {
			//错误提示容器
			$('#J_tip_'+ element[0].name).html(error);
		},
		errorElement: 'span',
		errorClass : 'tips_icon_error',
		validClass		: 'tips_icon_success',
		onkeyup : false,
		focusInvalid : false,
		rules: {
			passwd: {
				required	: true/*,
				remote : {
					url : old_pwd.data('checkurl'),
					type : 'post',
					dataType: "json",
					data : {
						pwd :  function(){
							return old_pwd.val();
						}
					},
					complete: function(jqXHR){
						if(jqXHR.status == '200') {
							var data = $.parseJSON(jqXHR.responseText);
							if(data.state == 'fail' && data.referer) {
								//尝试过多
								setTimeout(function(){
									location.href = data.referer;
								}, 1000);
							}
						}
					}
				}*/
			},
			oldPwd: {
				required	: true,
				remote : {
					url : old_pwd.data('checkurl'),
					type : 'post',
					dataType: "json",
					data : {
						pwd :  function(){
							return old_pwd.val();
						}
					},
					complete: function(jqXHR){
						if(jqXHR.status == '200') {
							var data = $.parseJSON(jqXHR.responseText);
							if(data.state == 'fail' && data.referer) {
								//尝试过多
								setTimeout(function(){
									location.href = decodeURIComponent(data.referer);
								}, 1000);
							}
						}
					}
				}
			},
			newPwd : {
				required : true,
				remote : {
					url : new_pwd.data('pwdcheck'),		//验证密码
					dataType: "json",
					type : 'post',
					data : {
						pwd : function(){
							return new_pwd.val();
						}
					}
				}
			},
			rePwd : {
				required : true,
				equalTo : '#J_newPwd'
			},
			email : {
				required : true,
				email : true
			},
			myquestion : {
				required : true
			},
			answer : {
				required : true
			}
		},
		highlight	: false,
		unhighlight	: function(element, errorClass, validClass) {
			var tip_elem = $('#J_tip_'+ element.name);

			if(element.value){
				tip_elem.html('<span class="'+ validClass +'" data-text="text"><span>');
			}
		},
		onfocusin	: function(element){
			var name = element.name;
			$('#J_tip_'+ name).html('<span class="reg_tips" data-text="text">'+ focus_tips[name] +'</span>');
			
			if(name == 'newPwd') {
				//密码则添加强度验证
				
				$(element).off('keyup').on('keyup', function(e){
					
					//过滤tab键
					if(e.keyCode !== 9) {

						$.post($(this).data('pwdstrong'), {
							pwd : new_pwd.val()
						}, function(data){

							if(data.state === 'success') {
								tip_new_pwd.html(passwordRank[data.message['rank']]);
							}else if(data.state === 'fail'){
								tip_new_pwd.html('');
							}
						}, 'json');

					}
					
				});
			}
		},
		onfocusout	:  function(element){
			var _this = this;
			
			if(element.name == 'email') {
				//邮箱匹配点击后，延时处理
				/*setTimeout(function(){
					_this.element(element);
				}, 150);*/
			}else{
			
				if(element.name === 'password'){
					//防止重复绑定
					$(element).off('keyup');
					
					//失焦标识
					reg_tip_password.data('blur', 'blur');
				}
	
			}
			
		},
		messages: {
			passwd: {
				required	: '登录密码不能为空'
			},
			oldPwd : {
				required	: '登录密码不能为空',
				remote : '原密码错误' //ajax验证默认提示
			},
			newPwd : {
				required : '新密码不能为空',
				remote : '密码不合要求' //ajax验证默认提示
			},
			rePwd : {
				required : '确认密码不能为空',
				equalTo : '两次输入的密码不一致。请重新输入'
			},
			email : {
				required : '邮箱不能为空',
				email : '请输入正确的电子邮箱地址',
				remote : '该电子邮箱已被注册，请更换别邮箱' //ajax验证默认提示
			},
			myquestion : {
				required	: '安全问题不能为空'
			},
			answer : {
				required	: '答案不能为空'
			}
		},
		submitHandler:function(form) {
			$(form).ajaxSubmit({
				dataType : 'json',
				beforeSubmit: function(){
					Wind.Util.ajaxBtnDisable(btn);
				},
				success : function(data){
					Wind.Util.ajaxBtnEnable(btn);
					if(data.state === 'success') {
						Wind.Util.formBtnTips({
							msg : data.message,
							wrap: btn.parent(),
							callback : function(){
								window.location.href = decodeURIComponent(data.referer);
							}
						});
					}else if(data.state === 'fail'){
						Wind.Util.formBtnTips({
							error : true,
							wrap: btn.parent(),
							msg : data.message
						});
					}
				}
			});
		}
	});
	
	//安全问题切换
	var question_dl = $('#J_question_dl'),
		question_custom = $('#J_question_custom'),		//自定义问题
		answer_dl = $('#J_answer_dl'),
		answer = $('#J_answer');
		
	$('#J_question_list').on('change', function(){
		var v = $(this).val();
		
		question_dl.hide();
		if(v == '-1' || v == '-2' || v == '-3') {
			//不修改 取消
			answer_dl.hide();
			question_dl.hide();
			question_custom.val('1');
			answer.val('1');
		}else{
			answer_dl.show();
			answer.val('').focus();
		}
		
		if(v == '-4') {
			//自定义问题
			question_dl.show();
			answer.val('');
			question_custom.val('').focus();
		}
	});
	
})();