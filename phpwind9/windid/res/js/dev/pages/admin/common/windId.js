/*

 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 后台-权限复制 提交
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later), ajaxForm
 * $Id: forumTree_table.js 15724 2012-08-10 10:20:09Z hao.lin $
 */
;(function () {
	var tr_first = $('#J_client_tbody > tr:first'), //第一项
		status,
		id;

	clientLoop(tr_first);

	//轮循状态
	function clientLoop(tr){
		if(!tr.length) {
			return;
		}
		status = tr.find('.J_status'),
		id = status.data('id');

		$.ajax({
			url : CLIENT_URL,
			type : 'post',
			data : {
				clientid : id
			},
			dataType : 'json',
			success : function(data){
				if(data.state == 'success') {
					status.text('正常');
				}else if(data.state == 'fail'){
					status.html('<span style="color:#ff0000">失败</span>');
				}

				clientLoop(tr.next());
			},
			error : function(){
				
			}
		});
	}
})();