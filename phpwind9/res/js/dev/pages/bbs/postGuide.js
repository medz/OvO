/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-发帖引导
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later)
 * $Id$
 */

$(function(){
	var post_trigger = $('#J_post_trigger'),
			head_forum_post = $('#J_head_forum_post'),
			head_forum_pop = $('#J_head_forum_pop'),
			org_style = head_forum_pop.attr('style');			//原始style

	post_trigger.on('click', function(e){
		e.preventDefault();
		var $this = $(this);
		
		if(!GV.U_ID) {
			//未登录
			location.href = GV.URL.LOGIN;
		}
		
		if(head_forum_pop.find('.pop_loading').length) {
			head_forum_post.click();
			forumPos(head_forum_pop);
		}else{
			//已有内容
			head_forum_pop.show();
			forumPos(head_forum_pop);
		}

	});

	head_forum_post.on('click', function(){
		//恢复定位
		head_forum_pop.attr('style', org_style);
		head_forum_pop.css({
			top : head_forum_post.offset().top + head_forum_post.height() - $(document).scrollTop()
		}).show();

	});

	$('#J_head_forum_close').on('click', function(){
		head_forum_pop.attr('style', org_style);
	});

	function forumPos(pop){
		pop.css({
			position : 'absolute',
			left : post_trigger.offset().left - pop.width() - post_trigger.width(),
			top : post_trigger.offset().top + post_trigger.height()
		});
		
	}

});