/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台用户名输入标签js（发私信、发帖提到某人）
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later)
 * $Id$
 */
;
(function(){
	window.userTag = function () {
		var user_tag_wrap = $('.J_user_tag_wrap');
		
		$.each(user_tag_wrap, function (i, o) {
			var $this = $(this),
				user_tag_ul = $this.find('ul.J_user_tag_ul'),
				user_tag_input = $this.find('input.J_user_tag_input'),
				timer;
			
			user_tag_input.val('');
			
			//点击区域输入聚焦
			user_tag_wrap.on('click', function (e) {
				if (e.target == $this[0]) {
					user_tag_input.focus();
				}
			});
			
			user_tag_input.on('keydown', function (e) {
				//键盘输入
				var $this = $(this);

				if (e.keyCode === 32 || e.keyCode === 13) {
					//输入空格或回车
					if(e.keyCode === 13) {
						e.preventDefault();		//mac ff下输中文按回车会终止
					}
					var v = $.trim($this.val());
					
					//是否有当前项
					var current = $('#J_user_match_wrap li.current');
					if(current.length) {
						v = current.text();
					}

					tagCreat(v, user_tag_ul, user_tag_input);
					
				}else if(e.keyCode === 8){
					//backspace
					if(!$.trim($this.val())) {
						user_tag_ul.children(':last').remove();
					}
				}
				
			}).on('blur', function (e) {
				//失焦
				var v = $.trim($(this).val());
				
				if (!v) {
					return false; //空内容
				}
				
				timer = setTimeout(function(){
					tagCreat(v, user_tag_ul, user_tag_input);
					$('#J_user_match_wrap').hide().empty();
				}, 100);
				
			});
			
		});
		
		//删除
		$('ul.J_user_tag_ul').on('click', 'del.J_user_tag_del', function (e) {
			e.preventDefault();
			$(this).parents('li').remove();
		});
		
	};

	userTag();

	//验证&创建用户tag
	function tagCreat(v, ul, input) {
		if(!v) {
			return false;
		}
		//验证用户名特殊字符
		var reg = /[&\\'\"\/*,<>#%?　]/g;
		
		if (reg.test(v)) {
			Wind.Util.resultTip({
				error : true,
				msg : '不能含有非法字符',
				follow : input
			});
			return;
		}
		
		//获取已生成的用户名
		var v_arr = [];
		$.each(ul.children('li'), function (i, o) {
			v_arr.push($(this).find('.J_tag_name').text());
		});
		
		//重复验证
		/*var repeat = false;
		$.each(v_arr, function (i, o) {
			if (o === v) {
				repeat = true;
			}
		});
		if (repeat) {
			return false;
		}*/
		
		//生成tag
		ul.append('<li><a><span class="J_tag_name">' + v + '</span><del title="' + v + '" class="J_user_tag_del">×</del><input type="hidden" value="' + v + '" name="'+ input.data('name') +'" /></a></li>');
		
		setTimeout(function(){
			input.val('');
		}, 0);
	}

})();

