/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-注册手机验证
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), M_CHECK, M_CHECK_MOBILE 页面定义
 * $Id$
 */
;
(function () {
    //手机验证
    var reg_mobile = $('#J_reg_mobile'), //手机号码input
		pwd_username = $('#J_pwd_username'), //用户名
        show_mcode = $('#J_show_mcode'), //获取手机验证码按钮
        mcode_resend = $('#J_mcode_resend'), //重新发送按钮
        send_mobile = $('#J_send_mobile'), //填写的手机号码
        mcode_tip = $('#J_mcode_tip'), //手机验证码输入提示
        reg_tip__mobile = $('#J_reg_tip__mobile'), //手机号错误提示
        reg_mobileCode = $('#J_reg_mobileCode'), //手机验证码input
        counttime = parseInt(reg_mobile.data('counttime')), //倒计时间 秒
        count_timer;

    var mCheckUtil = {
        check : function(elem, callback){
        	//验证手机返回验证码
        	Wind.Util.ajaxBtnDisable(elem);
            $.post(M_CHECK, {
				mobile : reg_mobile.val(),
				username : pwd_username.val()
			}, function(data){
            	Wind.Util.ajaxBtnEnable(elem);
                if(data.state == 'success') {
                    if(callback) {
                        callback();
                    }
                }else if(data.state == 'fail'){
                    Wind.Util.resultTip({
                        error : true,
                        follow : elem,
                        msg : data.message
                    });
                    reg_mobile.prop('disabled', false).removeClass('disabled');
                }
            }, 'json');
        },
        countDown : function(){
        	//倒计时
        	var _this = this,
        		c = counttime;

        	mcode_resend.text(c+'秒后重新发送').prop('disabled', true).addClass('disabled');

        	count_timer = setInterval(function(){
        		c--;
        		mcode_resend.text(c+'秒后重新发送');
        		if(c <= 0) {
        			clearInterval(count_timer);
        			mcode_resend.text('重新发送').prop('disabled', false).removeClass('disabled');
        			return;
        		}
        	}, 1000);
        }
    };

    reg_mobile.val('');
    reg_mobile.prop('disabled', false);

    var m_timer,
    	regexp = /^1[34578]\d{9}$/,
        checkin = false,
        _v;

    reg_mobile.on('focus', function(){
    	//手机输入聚焦
        var $this = $(this);
        reg_tip__mobile.hide();
        //计时器开始
        m_timer = setInterval(function(){
            var trim_v = $.trim($this.val());

            if(trim_v.length == 11 && regexp.test(trim_v)) {
                //手机格式验证通过

                if(checkin || trim_v == _v) {
                    //后端已验证或查询值重复
                    return;
                }
                checkin = true

                $.post(M_CHECK_MOBILE,{
                    mobile : trim_v,
					username : pwd_username.val()
                }, function(data){
                    _v = trim_v;
                    checkin = false;
                    if(data.state == 'success') {
                        show_mcode.show();
                        $('#J_reg_mobile_hide').val(trim_v);
                        reg_tip__mobile.hide().empty();
                    }else if(data.state == 'fail') {
                        reg_tip__mobile.html('<span class="tips_icon_error">'+ data.message +'</span>').show();
                    }
                }, 'json');
                /**/
            }else{
                show_mcode.hide();
                reg_tip__mobile.hide();
            }
        }, 200);

    }).on('blur', function(){
        //输入失焦，解除计时
        clearInterval(m_timer);

        var trim_v = $.trim($(this).val());
        reg_tip__mobile.show();

        if(!trim_v) {
        	reg_tip__mobile.html('<span class="tips_icon_error">手机号码不能为空</span>');
        	return;
        }

        if(trim_v.length !== 11 || !regexp.test(trim_v)) {
            //手机号错误提示
            reg_tip__mobile.html('<span class="tips_icon_error">请正确填写您的手机号码</span>');
            return;
        }
        
    });

    //获取验证码
    show_mcode.on('click', function(e){
        e.preventDefault();
        reg_mobile.prop('disabled', true).addClass('disabled');

        mCheckUtil.check(show_mcode, function(){
            show_mcode.hide();
            mcode_tip.show();
            $('#J_reg_tip_mobile').empty();
            reg_mobileCode.focus();
            send_mobile.text(reg_mobile.val());
            mCheckUtil.countDown();
        });
    });

    //修改号码
    $('#J_mobile_change').on('click', function(e){
    	e.preventDefault();
    	reg_mobile.prop('disabled', false).removeClass('disabled').val('').focus();
        mcode_tip.hide();
        clearInterval(count_timer);

		//重置对比值
		_v = undefined;
    });

    //重新发送
    mcode_resend.on('click', function(e){
    	e.preventDefault();

    	if(!mcode_resend.hasClass('disabled')) {
    		mCheckUtil.check(mcode_resend, function(){
    			reg_mobileCode.focus();
	            mCheckUtil.countDown();
	        });
    	}
    });

})();