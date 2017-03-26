/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-关注粉丝
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), global.js, URL_UNFOLLOW, URL_FOLLOW
 * $Id$
 */

;(function(){
	var friends_items = $('.J_friends_items');
/*
 * 显示隐藏取消关注
*/
	var unfollow_btn = $('a.J_unfollow_btn');
	friends_items.hover(function(){
		$(this).find('a.J_unfollow_btn').fadeIn('fast');
	}, function(){
		$(this).find('a.J_unfollow_btn').fadeOut('fast');
	});


/*
 * 关注&取消关注：我的粉丝 找人 脚印
*/
	var lock = false;
	friends_items.on('click', 'a.J_fans_follow', function(e){
		e.preventDefault();
		var $this = $(this),
				role = $this.data('role'),
				uid = $this.data('uid'),
				followed = $this.data('followed'),
				followed_attr = followed ? 'data-followed="true"' : '',		//已关注标识
				url = (role == 'follow' ? URL_FOLLOW : URL_UNFOLLOW);			//提交地址

		//global.js
		Wind.Util.ajaxMaskShow();

		//锁定
		if(lock) {
			return false;
		}
		lock = true;

		$.post(url, {uid : uid} ,function(data){
			//global.js
			Wind.Util.ajaxMaskRemove();
			lock = false;

			if(data.state == 'success') {
				var parent = $this.parent();
				if(role == 'follow') {
					//关注
					if(followed) {
						//对方已关注
						parent.html('<span title="互相关注" class="mnfollow">互相关注</span><a class="core_unfollow J_unfollow_btn J_fans_follow" '+ followed_attr +' data-role="unfollow" data-uid="'+ uid +'" href="#">取消关注</a>');
					}else{
						parent.html('<a class="core_unfollow J_fans_follow J_unfollow_btn" data-role="unfollow" data-uid="'+ uid +'" href="#">取消关注</a>');
					}
				}else{
					//取消关注
					parent.html('<a class="core_follow J_fans_follow" data-role="follow" '+ followed_attr +' data-uid="'+ uid +'" href="#">加关注</a>');
				}

				$('#J_user_card_'+ uid).remove();
				
			}else if(data.state == 'fail'){
				//global.js
				Wind.Util.resultTip({
					error : true,
					msg : data.message,
					follow : $this
				});
			}
		}, 'json');
	});

})();