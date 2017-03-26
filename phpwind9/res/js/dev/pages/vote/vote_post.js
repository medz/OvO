/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-投票
 * @Author	: luomingqu@gmail.com
 * @Depend	: jquery.js(1.7 or later), 由页面定义
 * $Id: vote_index.js 14800 2012-08-01 11:48:56Z hejin $
 */
 
;(function(){
	var forum_data = {},											//版块数据
			vote_forum_ct = $('#J_vote_forum_ct'),					//版块弹窗列表区
			vote_post_to_cate = $('#J_vote_post_to_cate'),				//发帖到_分类
			vote_post_to_forum = $('#J_vote_post_to_forum'),		//发帖到_版块
			vote_forum_submit = $('#J_vote_forum_submit'),			//确定
			forum_ul,
			fid = '';

	if(!forum_data.data) {
		//请求投票版块数据
		$.getJSON(URL_FORUM_LIST, function(data){
			if(data.state == 'success') {
				forum_data.data = $.parseJSON(data.data);

				//循环写入分类数据
				var cate_data = forum_data.data['cate'],		//分类数据
						cate_arr = [];
				for(i in cate_data) {
					cate_arr.push('<li tabindex="0" role="option" class="J_cate_item" data-cid="'+ i +'">'+ cate_data[i] +'</li>');
				}
				vote_forum_ct[0].innerHTML = '<div class="source_forum" tabindex="0" role="combobox" aria-owns="J_vote_forum_list" aria-label="选择分类"><h4>选择分类</h4><ul id="J_vote_forum_list">'+ cate_arr.join('') +'</ul></div><div class="target_forum" tabindex="0" role="combobox" aria-owns="J_vote_forum_ul" aria-label="选择版块"><h4>选择版块</h4><ul id="J_vote_forum_ul"></ul></div>'
				forum_ul = document.getElementById('J_vote_forum_ul');
			}
		});
	}


	//点击分类
	vote_forum_ct.on('click keydown', 'li.J_cate_item', function(e) {
		if(e.type === 'keydown' && e.keyCode !== 13) {
			return;
		}
		var current_cid = $(this).data('cid');

		$(this).addClass('current').siblings().removeClass('current');
		vote_post_to_cate.text($(this).text());																		//发帖到_分类
		vote_post_to_forum.text('');																				//发帖到_版块
		vote_forum_submit.addClass('disabled').prop('disabled', 'disabled');										//确定按钮不可用

		//循环写入版块数据
		
		var data_forum = forum_data.data['forum'][current_cid],
				forum_arr = [];
		for(i in data_forum) {
			forum_arr.push('<li tabindex="0" role="option" class="J_forum_item" data-fid="'+ i +'">'+ data_forum[i] +'</li>');
		}
		forum_ul.innerHTML = forum_arr.join('');
		forum_ul.parentNode.focus();

	});

	//点击版块
	vote_forum_ct.on('click keydown', 'li.J_forum_item', function(e) {
		if(e.type === 'keydown' && e.keyCode !== 13) {
			return;
		}else {
			e.preventDefault();
		}
		fid = $(this).data('fid');
		$(this).addClass('current').siblings('.current').removeClass('current');
		vote_post_to_forum.text($(this).text().replace(/-/g, ''));								//发帖到_版块
		vote_forum_submit.removeClass('disabled').removeProp('disabled');						//确定按钮可用
		if(e.type === 'keydown') {
			vote_forum_submit.focus();
		}
	});

	//跳转发帖页
	vote_forum_submit.on('click', function(e) {
		e.preventDefault();
		var $this = $(this),
				href = $this.data('url') +'&fid='+ fid + '&special=1',
				vote_forum_join = $('#J_vote_forum_join');

		if(vote_forum_join.prop('checked')) {
			//加入版块
			$.post(vote_forum_join.data('url'), {fid : fid}, function(data){
				location.href = href;
			}, 'json');
		}else{
			location.href = href;
		}
		
	});

	//关闭
		$('#J_vote_forum_close').on('click', function(e){
			e.preventDefault();
			$('#J_vote_forum_pop').hide();
		});

})();