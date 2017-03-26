/*!
 * PHPWind PAGE JS
 * 后台-任务管理
 * Author: linhao87@gmail.com
 */
 
 ;(function(){
	var	task_main = $('#J_task_main');
	
	//切换选项
	$('#J_task_radio input:radio').on('change', function(e){
		var $this = $(this);
		getTaskForum($this.data('url'), $this.data('key'), $this.data('param'));
	});
	
	//页面载入判断
	var checked = $('#J_task_radio input:radio:checked');
	if(checked.length) {
		getTaskForum(checked.data('url'), checked.data('key'), checked.data('param'));
		
		//当前tab
		var tab_item = checked.parents('.J_tab_item');
		$('#'+ tab_item.data('id')).click();
	}
	
	
	//请求对应版块
	function getTaskForum(url, key, param){
		var task_forum = $('tbody.J_task_forum');
		
		$('#J_key').val(key);
		
		if(!url) {
			task_forum.remove();
			return false;
		}

		$.post(url, {'var' : param}, function(data){
			if(data) {
				task_forum.remove();
				task_main.before(data);
			}
		}, 'html');
		
	}
	
	//切换奖励
	var reward_select = $('#J_reward_select');
	
	//切换事件
	reward_select.on('change', function(){
		var $this = $(this),
			op_selected = $this.children(':selected');
			
		getReward(op_selected.data('id'), op_selected.data('url'), op_selected.data('param'));
	});
	
	//页面载入事件
	if(reward_select.val()) {
		var reward_selected = $('#J_reward_select > option:selected');
		getReward(reward_selected.data('id'), reward_selected.data('url'), reward_selected.data('param'));
	}
	
	//获取奖励html片段
	function getReward(id, url, param){
		var reward_forum = $('tbody.J_reward_forum');
		
		//无
		if(!url) {
			reward_forum.remove();
			return false;
		}

		$.post(url, {'var' : param}, function(data){
			if(data) {
				reward_forum.remove();
				task_main.after(data);
			}
		}, 'html');
	}
	
	//监听checkbox
	var checked_all = $('#J_checked_all');
	$('input:checkbox').on('change', function(){
		if($('input.J_check:checked').length === $('input.J_check').length) {
			checked_all.val('1');
		}else{
			checked_all.val('');
		}
	});
	
 })();