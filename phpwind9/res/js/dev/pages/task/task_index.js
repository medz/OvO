/*!
 * PHPWind PAGE JS
 * 后台-任务前台
 * Author: linhao87@gmail.com
 */
 
;(function(){

	//领取按钮
	$('a.J_task_get_btn').on('click', function(e){
		e.preventDefault();
		var $this = $(this);
		$.getJSON($this.attr('href'), function(data){

			if(data.state === 'success') {
			
				var dialog = Wind.dialog.html($('#J_task_ta').text(), {
					className	: 'pop_deep',	//容器class
					position	: 'fixed',			//固定定位
					isMask		: false,			//无遮罩
					onShow		: function(){
						$('#J_task_link').attr('href', data.message.url);	//任务地址
						$('#J_task_name').text(data.message.title);			//名称
						$('#J_task_reward').text(data.message.reward);		//奖励
						
						//关闭弹窗
						$('#J_task_pop_close').on('click', function(e){
							e.preventDefault();
							$('#J_task_'+ $this.data('id')).fadeOut('fast', function(){
								$(this).remove();
								if(!$('#J_task_list').children().length) {
									location.reload();
								}
							});
							dialog.close();
						});
					}
				});

			}else if(data.state === 'fail') {
				Wind.Util.resultTip({
					error : true,
					msg : data.message
				});
			}
		});
	});
	
})();