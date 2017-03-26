/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-设置-我的积分
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), dialog, jquery.form, jquery.draggable, 由profile_layout.htm定义
 * $Id$
 */
;(function(){
	try {
    //var   EXCHANGE_DATA = $.parseJSON(EXCHANGE);			//积分转换数据
	//var   TRANSFER_DATA = $.parseJSON(TRANSFER);			//积分转账数据
	//var	CREDITNAME_DATA = $.parseJSON(CREDITNAME);	//积分名
	//var   CREDITUNIT_DATA = $.parseJSON(CREDITUNIT);	//积分单位

    var EXCHANGE_DATA = EXCHANGE,			//积分转换数据
    	TRANSFER_DATA = TRANSFER,			//积分转账数据
        CREDITNAME_DATA = CREDITNAME,	//积分名
        CREDITUNIT_DATA = CREDITUNIT;	//积分单位
	}catch(e){
		$.error(e);
	}

	var credit_transfer_wrap = $('#J_credit_transfer_wrap'),		//转账弹窗
			credit_change_wrap = $('#J_credit_change_wrap'),				//转换弹窗
			credit_form = $('form.J_credit_form');									//表单

	//拖拽组件
	credit_change_wrap.draggable( { handle : '.J_pop_handle'} );
	credit_transfer_wrap.draggable( { handle : '.J_pop_handle'} );

	//转账
	var transfer_name = $('#J_transfer_name'),									//积分名
			transfer_input = credit_transfer_wrap.find('input.J_input_number'),
			transfer_min_tip = $('#J_transfer_min_tip'),
			transfer_count = $('#J_transfer_count');


	//点击转账
	$('a.J_credit_transfer').on('click', function(e){
		e.preventDefault();
		var id = $(this).data('id');
				min = TRANSFER_DATA[id]['min'];
				rate = TRANSFER_DATA[id]['rate'];

		//表单重置
		credit_form.resetForm();
		
		transfer_name.text(CREDITNAME_DATA[id]);
		$('#J_transfer_id').val(id);

		//手续费
		$('#J_transfer_rate').text(rate +'%');

		Wind.Util.popPos(credit_transfer_wrap);
		credit_change_wrap.hide();
		credit_transfer_wrap.find('input:text:visible').first().focus();

		//统计
		transferCount(transfer_input.val(), min, rate);

		//输入金额
		transfer_input.on('keyup', function(){
			var selected = exchange_select.children(':selected');
			transferCount($(this).val(), min, rate);
		});

	});

	//转账统计
	function transferCount(v, min, rate){
		if(v === '') {
			v = 0;
		}else{
			v = parseInt(v);
		}

		if(v < min){
			//小于最低金额
			transfer_min_tip.show();
			$('#J_transfer_min').text(min);
			transfer_count.text('');
		}else{
			transfer_min_tip.hide();
			
			var rate_v = Math.floor(v * (rate/100));
			transfer_count.text(v+ ' + '+ rate_v +'(手续费)='+ (v + rate_v) + transfer_name.text());
		}
		
	}

	

	//转换
	var exchange_select = $('#J_exchange_select'),
			exchange_input = credit_change_wrap.find('input.J_input_number');

	//点击转换
	$('a.J_credit_change').on('click', function(e){
		e.preventDefault();

		var id = $(this).data('id');

		$('#J_orgin_credit').text(CREDITNAME_DATA[id]);
		$('#J_exchange_id').val(id);

		//转换为下拉
		var arr = [];
		$.each(EXCHANGE_DATA[id], function(i, o){
			arr.push('<option value="'+ o.credit2 +'" data-rate1="'+ o.value1 +'" data-rate2="'+ o.value2 +'">'+ CREDITNAME_DATA[o.credit2] +'</option>');
		});
		exchange_select.html(arr.join(''));

		//定位 global.js
		Wind.Util.popPos(credit_change_wrap);

		//转换比例
		var selected = exchange_select.children(':selected');
		exchangeRate(id, selected.text(), selected.data('rate1'), selected.data('rate2'));
		credit_transfer_wrap.hide();

		//转换比例 切换转换
		exchange_select.off('change').on('change', function(){
			var selected = exchange_select.children(':selected');
			exchangeRate(id, selected.text(), selected.data('rate1'), selected.data('rate2'));
		});

		//输入数量
		exchange_input.on('keyup', function(){
			var selected = exchange_select.children(':selected');
			exchangeCount($(this).val(), selected.data('rate1'), selected.data('rate2'));
		});
	});

	//转换比例计算
	function exchangeRate(id_orign, text, rate1, rate2){
		$('#J_exchange_rate').text(rate1 + CREDITUNIT_DATA[id_orign] + CREDITNAME_DATA[id_orign] +' = '+ rate2 + CREDITUNIT_DATA[exchange_select.val()] + text);
		
		$('#J_exchange_to').text(text);

		exchangeCount(credit_change_wrap.find('input.J_input_number').val(), rate1, rate2);
	}

	//转换总计
	var exchange_count = $('#J_exchange_count'),
			parity_wrap = $('#J_exchange_parity'),
			exchange_num = $('#J_exchange_num');
	function exchangeCount(v, rate1, rate2, parity){
		exchange_input.focus();
		if(rate1 > 1 && v%rate1 !== 0) {
			exchange_count.text('');
			parity_wrap.show();
			exchange_num.text(rate1);
		}else{
			exchange_count.text((v/rate1) * rate2);
			parity_wrap.hide();
		}
	}

	//关闭
	$('a.J_close').on('click', function(e){
		e.preventDefault();
		$('#J_credit_transfer_wrap, #J_credit_change_wrap').hide();
	});

	//提交
	var btn = credit_form.find('button:submit');
	credit_form.ajaxForm({
		dataType : 'json',
		beforeSubmit : function(){
			Wind.Util.ajaxBtnDisable(btn);
		},
		success : function(data, statusText, xhr, $form){
			Wind.Util.ajaxBtnEnable(btn);
			if(data.state == 'success') {
				Wind.Util.formBtnTips({
					wrap : btn.parent(),
					msg : '操作成功',
					callback : function(){
						window.location.reload();
					}
				});
			}else if(data.state == 'fail') {
				Wind.Util.formBtnTips({
					error : true,
					wrap : btn.parent(),
					msg : data.message
				});
			}
		}
	});
	
})();
