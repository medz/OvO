/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前后台-学校组件
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later), region.js
 * $Id$
 */
var setSchool = {};

;(function(){

	var SCHOOL_UNIVERSITY = {},
		SCHOOL_HIGH = {},
		SCHOOL_PRIMARY = {};

	var scholl_wrap,			//学校列表容器
		school_list,			//学校列表
		school_list_loading;	//学校列表 loading

	setSchool = {
		school_temp : '<div class="school_select">\
						在 <span class="b" id="J_region_name"></span> 的学校中搜索：<input id="J_school_search" type="text" class="input length_3 disabled" disabled="disabled">\
					</div>\
					<div id="J_school_filter" class="filter">\
						<a href="" class="current">全部</a><a href="">A</a><a href="">B</a><a href="">C</a><a href="">D</a><a href="">E</a><a href="">F</a><a href="">G</a><a href="">H</a><a href="">I</a><a href="">J</a><a href="">K</a><a href="">L</a><a href="">M</a><a href="">N</a><a href="">O</a><a href="">P</a><a href="">Q</a><a href="">R</a><a href="">S</a><a href="">T</a><a href="">U</a><a href="">V</a><a href="">W</a><a href="">X</a><a href="">Y</a><a href="">Z</a>\
					</div>\
			<div class="list" id="J_scholl_wrap"><div style="display:none;" id="J_school_list_loading" class="pop_loading"></div><ul style="display:none;" class="cc" id="J_school_list"></ul></div>',
		init : function(elem) {
			var _this = this;

			scholl_wrap = $('#J_scholl_wrap');
			school_list = $('#J_school_list');
			school_list_loading = $('#J_school_list_loading');				
			
			var region_name = $('#J_region_name'),
					province_item = $('#J_region_pop_province a.J_item');

			var pid = elem.data('pid'),
				cid = elem.data('cid'),
				did = elem.data('did'),
				sid = elem.data('sid'),
				typeid = parseInt(elem.data('typeid'));

			if(pid) {
				$('#J_province_'+ pid).addClass('current');

				if(did) {
					//中小学 市区
					//region.js
					//_this.getCity(pid, cid, did);

					_this.showSchools(did, sid, typeid);
				}else{
					//大学
					_this.showUniversity(pid, sid);
				}

				_this.searchAbled();
			}else{
				school_list.html('');
				_this.searchDisabled();
			}

			_this.searchSchool(school_list, typeid);
			_this.letterFilter(school_list, typeid);

				//点击省
				province_item.off('click').on('click', function(e){
					e.preventDefault();
					var $this = $(this),
							pid = $(this).parent().data('id');

					//region.js
					_this.btnDisable();

					school_list.html('');
					_this.searchDisabled();

					region_name.text($(this).text());

					if(typeid === 3) {
						_this.searchAbled();

						_this.showUniversity(pid, sid);
					}
					
				});

				//点击市
				$('#J_region_pop_city').off('click').on('click', 'a.J_item', function(e){
					e.preventDefault($('#J_region_pop_province li.current'));
					var text = '';

					_this.searchDisabled();

					if($(this).parent().data('id')){
						text = '-'+ $(this).text();
					}
					school_list.html('');

					region_name.text($('#J_region_pop_province li.current').text() + text);
				});

				//点击区县
				$('#J_region_pop_district').off('click').on('click', 'a.J_item', function(e){
					e.preventDefault();

					//region.js
					_this.btnDisable();

					var current_p = $('#J_region_pop_province li.current'),
							current_c = $('#J_region_pop_city li.current'),
							pid = current_p.data('id'),
							cid = current_c.data('id'),
							did = $(this).parent().data('id'),
							text = '';

					if(did){
						text = '-'+ $(this).text();

						_this.searchAbled();

						_this.showSchools(did, sid, typeid);
						
					}else{
						school_list.html('');
					}
					region_name.text(current_p.text() + '-' + current_c.text() + text);
				});

				//点击学校
				school_list.off('click').on('click', 'li', function(){
					if($(this).data('id')) {
						_this.btnRemoveDisable();
						$(this).addClass('current').siblings().removeClass('current');
					}
				});
			
				//确认
				$('button.J_region_pop_ok').off('click').on('click', function(e){
					e.preventDefault();
					var current = school_list.children('.current');

					elem.val(current.text()).data({
						'pid' : _this.getIds()[0],
						'cid' : _this.getIds()[1],
						'did' : _this.getIds()[2],
						'sid' : current.data('id')
					});
					elem.next('input:hidden').val(current.data('id'));
					$('#J_region_pop').hide();
				});
		},
		showUniversity : function(pid, sid){
			var _this = this;
			//显示大学列表
			try{
				if(SCHOOL_UNIVERSITY[pid]){
					_this.eachSchoolData(SCHOOL_UNIVERSITY[pid], sid);
				}else{
					school_list_loading.show();
					school_list.hide();
					$.ajax({
						url : GV.URL.SCHOOL,
						type : 'post',
						data : {typeid : 3, areaid : pid},
						dataType : 'json',
						success : function(data){
							school_list_loading.hide();
							school_list.show();
							if(!data) {
								school_list.html('<li>暂时没有符合条件的学校</li>');
							}else{
								SCHOOL_UNIVERSITY[pid] = data[pid];
								_this.eachSchoolData(data[pid], sid);
							}
						},
						error : function(){
							school_list.html('<li>数据请求失败，请与管理严联系</li>');
						}
					});
				}
			}catch(e){
				$.error(e);
			}

		},
		showSchools : function(did, sid, typeid){
			//显示中小学学校列表
			var _this = this;

			try{
				var _data = (typeid === 1 ? SCHOOL_PRIMARY : SCHOOL_HIGH);

				if(_data[did]){
					//数据已存在
					_this.eachSchoolData(_data[did], sid);
				}else{
					school_list_loading.show();
					school_list.hide();

					//请求数据
					$.post(GV.URL.SCHOOL, {typeid : typeid, areaid : did}, function(data){
						school_list_loading.hide();
						school_list.show();

						if(!data) {
							school_list.html('<li>暂时没有符合条件的学校</li>');
						}else{
							_data[did] = data[did];
							_this.eachSchoolData(data[did], sid);
						}
					}, 'json');

				}

			}catch(e){
				$.error(e);
			}
		},
		eachSchoolData : function (data, sid){
			//循环写入学校数据

			var u_arr = [];
			$.each(data, function(i, o){
				u_arr.push('<li id="J_school_'+ i +'" data-id="'+ i +'" data-letter="'+ o.letter +'">'+ o.name +'</li>');
			});
			school_list.html(u_arr.join(''));

			//选中当前学校
			var current = $('#J_school_'+ sid);
			if(current.length) {
				scholl_wrap.scrollTop(current.offset().top - school_list.offset().top);
				current.addClass('current');
			}

			//字母筛选-全部
			$('#J_school_filter > a').first().addClass('current').siblings().removeClass('current');
			
		},
		getIds : function(){
			//获取省市地区id
			var current_p = $('#J_region_pop_province li.current'),
					current_c = $('#J_region_pop_city li.current'),
					current_d = $('#J_region_pop_district li.current'),
					pid = current_p.data('id'),
					cid = current_c.data('id'),
					did = current_d.data('id');

			return [pid, (cid ? cid : ''), (did ? did : '')];
		},
		searchAbled : function(){
			$('#J_school_search').removeClass('disabled').prop('disabled', false);
		},
		searchDisabled : function(){
			$('#J_school_search').addClass('disabled').prop('disabled', true).val('');
		},
		searchSchool : function(list, typeid){
			$('#J_school_search').off('keyup').on('keyup', function(){
				var v = $.trim($(this).val());

				if(v) {
					var pid = $('#J_region_pop_province > li.current').data('id'),
						cid = $('#J_region_pop_city > li.current').data('id'),
						did = $('#J_region_pop_district > li.current').data('id');

					var arr = [];
					if(did) {
						var _data = (typeid === 1 ? SCHOOL_PRIMARY : SCHOOL_HIGH);
						//搜中小学
						$.each(_data[did], function(i, o){
							if(RegExp(v).test(o.name)) {
								arr.push('<li data-id="'+ i +'">'+ o.name +'</li>');
							}
						});

					}else{
						
						//搜大学
						$.each(SCHOOL_UNIVERSITY[pid], function(i, o){
							if(RegExp(v).test(o.name)) {
								arr.push('<li data-id="'+ i +'">'+ o.name +'</li>');
							}
						});

					}

					list.html(arr.join(''));
				}else{

				}
				
			});
		},
		letterFilter : function(list, typeid){
			var _this = this;
			$('#J_school_filter a').on('click', function(e){
				e.preventDefault();
				$(this).addClass('current').siblings().removeClass('current');

				var pid = $('#J_region_pop_province > li.current').data('id'),
						cid = $('#J_region_pop_city > li.current').data('id'),
						did = $('#J_region_pop_district > li.current').data('id');

				var child = list.children(),
						letter = $(this).text(),
						data = '';

				_this.btnDisable();

				if(did) {
					//筛中小学
					var _data = (typeid === 1 ? SCHOOL_PRIMARY : SCHOOL_HIGH);
					if(_data[did]) {
						data = _data[did];
					}
				}else{
					//筛大学
					if(SCHOOL_UNIVERSITY[pid]) {
						data = SCHOOL_UNIVERSITY[pid];
					}
					
				}

				_this.filterData(letter, list, data);
				
			});
		},
		filterData : function(letter, list, data){
			var arr = [];
			if(!data) {
				return;
			}

			if(letter == '全部') {
				$.each(data, function(i, o){
					arr.push('<li data-letter="'+ o.name +'" data-id="'+ i +'">'+ o.name +'</li>');
				});
			}else{
				$.each(data, function(i, o){
					if(o.letter == letter) {
						arr.push('<li data-letter="'+ letter +'" data-id="'+ i +'">'+ o.name +'</li>');
					}
				});
			}

			if(arr.length) {
				list.html(arr.join(''));
			}else{
				list.html('暂时没有符合条件的学校');
			}
		},

		btnDisable : function(){
			//确认不可点
			$('button.J_region_pop_ok').addClass('disabled').attr('disabled', 'disabled');
		},
		btnRemoveDisable : function(){
			//确认可点
			$('button.J_region_pop_ok').removeClass('disabled').removeAttr('disabled');
		}
	};

})();