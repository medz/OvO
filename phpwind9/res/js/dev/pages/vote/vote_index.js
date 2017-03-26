/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-投票
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later), 由页面定义
 * $Id: vote_index.js 21606 2012-12-11 11:33:10Z hao.lin $
 */
 
Wind.use('ajaxForm', function(){
	var vote_list_ul = $('ul.J_vote_list_ul');
	
	//更多
	$('a.J_vote_down').on('click', function(e) {
		e.preventDefault();
		var $this = $(this),
			role = $this.data('role'),
			tid = $this.data('tid'),
			item = $('#J_vote_list_'+ tid);

		$this.parent().hide().siblings('.J_vote_options').show();
		item.find('.J_dn').fadeIn();
		item.find('ul').addClass('ul_line');

	});
	
	//收起
	$('a.J_vote_up').on('click', function(e){
		e.preventDefault();
		var $this = $(this),
			dl = $this.parents('dl'),
			tid = $this.data('tid'),
			item = $('#J_vote_list_'+ tid);
		
		item.find('.J_dn').hide();
		item.find('ul').removeClass('ul_line');
		$this.parent().hide().siblings('.J_vote_more').show();

		//锚点定位 计算
		var doc = $(document),
			doc_sc = doc.scrollTop(),
			header_h = $('#J_header').height();

		if(dl.offset().top <= doc_sc+header_h) {
			location.hash = 'vote' + tid;

			//重新获取滚动高度并减去header高
			doc_sc = doc.scrollTop();
			doc.scrollTop(doc_sc - header_h);
		}
		
	});
	
	//点击投票区 触发下拉
	vote_list_ul.on('click', function(e){
		var $this = $(this),
			elem_hide = $this.find('.J_dn:hidden');

		$this.parent().siblings('.J_vote_more').children('a.J_vote_down').click();
	});
	
	//列表提交
	$('button.J_vote_list_sub').on('click', function(e){
		e.preventDefault();

		$('#J_vote_form_'+ $(this).data('tid')).ajaxSubmit({
			dataType : 'json',
			success : function(data){
				if(data.state === 'success') {
					Wind.Util.resultTip({
						msg : '投票成功',
						callback : function(){
							Wind.Util.reloadPage(window);
						}
					});
				}else if(data.state === 'fail'){
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
				}
			}
		});
	});
	
	//投票多选限制
	$.each(vote_list_ul, function(i, o){
		var $this = $(this),
			vote_checkbox = $this.find('input:checkbox'),			//投票框
			vote_max = parseInt($this.data('max'));					//多选数
			
		if(vote_max) {
		//存在最多项限制
			vote_checkbox.on('change', function(){
			
				//选中数是否等于多选数
				if(vote_checkbox.filter('input:checkbox:checked').length === vote_max) {
					$.each(vote_checkbox, function(){
						if(!$(this).prop('checked')) {
							//未选中项不可用
							$(this).prop('disabled', true);
						}
					});
				}else{
					vote_checkbox.filter(':disabled').prop('disabled', false);
				}
			});
		}
	});
	
	
});