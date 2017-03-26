/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-checkbox列表分组
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later), global, GRROUP_DATA
 * $Id: headMsg.js 16997 2012-08-30 07:12:20Z hao.lin $
 */


;(function(){
	GRROUP_DATA = $.parseJSON(GRROUP_DATA);//console.log(GRROUP_DATA)
	var group_check_ul = $('#J_group_check_ul'),		//列表
		_check_arr = [],
		index;

	var saveList = group_check_ul.data('saveList'),		//保存分组
		saveCreat = group_check_ul.data('saveCreat');	//保存创建

	$('a.J_group_trigger').each(function(i, o) {
		var $this = $(this),
			list = $('#J_group_check_list_' + $this.data('id')),
			id = $this.data('id');

		//填充数据
		list.find('ul').html(group_check_ul.html());

		Wind.Util.clickToggle({
			elem : $this,							//触发元素
			list : list,							//隐藏列表
			callback : function(elem, list){
				if(!elem.data('checked')) {
					try{
						var id = elem.data('id'),
							_data = [];

						for(i=0,len=GRROUP_DATA.length;i<len;i++) {
							if(GRROUP_DATA[i]['id'] == id) {
								_data = GRROUP_DATA[i]['items'];
								index = i;
								break;
							}
						}

						$.each(_data, function(i, o){
							list.find('input[data-id='+ o.id +']').prop('checked', true);
						});
						
						elem.data('checked', true);
					}catch(e){
						$.error(e);
					}
					
				}

				//写入默认id
				_id_arr = [];
				$.each(list.find('input:checked'), function(i, o){
					_id_arr.push($(this).data('id'));
				});
			},
			callbackHide : function(elem, list) {
				
				//隐藏创建
				list.find('a.J_group_creat_show').show();
				$('#J_group_creat_wrap').remove();
				
				if(index !== undefined) {
					var id_arr = [];
					//清空原数据
					GRROUP_DATA[index]['items'] = {};

					//获取分组
					$.each(list.find('input:checked'), function(){
						var _id = $(this).data('id');
						id_arr.push(_id);
						//更新数据
						GRROUP_DATA[index]['items'][_id] = $(this).data('value');
					});
				}
			}
		});
	});

	
	//创建新分组
	var temp_creat = '<li id="J_group_creat_wrap" style="">\
								<input id="J_group_creat_input" type="text" class="input" placeholder="最多10个字">\
								<button type="button" class="btn btn_submit J_creat_sub">保存</button><button type="button" class="btn J_creat_cancl">取消</button>\
							</li>';
							
	//显示创建区
	$('.J_group_check_list').on('click', 'a.J_group_creat_show', function(e){
		e.preventDefault();
		var $this = $(this);
		
		$this.hide();
		$this.prev().append(temp_creat);

		var group_creat_input = $('#J_group_creat_input');

		Wind.Util.buttonStatus(group_creat_input, group_creat_input.next('.J_creat_sub'));
		ieHolder($('#J_group_creat_input'));
		
		var creat_wrap = $('#J_group_creat_wrap');
		//取消创建
		creat_wrap.find('button.J_creat_cancl').on('click', function(e){
			e.preventDefault();
			creat_wrap.remove();
			$this.show();
		});
		
		//提交创建
		creat_wrap.find('button.J_creat_sub').on('click', function(e){
			e.preventDefault();

			//保存创建，页面定义
			saveCreat($this);
		});
		
	}).on('change', 'input.J_group_name', function(){
		//选择分组
		var wrap = $(this).parents('.J_group_check_list');
		setGroupNames($(this));

		//保存列表，页面定义
		saveList($('#J_group_trigger_'+wrap.data('id')), this.checked, $(this).data('id'));
	});


	window.setGroupNames = function(elem){
		var wrap = elem.parents('.J_group_check_list'),
			checked = wrap.find('input:checked'),				//选中分组
			text_arr = [],
			insert_v= '';
		
		if(checked.length) {
			$.each(checked, function(i, o){
				text_arr.push($(this).data('value'));
			});
			
			var str = String(text_arr.join(''));
			if(str.length > 5) {
				//选项的文字数已超过5个字
				
				if(String(text_arr[0]).length >= 5) {
					//第一项已经超过5个字
					var s_v = String(text_arr[0]);
					
					insert_v = s_v.match(/\S{5}/)[0] +'…';
				}else{
					//第一项不足5个字
					
					var k = 0, n_arr = [];
					//循环所有选中项
					$.each(text_arr, function(i, o){
						k = k + String(o).length;
						if(k <= 5) {
							//截取相加小于等于5个字的项写入新数组
							n_arr.push(String(o));
						}
					});

					var n_length = n_arr.join('').length;
					if(n_length < 5) {
						//截取小于5个字
					
						//循环含第5个字的项
						var s_arr = [];
						$.each(String(text_arr[n_arr.length]), function(i, o){
							if(i <= (4 - n_length)) {
								s_arr.push(o);
							}
						});

						insert_v = n_arr.join(',') +','+ s_arr.join('') + '…';
					
					}else{
						//刚好截取5个字
						insert_v = n_arr.join(',');
					}
				}

			}else{
				//选取的小于等于5个字
				insert_v = text_arr.join(',');
			}
		}else{
			//未选择
			insert_v = '未分组';
		}

		$('#J_group_trigger_'+ wrap.data('id')).children('.J_group_names').text(insert_v).attr('title', text_arr.join(','));
	}

	
})();



(function(){
	// 侧栏分组管理

	var side_list = $('#J_side_group_list'),
		creat_group_side = $('#J_creat_group_side');

	var temp_edit = '<li id="J_group_edit_wrap">\
			<input id="J_group_edit_input" type="text" class="input" placeholder="最多10个字">\
			<button type="button" class="btn btn_submit" id="J_group_edit_sub">保存</button><button type="button" class="btn" id="J_group_edit_cancl">取消</button>\
		</li>',
		sideSave = side_list.data('save'),
		sub_url;
	
	side_list.on('click', 'a.J_group_edit', function(e){
		//编辑分组
		e.preventDefault();
		var $this = $(this),
			li = $this.parent();

		sub_url = side_list.data('editsave');
		
		$('#J_group_edit_wrap').remove();
		li.siblings('li:hidden').show();
		creat_group_side.show();
		li.hide().after(temp_edit);

		var input = $('#J_group_edit_input');
		input.focus().val($this.data('name')).data('id', $this.data('id'));

		Wind.Util.buttonStatus(input, $('#J_group_edit_sub'));
		ieHolder(input);
	}).on('click', '#J_group_edit_cancl', function(e){
		//取消编辑
		var group_edit_wrap = $('#J_group_edit_wrap');
		group_edit_wrap.siblings('li:hidden').show();
		creat_group_side.show();
		group_edit_wrap.remove();
	}).on('click', '#J_group_edit_sub', function(e){
		//保存
		e.preventDefault();
		var group_edit_input = $('#J_group_edit_input');

		sideSave(group_edit_input, sub_url);
	}).on('click', 'a.J_group_del', function(e){
		//删除分组
		e.preventDefault();
		var $this = $(this),
			href = this.href;
		Wind.Util.ajaxConfirm({
			elem : $this,
			href : href,
			msg : '确定要删除吗？', //<br><span class="gray">此分组下的人不会被取消关注</span> 
			callback : function(){
				$this.parents('li').fadeOut(function(){
					$(this).remove();
					$('input.J_group_name[data-id='+ $this.data('id') +']').parents('li').remove();
				});
			}
		});
	});
	
	//创建
	creat_group_side.on('click', function(e){
		e.preventDefault();
		sub_url = side_list.data('creatsave');

		var group_edit_wrap = $('#J_group_edit_wrap');
		group_edit_wrap.siblings('li:hidden').show();
		group_edit_wrap.remove();
		
		$(this).hide();
		side_list.append(temp_edit);

		var input = $('#J_group_edit_input');

		//global.js
		Wind.Util.buttonStatus(input, $('#J_group_edit_sub'));
		ieHolder(input);
		//input.focus();
	});

	window.ieHolder = function(input){
		if($.browser.msie) {
			if(!input.val()) {
				input.val(input.attr('placeholder'));
			}

			//ie placeholder
			input.addClass('placeholder').focus(function() {
				if(input.val() == input.attr('placeholder')) {
					input.val('');
					input.removeClass('placeholder');
				}
			}).blur(function() {
				if(input.val() == '' || input.val() == input.attr('placeholder')) {
					input.addClass('placeholder');
					input.val(input.attr('placeholder'));
				}
			});
		}
	}

	//ie6 hover
	if($.browser.msie && $.browser.version < 7) {
		side_list.on('mouseenter', 'li', function(){
			$(this).addClass('hover');
		}).on('mouseleave', 'li', function(){
			$(this).removeClass('hover');
		});
	}
})();