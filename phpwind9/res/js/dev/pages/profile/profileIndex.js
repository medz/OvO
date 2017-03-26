/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-设置-资料
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), ajaxForm, validate
 * $Id$
 */
 
Wind.use('ajaxForm', 'validate', function(){
	//聚焦时默认提示
	var sin_max = undefined;
	if($('#J_bbs_sign').length) {
		if(SING_MAX_LENGTH) {
			sin_max = parseInt(SING_MAX_LENGTH);
		}
	}

	var focus_tips = {
		homepage : '请输入有效的URL地址，以http://开头',
		profile : '您最多可以输入250字',
		bbs_sign : ( sin_max ? '您最多可以输入'+ sin_max +'字' : '' )
	};
	
	$("form.J_profile_form").validate({
		errorPlacement: function(error, element) {
			//错误提示容器
			$('#J_profile_tip_'+ element[0].name).html(error);
		},
		errorElement: 'span',
		focusInvalid : false,
		errorClass : 'tips_icon_error',
		validClass		: 'tips_icon_success',
		onkeyup : false,
		rules: {
			homepage: {
				url	: true
			},
			profile : {
				maxlength : 250
			},
			bbs_sign : {
				maxlength : sin_max
			},
			mobile : {
				number : true
			},
			telphone : {
				telphone : true
			},
			zipcode : {
				zipcode : true
			}
		},
		highlight	: false,
		unhighlight	: function(element, errorClass, validClass) {
			var tip_elem = $('#J_profile_tip_'+ element.name);
			tip_elem.html('');
		},
		onfocusin	: function(element){
			var id = element.name;
			$('#J_profile_tip_'+ id).html(focus_tips[id]);
		},
		onfocusout : function(element){
			$('#J_profile_tip_'+ element.name).html('');
		},
		messages: {
			homepage : {
				url : '请输入有效的URL地址'
			},
			profile : {
				maxlength : '最多只能输入250字'
			},
			bbs_sign : {
				maxlength : '最多只能输入'+ sin_max +'字'
			},
			mobile : {
				number : '格式错误，仅支持数字'
			},
			telphone : {
				number : '格式错误，仅支持数字'
			}
		},
		submitHandler:function(form) {
			//提交
			var btn = $(form).find('button:submit');
			$(form).ajaxSubmit({
				dataType : 'json',
				beforeSubmit : function(){
					Wind.Util.ajaxBtnDisable(btn);
				},
				success : function(data){
					Wind.Util.ajaxBtnEnable(btn);
					if(data.state === 'success') {
						Wind.Util.formBtnTips({
							wrap : btn.parent(),
							msg : data.message
						});
					}else if(data.state === 'fail'){
						Wind.Util.formBtnTips({
							error : true,
							wrap : btn.parent(),
							msg : data.message
						});
					}
				}
			});
		}
	});
	
	//邮箱后缀匹配 jquery.emailAutoMatch
	Wind.use('emailAutoMatch', function(){
		$('#J_profile_email').emailAutoMatch();
	});


	//帖子签名ubb帮助
	var ubbdemo_pop = $('#J_ubbdemo_pop');
	$('#J_ubbdemo').on('click', function(e){
		e.preventDefault();
		Wind.Util.popPos(ubbdemo_pop);

		Wind.use('draggable', function(){
			ubbdemo_pop.draggable({ handle : '.J_pop_handle'});
		});
	});
 
	$('#J_ubbdemo_close').on('click', function(e){
		e.preventDefault();
		ubbdemo_pop.hide();
	});

});