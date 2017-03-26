/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-设置-积分充值
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), global.js, RECHARGE页面定义
 * $Id$
 */
 
;(function(){
	var recharge_select = $('#J_recharge_select'),
			recharge_amount = $('#J_recharge_amount');		//金额

	try {
		var data = $.parseJSON(RECHARGE);
	}catch(e){
		$.error(e);
	}

	//页面载入
	rechargeChange(recharge_select.val(), recharge_select.children(':selected').text());

	//切换充值项
	recharge_select.on('change', function(){
		rechargeChange($(this).val(), $(this).children(':selected').text());
	});

	//输入金额
	recharge_amount.on('keyup', function(){
		if(/^\d+(\.\d{0,2})?$/.test($(this).val())) {
			rechargeCount($(this).val(), data[recharge_select.val()]['rate'], recharge_select.children(':selected').text())
		}else{
			$('#J_recharge_count').html('请输入数字，小数最多两位');
		}
		
	});

	//支付方式
	$('#J_payment_list a').on('click', function(e){
		e.preventDefault();
		$(this).parent().addClass('current').siblings().removeClass('current');

		//隐藏表单
		$('#J_payment_type').val($(this).data('val'));
	});

	//提交
	$('#J_recharge_form').ajaxForm({
		dataType : 'json',
		success : function(data){
			if(data.state == 'success') {
                //替换连接中&amp
                data.data.url = (data.data.url).replace(/&amp;/g,'&');
				//支付跳转
				window.location.href = decodeURIComponent(data.data.url);//todo
				//window.location.href = data.referer;
			}else if(data.state == 'fail'){
				//global.js
				Wind.Util.resultTip({
					error : true,
					msg : data.message
				});
			}
		}
	});

	//切换显示
	function rechargeChange(v, text){
		//充值比例
		$('#J_recharge_rate').text(data[v]['rate'] + text);

		//最少充值
		$('#J_recharge_min').text(data[v]['min']);

		//可获得
		rechargeCount(recharge_amount.val(), data[v]['rate'], text);
	}

	//可获得统计
	//可获得统计
	function rechargeCount(v, rate, text){
		if(v) {
			$('#J_recharge_count').html('<span class="red">'+ (v * rate) +'</span>'+ text);
		}else{
			$('#J_recharge_count').html('');
		}
		
	}
	
})();
