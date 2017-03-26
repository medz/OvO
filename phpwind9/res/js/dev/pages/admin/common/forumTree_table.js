/*

 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 后台-版块设置/导航等的添加、删除…操作
 * @Author	: chaoren1641@gmail.com linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later)
 * 	各页面要声明插入的html段落变量，如：一级html变量为"root_tr_html"，子html为"child_tr_html"（某些复杂页面还有其他html段，如”版块设置“）；
 * 	html段所含的"root_", "child_", "id_"等字符(最后一位均为下划线)由以下js代码进行替换，做表单提交参数用；
 * 	各页面的input提交参数会有不同规律和需求。
 *
 * 删除“版块设置”页面添加行的js在其页面定义
 *
 * $Id: forumTree_table.js 16999 2012-08-30 07:14:27Z hao.lin $
 */
$(function () {
	var table_list = $('#J_table_list');
	
	if (table_list.length) {
		var child_id = 1;
		//添加根导航&添加新分类&添加新积分，均为一级内容
		//var temp_id = 1;
		$('#J_add_root').on('click', function (e) {
			e.preventDefault();
			child_id++; //添加一个临时id关联
			var $this = $(this), $tbody;
			
			//转换&输出最终的html段
			if ($this.data('type') === 'credit_root') {
				//积分设置，依赖已有的最新积分的key值，由credit_run.htm页面声明
				last_credit_key = last_credit_key + 1;
				$tbody = $('<tbody>' + root_tr_html.replace(/root_/g, 'root_' + child_id).replace(/credit_key_/g, last_credit_key) + '</tbody>');
			} else {
				//其他
				$tbody = $('<tbody>' + root_tr_html.replace(/root_/g, 'root_' + child_id).replace(/NEW_ID_/g, child_id) + '</tbody>');
			}
			
			//完成添加
			table_list.append($tbody);
			$tbody.find('input.input').first().focus();
			
		});

		
		//添加二级导航&子版块等，均为二级及以下内容
		
		$('#J_table_list').on('click', '.J_addChild', function (e) {
			e.preventDefault();
			child_id++;
			var $this = $(this),
				id = $this.data('id'), //关联父子结构的参数
				$tr;
			
			//其他页面特殊变量
			var forum_level = $this.data('forumlevel') ? parseInt($this.data('forumlevel')) : ''; /*版块设置_级别*/
			
			//转换&输出最终的html段
			if (forum_level && forum_level <= 4) {
			
				//根据添加按钮是否含data-nameid属性，判断父版是否已保存
				if($this.data('nameid')) {
					var name_id = $this.data('nameid');
				}
				//二、三级_版块设置，forumChild()方法在setforum_run.htm模板底部，会返回“二、三级版块”的html段
				$tr = $(forumChild(forum_level, child_id, name_id).replace(/id_/g, id).replace(/NEW_ID_/, child_id));
				
			} else {
			
				//其他子html
				$tr = $(child_tr_html.replace(/child_/g, 'child_' + child_id).replace(/id_/g, id));
				
			}

			//判断插入位置，完成添加
			if ($this.data('html') === 'tbody') {
				//展开下拉
				$this.parents('tr').find('.J_start_icon.start_icon').click();
				
				//添加新版块&添加二级导航，需要判断tbody标签
				
				var tbody = $('#J_table_list_' + id);
				
				//无子内容则创建tbody标签
				if (!tbody.length) {
					tbody = $('<tbody id="J_table_list_' + id + '"/>');
					tbody.insertAfter($this.parents('tbody'));
				}
				
				$tr.prependTo(tbody);
				
			} else if ($this.data('html') === 'tr') {
				//添加二三级版块，html待定
				$tr.insertAfter($this.parents('tr'));
			}
			
			$tr.find('input.input').first().focus();
			
		});
		
		
		
		//新添加的行可直接删除
		table_list.on('click', 'a.J_newRow_del', function (e) {
			e.preventDefault();
			var tr = $(this).parents('tr'),
				tbody = $(this).parents('tbody');

			if(tr.next().length && !tr.prev().length) {
				//删除一级内容
				tbody.remove();
			}else{
				if(tbody.children().length === 1) {
					tbody.remove();
				}else{
					$(this).parents('tr').remove();
				}
			}
		});
		
		
		//树形菜单展开收缩
		var start_icon = $('.J_start_icon');
		start_icon.toggle(function (e) {
			var data_id = $(this).attr('data-id');
			$('#J_table_list_' + data_id).hide(100);
			$(this).removeClass('away_icon').addClass('start_icon');
		}, function () {
			var data_id = $(this).attr('data-id');
			$('#J_table_list_' + data_id).show(100);
			$(this).removeClass('start_icon').addClass('away_icon');
			
		});
		
		
		//展开全部
		$('#J_start_all').on('click', function (e) {
			e.preventDefault();
			var start_icons = $('.J_start_icon.start_icon');
			if (start_icons.length) {
				start_icons.removeClass('start_icon').addClass('away_icon');
				$('tbody[id^="J_table_list"]').show();
			}
		});
		
		
		//收起全部
		$('#J_away_all').on('click', function (e) {
			e.preventDefault();
			var away_icons = $('.J_start_icon.away_icon')
			if (away_icons.length) {
				away_icons.removeClass('away_icon').addClass('start_icon');
				$('tbody[id^="J_table_list"]').hide();
			}
		});
		
		//鼠标移上去显示添加导航按钮
		$('#J_table_list').on('mouseover', 'tr', function (e) {
			$(this).find('a.J_addChild').show();
		}).on('mouseout', 'tr', function (e) {
			$(this).find('a.J_addChild').hide();
		});
		
	}
	
});
