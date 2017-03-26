/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 后台-学校管理
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js, jquery.js(1.7 or later), dialog.js, GV.REGION_CONFIG SCHOOL_CONFIG QUICK_LOGIN由footer定义
 * $Id: medal_manage.js 4949 2012-02-28 03:16:33Z hao.lin $
 */
 
Wind.use('region', function(){

	var yarnball_province = $('#J_yarnball_province'),
		yarnball_city = $('#J_yarnball_city'),
		yarnball_district = $('#J_yarnball_district'),
		school_filter = $('#J_school_filter'),
		school_list = $('#J_school_list'),
		input_areaid = $('#J_input_areaid'),
		typeid = $('#J_input_typeid').val(),
		school_add = $('#J_school_add');

	school_filter.region();

	//确定
	$(document).on('click', 'button.J_region_pop_ok', function(e){
			var p_current = $('#J_region_pop_province li.current'),
				c_current = $('#J_region_pop_city li.current'),
				d_current = $('#J_region_pop_district li.current'),
				pid = p_current.data('id'),
				cid = $('#J_region_pop_city li.current').data('id'),
				did = d_current.data('id'),
				areaid = (did ? did : pid),
				arr = [];

			input_areaid.val(areaid);

			if(GV.SCHOOL_CONFIG[areaid]){
				eachSchoolData(GV.SCHOOL_CONFIG[areaid]);
			}else{
				school_list.html('<tr><td colspan="2"><span class="tips_loading">正在查询</span></td></tr>');
				$.post(GV.URL.SCHOOL, {typeid : typeid, areaid : areaid}, function(data){
					if(!data) {
						school_list.html('');
					}else{
						GV.SCHOOL_CONFIG[areaid] = data[areaid];
						eachSchoolData(data[areaid]);
					}
					school_add.show();
				}, 'json');
			}

			yarnball_province.show().children('a.J_yarnball').text(p_current.text());
			if(cid) {
				yarnball_city.show().children('a.J_yarnball').text(c_current.text());
				yarnball_district.show().children('a.J_yarnball').text(d_current.text());
			}
			//school_filter.data('pid', pid).data('did', did)
			school_filter.data({
				'pid' : pid,
				'cid' : cid,
				'did' : did
			});
			$('#J_region_pop').hide();

		});

	//双击编辑
	school_list.on('dblclick', '.J_school_item', function(e){
		var edit_item = $(this).siblings('input');
		if(edit_item.length) {
			//显示编辑
			$(this).hide();
			edit_item.show().focus();
		}else{
			//插入编辑
			$(this).hide().after('<input type="text" name="update['+ $(this).data('id') +']" value="'+ $(this).text() +'" class="input length_2">');
			$(this).siblings('input').focus();
		}
		
	});

	//删除
	Wind.use('dialog',function() {
		school_list.on('click', 'a.J_school_del', function(e){
			e.preventDefault();
			var $this = $(this),
					href = $this.attr('href');

			Wind.dialog({
				message	: '确定删除该学校？', 
				type	: 'confirm', 
				isMask	: false,
				follow	: $(this),//跟随触发事件的元素显示
				onOk	: function() {
					$.getJSON(href).done(function(data) {
						if(data.state === 'success') {
							$this.parents('tr').remove();
						}else if( data.state === 'fail' ) {
							Wind.dialog.alert(data.message);
						}
					});
				}
			});
		});
	});

	//搜索
	$('#J_shcool_search').on('submit', function(e){
		e.preventDefault();
		var input = $(this).children('input:text'),
				pid = school_filter.data('pid'),
				cid = school_filter.data('cid'),
				did = school_filter.data('did'),
				data = (did ? GV.SCHOOL_CONFIG[did] : GV.SCHOOL_CONFIG[pid]),
				v = $.trim(input.val()),
				arr = [];

		if(v && pid) {
			$.each(data, function(i, o){
				if(RegExp(v).test(o.name)) {
					arr.push('<tr><th><div data-id="'+ i +'" class="J_school_item">'+ o.name +'</div></th><td><a class="J_school_del" href="'+ SCHOOL_DEL +'&schoolid='+ i +'">[删除]</a></td></tr>');
				}
			});

			if(arr.length) {
				school_list.html(arr.join(''));
			}else{
				school_list.html('<tr><td colspan="2">没有符合条件的学校</td></tr>');
			}
			
		}else{
			eachSchoolData(data)
		}

	});

	//循环写入学校数据
	function eachSchoolData(data){
		if(!data) {
			return;
		}
		var arr = [];
		$.each(data, function(i, o){
			arr.push('<tr><th><div class="J_school_item" data-id="'+ i +'">'+ o.name +'</div></th><td><a class="J_school_del" href="'+ SCHOOL_DEL +'&schoolid='+ i +'">[删除]</a></td></tr>');
		});
		if(arr.length) {
			school_list.html(arr.join(''));
		}
	}
	
});