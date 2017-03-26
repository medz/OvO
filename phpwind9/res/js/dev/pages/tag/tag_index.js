/*!
 * PHPWind PAGE JS
 * 后台-话题前台页
 * Author: linhao87@gmail.com
 */
 
 ;(function(){
 	var side_my_tags = $('#J_side_my_tags');
	//hover右侧话题
	if($.browser.msie && $.browser.version < 7) {
		$('#J_side_my_tags > li').hover(function(e){
			$(this).find('a.J_tag_del').show();
		}, function(e){
			$(this).find('a.J_tag_del').hide();
		});

		$('.J_tag_items_hd').hover(function(e){
			$(this).addClass('hover');
		}, function(e){
			$(this).removeClass('hover');
		});
	}
	
	//删除右侧话题
	side_my_tags.on('click', 'a.J_tag_del', function(e){
		e.preventDefault();
		var $this = $(this);
		$.getJSON($this.attr('href'), function(data){
			if(data.state === 'success') {
				$this.parent('li').slideUp('slow', function(){
					$(this).remove();

					if(!side_my_tags.children('li').length) {
						location.reload();
					}
				});
				
				$('#J_tag_item_'+ $this.data('id')).slideUp('slow', function(){
					$(this).remove();
				});
			}else{
				Wind.Util.resultTip({
					error : true,
					msg : data.message
				});
			}
		});
	});

 
	//载入更多
	var tag_more = $('#J_tag_more'),
			step = tag_more.data('step');
	tag_more.on('click', function(e){
		e.preventDefault();
		var $this = $(this),
			li_arr = [];

		$.getJSON(this.href, {
			step : step
		}, function(data){
			$.each(data.tags, function(i, o) {
				li_arr.push('<li><a class="icon_del J_tag_del" data-id="'+ o.tag_id +'" href="'+ $this.data('delurl')+ '&id=' + o.tag_id +'">删除</a><a class="title" href="'+ $this.data('viewurl') + '&id=' + o.tag_id +'">'+ o.	
tag_name +'<em>('+ o.content_count +')</em></a></li>');
			});
			$(li_arr.join('')).hide().insertBefore(tag_more.parent('li')).slideDown('fast');
			
			//全部载完毕
			if(!data.step){
				$this.parent().remove();
				return false;
			}else{
				step++;
			}
		});
	});
	
 })();