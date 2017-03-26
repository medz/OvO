/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-页面设计
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery(1.8), global, draggable, ajaxForm
 * $Id: message_index.js 5804 2012-03-12 08:58:35Z hao.lin $
 */

;(function(){

	//初始设置
	var designUtil = {},
		mod_wrap = $('div.J_mod_wrap'),
		layout_edit = $('#J_layout_edit'),
		mode_edit = $('#J_mode_edit'),
		mode_edit_tit = $('#J_mode_edit_tit'),
		mode_edit_btn = $('#J_mode_edit_btn'),
		mod_tit_edit = $('#J_mod_tit_edit'),
		design_move_temp = $('#J_design_move_temp'),									//移动模板
		move_lock = true,																							//移动锁定
		layout_id,
		structure,
		layout_sample = $('#J_layout_sample'),
		layout_a = $('#J_layout_sample a'),
		module_a = $('#J_tab_type_ct a'),
		module_url = null,																						//模块请求地址
		mudule_box,
		moduleid = '',
		title_clone = '',										//标题html_添加
		menu_pop = $('div.J_menu_pop'),						//菜单弹窗
		doc = $(document),
		$body = $('body'),
		design_zindex = 100000;											//设计下统一z值

	var layout_edit_pop = $('#J_layout_edit_pop'),											//弹窗
		layout_edit_nav = $('#J_layout_edit_nav'),											//弹窗导航
		layout_edit_contents = $('#J_layout_edit_contents');						//弹窗内容
		design_name = $('#J_design_name'),															//弹窗名
		design_del = $('#J_design_del');																//删除

	var moduleid_input = $('#J_moduleid'),						//值同moduleid 用于单个页面内的js获取
		uniqueid = $('#J_uniqueid').val(),
		pageid = $('#J_pageid').val(),
		dtype = $('#J_type').val(),
		uri = $('#J_uri').val();

	var tabnav_data = $('#J_tabnav_data');

	var tabct_data = $('#J_tabct_data'),
		tabct_push = $('#J_tabct_push');			//推送tab content

	var layout_temp = {
		//结构html
		'100' : '<div role="structure__ID" data-lcm="100" class="box_wrap design_layout_style J_mod_layout" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>100%</span></h2>\
			<div class="design_layout_ct J_layout_item"></div></div>',
		'1_1' : '<div role="structure__ID" data-lcm="1_1" class="box_wrap design_layout_style J_mod_layout design_layout_1_1" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>1:1</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_1_1_left J_layout_item"></div><div class="design_layout_1_1_right J_layout_item"></div></div></div>',
		'1_2' : '<div role="structure__ID" data-lcm="1_2" class="box_wrap design_layout_style J_mod_layout design_layout_1_2" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>1:2</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_1_2_left J_layout_item"></div><div class="design_layout_1_2_right J_layout_item"></div></div></div>',
		'2_1' : '<div role="structure__ID" data-lcm="2_1" class="box_wrap design_layout_style J_mod_layout design_layout_2_1" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>2:1</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_2_1_left J_layout_item"></div><div class="design_layout_2_1_right J_layout_item"></div></div></div>',
		'1_3' : '<div role="structure__ID" data-lcm="1_3" class="box_wrap design_layout_style J_mod_layout design_layout_1_3" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>1:3</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_1_3_left J_layout_item"></div><div class="design_layout_1_3_right J_layout_item"></div></div></div>',
		'3_1' : '<div role="structure__ID" data-lcm="3_1" class="box_wrap design_layout_style J_mod_layout design_layout_3_1" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>3:1</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_3_1_left J_layout_item"></div><div class="design_layout_3_1_right J_layout_item"></div></div></div>',
		'2_3' : '<div role="structure__ID" data-lcm="2_3" class="box_wrap design_layout_style J_mod_layout design_layout_2_3" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>2:3</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_2_3_left J_layout_item"></div><div class="design_layout_2_3_right J_layout_item"></div></div></div>',
		'3_2' : '<div role="structure__ID" data-lcm="3_2" class="box_wrap design_layout_style J_mod_layout design_layout_3_2" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>3:2</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_3_2_left J_layout_item"></div><div class="design_layout_3_2_right J_layout_item"></div></div></div>',
		'1_1_1' : '<div role="structure__ID" data-lcm="1_1_1" class="box_wrap design_layout_style J_mod_layout design_layout_1_1_1" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>1:1:1</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_1_1_1_left J_layout_item"></div><div class="design_layout_1_1_1_cont J_layout_item"></div><div class="design_layout_1_1_1_right J_layout_item"></div></div></div>',
		'1_1_1_1' : '<div role="structure__ID" data-lcm="1_1_1_1" class="box_wrap design_layout_style J_mod_layout design_layout_1111" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>1:1:1:1</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_1111_left J_layout_item"></div><div class="design_layout_1111_left J_layout_item"></div><div class="design_layout_1111_right J_layout_item"></div><div class="design_layout_1111_right J_layout_item"></div></div></div>',
		'2_3_3' : '<div role="structure__ID" data-lcm="2_3_3" class="box_wrap design_layout_style J_mod_layout design_layout_233" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>2:3:3</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_233_left J_layout_item"></div><div class="design_layout_233_cont J_layout_item"></div><div class="design_layout_233_right J_layout_item"></div></div></div>',
		'3_3_2' : '<div role="structure__ID" data-lcm="3_3_2" class="box_wrap design_layout_style J_mod_layout design_layout_332" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>3:3:2</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_332_left J_layout_item"></div><div class="design_layout_332_cont J_layout_item"></div><div class="design_layout_332_right J_layout_item"></div></div></div>',
		'1_4_3' : '<div role="structure__ID" data-lcm="1_4_3" class="box_wrap design_layout_style J_mod_layout design_layout_143" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>1:4:3</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_143_left J_layout_item"></div><div class="design_layout_143_cont J_layout_item"></div><div class="design_layout_143_right J_layout_item"></div></div></div>',
		'3_4_1' : '<div role="structure__ID" data-lcm="3_4_1" class="box_wrap design_layout_style J_mod_layout design_layout_341" style="display:none;">\
			<h2 role="titlebar" class="design_layout_hd cc J_layout_hd"><span>3:4:1</span></h2>\
			<div class="design_layout_ct"><div class="design_layout_341_left J_layout_item"></div><div class="design_layout_341_cont J_layout_item"></div><div class="design_layout_341_right J_layout_item"></div></div></div>',
		'tab' : '<div role="structure__ID" data-lcm="tab" class="box_wrap design_layout_style J_mod_layout J_tab_wrap" style="display:none;">\
			<div role="titlebar" class="design_layout_hd design_layout_tab cc J_layout_hd"><ul class="J_tabs_nav">\
				<li class="current"><a href="" data-id="tab_1">栏目1</a></li><li><a href="" data-id="tab_2">栏目2</a></li>\
			</ul></div>\
			<div class="J_tabs_ct"><div class="design_layout_ct J_tabct_tab_1 J_layout_item" tabid="tab_1"></div>\
			<div class="design_layout_ct J_tabct_tab_2 J_layout_item" tabid="tab_2" style="display:none;"></div></div></div>'
	},
	insert_holder = $('<div id="J_insert_holder" class="insert_holder"></div>'),		//插入位置提示框
	layout_class_mapping = {
		//结构默认class映射
		'100' : 'design_layout_style J_mod_layout',
		'1_1' : 'design_layout_style J_mod_layout design_layout_1_1',
		'1_2' : 'design_layout_style J_mod_layout design_layout_1_2',
		'2_1' : 'design_layout_style J_mod_layout design_layout_2_1',
		'1_3' : 'design_layout_style J_mod_layout design_layout_1_3',
		'3_1' : 'design_layout_style J_mod_layout design_layout_3_1',
		'2_3' : 'design_layout_style J_mod_layout design_layout_2_3',
		'3_2' : 'design_layout_style J_mod_layout design_layout_3_2',
		'1_1_1' : 'design_layout_style J_mod_layout design_layout_1_1_1',
		'1_1_1_1' : 'design_layout_style J_mod_layout design_layout_1111',
		'2_3_3' : 'design_layout_style J_mod_layout design_layout_233',
		'3_3_2' : 'design_layout_style J_mod_layout design_layout_332',
		'1_4_3' : 'design_layout_style J_mod_layout design_layout_143',
		'3_4_1' : 'design_layout_style J_mod_layout design_layout_341',
		'tab' : 'design_layout_style J_mod_layout J_tab_wrap'
	},
	//模块可使用class映射
	module_class_mapping = 'mod_boxA mod_boxB mod_boxC mod_boxD mod_boxE mod_boxF mod_boxG mod_boxH mod_boxI';

	//离开页面提示
	window.onbeforeunload = function() {
		return '您确定要退出页面设计状态？确定退出后，已修改的数据将不会保存。';
	};

	designUtil = {
		commonFn : function (){
			//弹窗内公共方法&组件

			//拾色器
			var color_pick = $('.J_color_pick');
			if(color_pick.length) {
				Wind.use('colorPicker', function() {
					color_pick.each(function() {
						var bg_elem = $(this).find('.J_bg');

						$(this).colorPicker({
							zIndex : design_zindex,
							default_color : 'url("'+ GV.URL.IMAGE_RES +'/transparent.png")',
							callback:function(color) {
								bg_elem.css('background',color);
								$(this).next('.J_hidden_color').val(color.length === 7 ? color : '');
							}
						});
					});
				});
			}

			//日历
			var design_date = $('input.J_design_date');
			if(design_date.length) {
				Wind.use('datePicker',function() {
					design_date.each(function(){
						$(this).datePicker({
							time : true
						});
					});

					$('#calroot').css('zIndex', design_zindex);
				});

			}

			//字体配置
			if($('.J_design_font_config').length) {
				Wind.use('colorPicker', function() {
					$('.J_design_font_config').each(function() {
						var elem = $(this).find('.J_design_color_pick');
						var panel = elem.parent('.J_design_font_config');
						var bg_elem = $(this).find('.J_bg');

						elem.colorPicker({
							zIndex : design_zindex,
							default_color : 'url("'+ GV.URL.IMAGE_RES +'/transparent.png")',
							callback:function(color) {
								bg_elem.css('background',color);

								var v = ( color.length === 7 ? color : '' );
								panel.find('.J_case').css('color', v);
								panel.find('.J_hidden_color').val(v);
							}
						});
					});

					//加粗、斜体、下划线的处理
					$('.J_bold,.J_italic,.J_underline').on('click',function() {
						var panel = $(this).parents('.J_design_font_config');
						var c = $(this).data('class');
						if( $(this).prop('checked') ) {
							panel.find('.J_case').addClass(c);
						}else {
							panel.find('.J_case').removeClass(c);
						}
					});

				});
			}

			//全选
			var design_check_all = $('input.J_design_check_all');
			if(design_check_all.length) {
				var check_wrap = design_check_all.parents('table'),
						checks = check_wrap.find('input.J_design_check');
				//点击全选
				design_check_all.off('change').on('change', function(){
					if(this.checked) {
						checks.prop('checked', true);
					}else{
						checks.prop('checked', false);
					}
				});

				//点击单项
				checks.on('change', function(){
					if(this.checked) {
						if(checks.filter(':checked').length == checks.length) {
							design_check_all.prop('checked', true);
						}
					}else{
						design_check_all.prop('checked', false);
					}
				});
			}

			//地区组件
			if($('a.J_region_change').length) {
				Wind.use('region', function(){
					$('a.J_region_change').region({
						zindex : design_zindex,
						regioncancl : true
					});
				});
			}

			//图片预览
			var input_prev = layout_edit_pop.find('input.J_upload_preview');
			if(input_prev.length) {
				Wind.use('uploadPreview',function() {
					input_prev.uploadPreview();
				});
			}

		},
		eachModCoreY : function(wrap){
			//循环结构模块中心点Y坐标
			var childs = wrap.children('.J_mod_box, .J_mod_layout'),
				arr = [];

			childs.each(function(i, o){
				var off_top = $(this).offset().top,
					y_core = off_top + $(this).height()/2;
				arr.push(y_core);
			});

			wrap.data('ycore', arr);
		},
		insertJudge : function(elem, top){
			//模板插入位置判断
			var _this = this;

			if(elem.find('.J_mod_box, .J_mod_layout').length) {
				var _ycore = elem.data('ycore'),								//当前区域结构数据
					insert_wrap = elem,
					minus_arr = [],
					child_index;

				if(!_ycore) {
					return;
				}

				var selecter = '.J_mod_box, .J_mod_layout';
				//获取中心点差值
				for(i=0,len=_ycore.length; i<len; i++) {
					minus_arr.push(Math.abs(top-_ycore[i]));
				}
				//获取最小值索引
				var mini = Math.min.apply(null, minus_arr);	//最小值
				for(i=0,len=minus_arr.length; i<len; i++) {
					if(minus_arr[i] == mini){
						child_index = i;
						break;
					}
				}

				var taget_el = elem.children(selecter).eq(child_index);

				if(top > _ycore[child_index]) {
					insert_holder.insertAfter(taget_el);
				}else{
					insert_holder.insertBefore(taget_el);
				}
			}else{
				elem.append(insert_holder);
			}

			//重新计算区域结构数据
			_this.eachModCoreY(elem);
		},
		layoutFormSub : function (content, role){
			//结构弹窗提交
			var _this = this;

			//form添加标识
			content.children('form').attr('data-role', role);

			//提交
			$('form.J_design_layout_form').ajaxForm({
				dataType : 'json',
				data : {
					structure : structure ? structure : ''
				},
				beforeSubmit : function(){
					//global.js
					Wind.Util.ajaxBtnDisable(_this.getPopBtn());
				},
				success : function(data, statusText, xhr, $form){
					if(data.state == 'success') {
						var btn = _this.getPopBtn();
						//global.js
						Wind.Util.ajaxBtnEnable(btn);

						_this.popHide();
						Wind.Util.resultTip({
							msg : '操作成功',
							zIndex : design_zindex
						});

						//提交后不tab内容重载
						layout_edit_nav.find('a').data('load', false);
						layout_edit_contents.children(':not(:visible)').html('<div class="pop_loading"></div>');

						var layout = $('#'+ layout_id),																		//编辑的结构
							role = $form.data('role');																		//编辑的区域角色

						if(role == 'layouttitle') {
							//写入标题
							var layout_hd = layout.children('.design_layout_hd, J_layout_hd'),
								tit = data['html']['tab'];

							layout_hd.attr('style', data['html']['background']);

							if(structure == 'tab') {
								layout_hd.find('ul').html(tit);
								var tab_ct_child = layout.find('.J_tabs_ct').children();
								tab_ct_child.hide();

								//匹配tab
								_this.layoutTabMatch(layout);

							}else{
								if(tit) {
									layout_hd.show().html(tit);
								}else{
									//隐藏标题
									layout_hd.hide();
								}

							}
						}else if(role == 'layoutstyle') {
							//写入样式
							var prev_style = layout.prev('style'),
									layout_style = '<style type="text/css" class="_tmp">#'+ data.html.styleDomId[0] +'{'+ data.html.styleDomId[1] +'}#'+ data.html.styleDomId[0] +' a{'+ data.html.styleDomIdLink[1] +'}</style>';
							if(prev_style.length) {
								//已有style标签
								prev_style.replaceWith(layout_style);
							}else{
								layout.before(layout_style);
							}

							if(data.html.styleDomClass) {
								//写入class
								layout.attr('class', layout_class_mapping[layout.data('lcm')]+' '+data.html.styleDomClass);
							}

						}

					}else if(data.state == 'fail') {
						Wind.Util.formBtnTips({
							error : true,
							msg : data.message,
							wrap : btn.parent()
						});
					}

				}
			});

		},
		layoutMoveReset : function(layout_move){
			//重置结构
			layout_move.removeAttr('style').attr({
				'id' : layout_move.data('randomid'),
				'style' : layout_move.data('orgstyle')		//恢复样式
			});
		},
		layoutTabMatch : function(layout) {
			//tab结构标题内容匹配
			try{
				var hd = layout.children('.design_layout_hd, J_layout_hd').children('ul'),
					hd_a = hd.find('a'),
					ct = layout.find('.J_tabs_ct'),
					ct_child = layout.find('.J_tabs_ct').children();

				//先清除不存在的内容
				ct.children().each(function(){
					var ct_tabid = $(this).attr('tabid');

					if(!hd.find('a[data-id='+ ct_tabid +']').length) {
						//不存在对应的tab项
						$(this).remove();
					}
				});
				
				for(i=0,len=hd_a.length;i<len;i++) {
					var $this = $(hd_a[i]),
						tab_id = $this.data('id'),
						tab_index = $this.parents('li').index(),
						ct_child_item = ct_child.filter(':eq('+ tab_index +')');

					if(ct_child_item.attr('tabid') !== tab_id) {
						//不匹配 删除，重新匹配
						ct.append('<div style="display:none;" class="design_layout_ct J_layout_item" tabid="'+ tab_id +'"></div>');
					}
				};

				//重新绑定tab组件
				hd.removeData('plugin_tabs');
				layout.find('.J_tabs_nav').first().tabs(layout.find('div.J_tabs_ct').first().children('div'));

			}catch(e){
				$.error(e);
			}

		},
		layoutTitEditPos : function (elem, tar){
			//结构&标题编辑框 定位显示
			var lcm = tar.data('lcm');
			if(lcm == 'tab') {
				elem.data('structure', 'tab');
			}else{
				elem.data('structure', '');
			}

			elem.css({
				left : tar.offset().left + tar.outerWidth() - elem.width(),
				top : tar.offset().top
			}).data('id', tar.attr('id')).show();

		},
		moveTemp : function(move_elem, left, top){
			var _this = this;
			//移动模板结构

			//获取移动中心点元素
			var elem_point = document.elementFromPoint(left, top - $(document).scrollTop());
			window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty();

			var $elem_point = $(elem_point),
				wrap_parent = $elem_point.parents('.J_mod_wrap');

			if($elem_point.hasClass('insert_holder')) {
				return;
			}

			if($elem_point.hasClass('J_mod_wrap')) {

				if(!module_url) {
					//移动结构
					_this.insertJudge($elem_point, top);
				}else{
					//移动模块
					if(!$elem_point.find('div.J_mod_layout').length) {
						//结构为空
						return;
					}
				}

			}else if(wrap_parent.length){


				var item_parent = $elem_point.parents('.J_layout_item').first(),
					lay_parent = $elem_point.parents('.J_mod_layout').first();

				if($elem_point.hasClass('J_layout_item')) {
					//设计区的J_layout_item内

					if(module_url){
						//移动模板且为空
						if(!$elem_point.children('.J_mod_box, .J_mod_layout').length) {
							_this.insertJudge($elem_point, top);
						}

					}else{
						var parents_mod_layout_len = $elem_point.parentsUntil('div.J_mod_wrap').filter('.J_mod_layout').length,	//已有的J_mod_layout层数
								move_mod_layout_len = move_elem.find('.J_mod_layout').length;																				//移动模板里的J_mod_layout层数
						if(parents_mod_layout_len < 2 && move_mod_layout_len < 1){
							//设计区小于2层且没有模块数据

							//位置判断
							_this.insertJudge($elem_point, top);

							//重新计算区域结构数据
							//eachModLayout($elem_point.parents('div.J_mod_wrap'));
						}else{
							//超过2层 则定位到上层
							var par_item = $elem_point.parents('.J_layout_item');
							if(par_item.parentsUntil('div.J_mod_wrap').filter('.J_mod_layout').length < 2) {
								_this.insertJudge(par_item, top);
							}
						}
					}
					return;
				}else if(item_parent.length){
					if(!module_url){
						var _len = $elem_point.parentsUntil('div.J_mod_wrap').filter('.J_mod_layout').length;
						if(_len >= 2) {
							return;
						}
					}
					_this.insertJudge(item_parent, top);
					return;
				}else{
					if(!module_url){
						if(!$elem_point.hasClass('insert_holder')) {
							_this.insertJudge($elem_point.parents('.J_mod_wrap'), top);
						}
					}
				}

			}else{
				var _mod_wrap = insert_holder.parents('.J_mod_wrap');	//被移出的自定义区域

				//设计区外
				insert_holder.remove();
			}
		},
		popHide : function(type){
			//隐藏弹窗并恢复内容为loading

			layout_edit_pop.hide();

			if(type == 'loading') {
				layout_edit_contents.children().html('<div class="pop_loading"></div>');
			}else if(type == 'error') {
				Wind.Util.resultTip({
					error : true,
					msg : '提交出错，请稍后刷新再试'
				});
			}
			
			this.popTabReset();
		},
		popTabReset : function(){
			//弹窗重置

			//先清空为loading效果
			layout_edit_contents.children().html('<div class="pop_loading"></div>');

			//tab清空已点击标识
			layout_edit_nav.find('a').data('load', false);
		},
		updatePopList : function(btn, updatemod, hidepop){
			//更新弹窗列表内容
			var _this = this;
			if(btn) {
				Wind.Util.ajaxBtnDisable(btn);
			}


			try{
				$.post(layout_edit_nav.children('.current').children().attr('href'), {
					moduleid : moduleid,
					pageid:pageid
				}, function(data){
					//global.js
					Wind.Util.ajaxMaskRemove();

					if(btn) {
						Wind.Util.ajaxBtnEnable(btn);
					}

					if(Wind.Util.ajaxTempError(data)) {
						return false;
					}
					//重新列表
					var current_content = layout_edit_contents.children(':visible');
					current_content.html(data);

					if(btn) {
						Wind.Util.formBtnTips({
							wrap : btn.parent(),
							msg : '操作成功'
						});
					}

					//current_content.find('.J_scroll_fixed').scrollFixed();
					_this.commonFn();

					//是否更新模块
					if(updatemod && mudule_box.children(':not(.J_mod_layout)').length) {
						updateModuleList(moduleid, mudule_box, hidepop);
					}
				});
			}catch(e){
				$.error(e);
			}
		},
		getPopBtn : function(){
			//获取弹窗提交按钮
			return layout_edit_contents.children(':visible').find('button:submit');
		}
	};

	$.ajaxSetup({
		error : function(jqXHR, textStatus, errorThrown){
			if(errorThrown) {
				Wind.Util.ajaxMaskRemove();
				designUtil.popHide('error');
			}
		}
	})

	//已有编辑模块 循环结构数据
	var mod_wrap_len = mod_wrap.length;
	var J_mod_layout = $('.J_mod_layout');
	for(i=0,len=J_mod_layout.length;i<len;i++){
		designUtil.eachModCoreY($(J_mod_layout[i]).parent());

	}

	//已有编辑模块 循环模块数据
	var saved_layout_items = $('.J_layout_item');
	for(i=0, len=saved_layout_items.length;i<len;i++){
		var item = $(saved_layout_items[i]);
		if(item.find('.J_mod_box').length){
			designUtil.eachModCoreY(item);
		}
	}

	//防止链接元素被拖拽
	$('#J_layout_sample a, #J_tab_type_ct a').each(function(){
		this.ondragstart = function (e) {
			return false;
		};
	});

	//ie 防止弹出离开页面提示
	$('#J_design_top_ct a').on('click', function(e){
		e.preventDefault();
	});

	//去除锚点
	if(location.hash) {
		location.hash = '';
	}

	Wind.use('tabs', function(){
		$('#J_design_top_nav').tabs($('#J_design_top_ct > div'));
		$('#J_tab_type_nav').tabs($('#J_tab_type_ct > div'));
		layout_edit_nav.tabs(layout_edit_contents.children('div'));
	});




//操作

	//点击结构项
	var leftx,topx;
	layout_a.on('mousedown', function(e) {

		//解锁
		move_lock = false;

		module_url = undefined;

		//显示移动模板
		design_move_temp.show().css({
			left : e.pageX - 20,
			top : e.pageY - 20
		}).data('name', $(this).data('name'));

		//防止界面选择区被拖动
		layout_sample.css({
			'overflowY': 'hidden'
		});

		$body.addClass('move');

		//鼠标拖动
		doc.off('mousemove').on('mousemove', function(e){
			if(!move_lock) {
				leftx = e.pageX,
				topx = e.pageY;

				//模板定位
				design_move_temp.show().css({
					left : leftx + 5,	//+5像素 防止point定位到拖动上
					top : topx + 5
				});

				designUtil.moveTemp(design_move_temp, leftx, topx);

			}
		});

	});


	//点击模块
	var model;
	module_a.on('mousedown', function(e){
		//判断是否有结构
		if(!$('div.J_mod_layout').length) {
			$body.removeClass('move');
			Wind.Util.resultTip({
				error : true,
				msg : '选择结构后才能设置模块，请先选择结构',
				zindex : design_zindex
			});
			return false;
		}

		module_url = this.href;
		model = this.id;
		//解锁
		move_lock = false;

		//显示移动模板
		design_move_temp.show().css({
			left : e.pageX - 20,
			top : e.pageY - 20
		});
		$body.addClass('move');

		//鼠标拖动
		doc.off('mousemove').on('mousemove', function(e){
			if(!move_lock) {
				leftx = e.pageX,
				topx = e.pageY;

				//模板定位
				design_move_temp.show().css({
					left : leftx + 5,
					top : topx + 5
				});

				designUtil.moveTemp(design_move_temp, leftx, topx);

			}
		});

	}).on('click', function(e){
		e.preventDefault();
	});

	//设置模板 导入
	$('a.J_nav_import').on('click', function(e){
		e.preventDefault();

		if(confirm('导入后会覆盖之前的页面数据，请慎重考虑！')) {
			Wind.Util.ajaxMaskShow(design_zindex);
			$.post(this.href, function(data){
				if(data.state == 'success') {
					window.onbeforeunload = null;
					Wind.Util.reloadPage(window);
				}else if(data.state == 'fail'){
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
				}
			}, 'json');
		}
	});


	//鼠标抬起 取消移动; iframe里无法获取
	doc.on('mouseup', function(e){
		move_lock = true;
		$body.removeClass('move');

		doc.off('mousemove');

		layout_sample.css({
			'overflowY': 'auto'
		});

		var wrap = $('#J_insert_holder').parent(),
				insert_holder_visible = $('#J_insert_holder:visible'),
				layout_move = $('#J_layout_move');												//已有的结构

		if(insert_holder_visible.length) {
			//有占位模板
			$('#J_mod_box').remove();

			if(layout_move.length) {
				//移动已有结构
				layout_move.attr({
					'id' : layout_move.data('randomid'),
					'style' : layout_move.data('orgstyle')		//恢复样式
				});
				layout_move.hide().insertAfter(insert_holder_visible).fadeIn('200');
				insert_holder_visible.remove();

				//eachModLayout();
				//eachModCoreY(layout_move.parent())
			}else{
				if(module_url) {
					//请求模块
					layout_item = insert_holder.parent('.J_layout_item');

					//加入隐藏模块区
					insert_holder_visible.after('<div id="J_mod_box" style="display:none;"></div>')

					try{
						$.post(MODULE_ADD_TAB, {
							model : model,
							pageid : pageid
						}, function(data){
							if(data.state == 'success') {
								menu_pop.hide();

								design_name.text('模块管理');
								design_del.text('删除该模块').data('role', 'module');

								design_del.hide();
								//global.js
								Wind.Util.popPos(layout_edit_pop);

								layout_edit_nav.children().hide();
								var _data = data.data,
									nav_temp = false;
								for(i=0,len=_data.length; i<len; i++) {
									if(_data[i] == 'template') {
										nav_temp = true;
										break;
									}
								}

								getModule(layout_item, model, nav_temp);
							}else if(data.state == 'fail') {
								Wind.Util.resultTip({
									error : true,
									zindex : design_zindex,
									msg : data.message
								});
							}

							module_url = null;
						}, 'json');
					}catch(e){
						$.error(e);
						designUtil.popHide('error');
					}

					insert_holder.remove();

				}else{
					//插入新结构
					var randomid = randomSix(),
							name = design_move_temp.data('name');
					$(layout_temp[name].replace(/_ID/, randomid)).insertAfter(insert_holder).fadeIn('200').attr('id', randomid);
					insert_holder_visible.remove();

					if(name == 'tab') {
						var $randomid = $('#'+ randomid);
						$randomid.find('.J_tabs_nav').first().tabs($randomid.find('div.J_tabs_ct').first().children('div'));
					}

				}

			}

		}else{
			//恢复原结构位置
			designUtil.layoutMoveReset(layout_move);
		}

		//隐藏拖拽
		design_move_temp.hide();

	}).on('keyup', function(e){
		//按esc
		if(e.keyCode === 27) {
			var layout_move = $('#J_layout_move');

			//重置正拖拽结构
			if(layout_move.length) {
				designUtil.layoutMoveReset(layout_move);
			}

			//重置空的拖拽区
			if(!move_lock) {
				design_move_temp.hide();
				insert_holder.remove();
			}

			if(layout_edit_pop.is(':visible')) {
				//关闭弹窗
				designUtil.popHide('loading')
			}
		}

	});

	//生成6位随机字母
	function randomSix(){
		var cha = '';
		for(i=1;i<=6;i++){
			cha += String.fromCharCode(Math.floor( Math.random() * 26) + "a".charCodeAt(0));
		}
		return cha;
	}

	//获取模块
	function getModule(layout_item, model, nav_temp){
		var current_nav = $('#J_moduleproperty_add'),							//当前tab项
			current_content = layout_edit_contents.children(':eq('+ current_nav.index() +')');			//当前内容项

		designUtil.popTabReset();
		current_nav.show().click();

		try{
			$.post(module_url, {
				pageid :pageid,
				model : model,
				struct : layout_item.parents('.J_mod_layout').attr('id')
			}, function(data){
				if(Wind.Util.ajaxTempError(data)) {
					return false;
				}

				current_content.html(data);
				if(nav_temp) {
					$('#J_property_add').text('下一步');
				}

				//jquery.scrollFixed
				//current_content.find('.J_scroll_fixed').scrollFixed();
				designUtil.commonFn();

				var btn = designUtil.getPopBtn();
				//提交
				$('form.J_design_module_form').ajaxForm({
					dataType : 'json',
					beforeSubmit : function(){
						Wind.Util.ajaxBtnDisable(btn);
					},
					success : function(data, statusText, xhr, $form){
						if(data.state == 'success') {
							moduleid = data.data;
							moduleid_input.val(moduleid);

							//写入模块html
							$.post(MODULE_BOX_UPDATE, {moduleid : moduleid}, function(data){
								Wind.Util.ajaxBtnEnable(btn);
								if(Wind.Util.ajaxTempError(data)) {
									designUtil.popHide('loading');
									return false;
								}
								mudule_box = $('#J_mod_box');
								mudule_box.html(data).show();
								mudule_box.attr({
									'id' : 'J_mod_'+ moduleid,
									'data-id' : moduleid,
									'data-model' : model,
									'class' : 'mod_box J_mod_box'
								});

								if(nav_temp) {
									//触发模板tab
									var tabnav_template = $('#J_tabnav_template');
									tabnav_template.show().click().siblings().hide();
									tabnav_template.children('a').click();
								}else{
									designUtil.popHide('loading');
									Wind.Util.resultTip({
										msg : '操作成功',
										zIndex : design_zindex
									});
								}

							});
						}else if(data.state == 'fail'){
							Wind.Util.ajaxBtnEnable(btn);
							Wind.Util.formBtnTips({
								error : true,
								msg : data.message,
								wrap : btn.parent()
							});
						}
					}
				});

			});
		}catch(e){
			$.error(e);

			designUtil.popHide('error');
		}
	}

	//拖拽已添加的结构
	mod_wrap.off('mousedown').on('mousedown', '.J_layout_hd', function(e){
			var wrap = $(this).parent(),
					wrap_left = wrap.offset().left,
					wrap_top = wrap.offset().top,
					org_style = wrap.attr('style');			//原样式

			//点击链接不拖动
			if(e.target.tagName.toLowerCase() == 'a') {
				return false;
			}

			move_lock = false;

			wrap.css({
				width : wrap.width(),
				position : 'absolute',
				zIndex : 1,
				opacity : 0.6,
				left :wrap_left,
				top : e.pageY + 2
			}).data({
				'randomid' : wrap.attr('id'),
				'orgstyle' : org_style ? org_style : ''
			}).attr('id', 'J_layout_move');
			$body.addClass('move');

			var dis_left = e.pageX - wrap_left,
				dis_top = e.pageY - wrap_top;

			doc.off('mousemove').on('mousemove', function(e){
				if(move_lock) {
					return false;
				}

				leftx = e.pageX,
				topx = e.pageY;

				//模板定位
				wrap.css({
					left : e.pageX - dis_left,
					top : e.pageY + 2
				});

				designUtil.moveTemp(wrap, leftx, topx);
			});
		});


//结构&标题&模块编辑 显示隐藏
	//结构编辑
	mod_wrap.on('mouseenter', 'div.J_mod_layout, .design_layout_style', function(e){
		//e.stopPropagation();
		var $this = $(this),
				tar = $(e.target);

		if(!move_lock) {
			return false;
		}

		//移入模块
		var mod_box = tar.parents('.J_mod_box');
		if(mod_box.length) {
			modEditPos(mod_box);
		}

		//父结构
		var layout_box = tar.parents('.J_mod_layout');
		if(layout_box.length) {
			designUtil.layoutTitEditPos(layout_edit, layout_box);
		}

		//本结构
		if(tar.is('.J_mod_layout')) {
			designUtil.layoutTitEditPos(layout_edit, $this);
		}

	}).on('mouseleave', 'div.J_mod_layout, .design_layout_style', function(e){
		e.stopPropagation();
		if(!move_lock) {
			return false;
		}
		var rel_tar = $(e.relatedTarget);
		if(!rel_tar.is('#J_layout_edit')) {
			//移进父结构
			if(rel_tar.is('.J_mod_layout')) {
				designUtil.layoutTitEditPos(layout_edit, rel_tar);
			}else if(rel_tar.parents('div.J_mod_layout').length) {
				designUtil.layoutTitEditPos(layout_edit, rel_tar.parents('div.J_mod_layout'));
			}else{
				layout_edit.hide();
			}
		}

	});

	layout_edit.on('mouseleave', function(){
		layout_edit.hide();
	});

	//标题编辑
	$('div.J_mod_title').hover(function(e){
		designUtil.layoutTitEditPos(mod_tit_edit, $(this));
	}, function(e){
		var rel_tar = $(e.relatedTarget);
		if(!rel_tar.is(mod_tit_edit)) {
			mod_tit_edit.hide();
		}
	});

	mod_tit_edit.on('mouseleave', function(){
		mod_tit_edit.hide();
	});

	//模块编辑
	$(document).on('mouseenter', 'div.J_mod_box', function(e){
		e.stopPropagation();

		if (move_lock) {
			modEditPos($(this));
			mode_edit_btn.data({
				'id': $(this).data('id'),
				'model' : $(this).data('model')
			});
		}
	});

	mode_edit.on('mouseleave', function(e){
		mode_edit.hide();
	});

	//模块编辑框 定位显示
	function modEditPos(tar){
		if(!tar.data('id')) {
			return ;
		}
		mode_edit.css({
			width : tar.outerWidth(),
			height : tar.outerHeight(),
			left : tar.offset().left,
			top : tar.offset().top
		}).show();
	}


	var layouttitle = $('#J_layouttitle'),
			layoutstyle = $('#J_layoutstyle');
	layout_edit_pop.draggable( { handle : '.J_drag_handle'} );

	//点击结构编辑
	layout_edit.on('click', function(e){
		e.preventDefault();
		var $this = $(this),
			index = layouttitle.index(),
			content = layout_edit_contents.children(':eq('+ index +')'),
			role = layouttitle.children().data('role'),
			edit_data = {},
			title;

			structure = $this.data('structure');

		menu_pop.hide();

		designUtil.popTabReset();

		layout_id = $this.data('id'),
		design_name.text('结构管理');
		design_del.text('删除该结构').data('role', 'layout').show();
		//btn = $this;
		layout_edit_nav.children().children().data('load', false);


		Wind.Util.popPos(layout_edit_pop);

		layouttitle.click().show().children().attr('data-submit', false);	//默认未提交
		layouttitle.siblings().hide();
		layoutstyle.show();

		edit_data['name'] = layout_id;
		edit_data['pageid'] = pageid;
		edit_data['title'] = $('#'+layout_id).children('.J_layout_hd').text();;

		if(structure == 'tab') {
			//tab结构数组
			var tab_arr = [];
			$('#'+ layout_id + ' .J_tabs_nav li').each(function(){
				tab_arr.push($(this).children('a').data('id'));
			})
			edit_data['tab'] = tab_arr;
			edit_data['title'] = undefined;
		}

		try{
			$.post(LAYOUT_EDID_TITLE, edit_data, function(data){
				if(Wind.Util.ajaxTempError(data)) {
					return false;
				}
				content.html(data);

				//jquery.scrollFixed
				//content.find('.J_scroll_fixed').scrollFixed();

				designUtil.layoutFormSub(content, role);
				designUtil.commonFn();

				title_clone = content.find('div.J_mod_title_cont').clone().html();		//添加新标题的html复制

			});
		}catch(e){
			$.error(e);
			designUtil.popHide('error');
		}
	});

	//点击模块编辑
	mode_edit_btn.on('click', function(e){
		e.preventDefault();
		var $this = $(this);

		menu_pop.hide();

		//tab内容
		designUtil.popTabReset();

		design_name.text('模块管理');
		moduleid = $this.data('id');
		moduleid_input.val(moduleid);
		model = $this.data('model');
		mudule_box = $('#J_mod_' + moduleid);
		design_del.text('删除该模块').data('role', 'module');

		//判断tab显示
		Wind.Util.ajaxMaskShow(design_zindex);
		
		try{
			$.post(MODULE_EDIT_JUDGE, {
				moduleid : moduleid,
				pageid:pageid
			}, function(data){
				if(data.state == 'success') {


					layout_edit_nav.children().hide();
					//显示tab项
					var tab_arr = data.data;
					if(tab_arr.length) {
						for(i=0,len=tab_arr.length; i<len; i++) {
							$('#J_tabnav_' + tab_arr[i]).show();

							//是否显示 删除模块
							if(tab_arr[i] == 'delete') {
								design_del.show();
							}else{
								design_del.hide();
							}
						};
					}

					//global.js
					Wind.Util.popPos(layout_edit_pop);

					var current_nav = layout_edit_nav.children(':visible').first();
					current_nav.click();
					var current_content = layout_edit_contents.children(':visible').first();

					//获取第一个tab内容
					$.post(current_nav.children().attr('href'), {
						moduleid : moduleid,
						pageid:pageid
					}, function(data){
						if(Wind.Util.ajaxTempError(data)) {
							designUtil.popHide();
							return false;
						}
							Wind.Util.ajaxMaskRemove();

							current_content.html(data);
							current_nav.children().data('load', true);

							//jquery.scrollFixed
							//current_content.find('.J_scroll_fixed').scrollFixed();

							designUtil.commonFn();

							if(current_nav.children().data('type') == 'title') {
								//模块标题添加复制
								title_clone = current_content.find('div.J_mod_title_cont').clone().html();
							}
					});

				}else{
					//global.js
					Wind.Util.ajaxMaskRemove();
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
				}
			}, 'json');
		}catch(e){
			$.error(e);
			designUtil.popHide('error');
		}

	});

	//标题编辑
	mod_tit_edit.on('click', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		Wind.Util.ajaxMaskShow(design_zindex);
		
		try{
			$.post(this.href, {name : id, pageid : pageid}, function(data){
				Wind.Util.ajaxMaskRemove();
				if(Wind.Util.ajaxTempError(data)) {
					designUtil.popHide();
					return false;
				}
					var moduletitle = $('#J_moduletitle'),
							current_content = layout_edit_contents.children(':eq('+ moduletitle.index() +')');
					design_name.text('标题编辑');
					design_del.hide();
					moduletitle.show().addClass('current').siblings().hide();
					current_content.html(data).show().siblings().hide();

					Wind.Util.popPos(layout_edit_pop);

					$('#J_design_tit_form').ajaxForm({
						dataType : 'json',
						beforeSubmit : function(){
							Wind.Util.ajaxMaskShow(design_zindex);
						},
						success : function(data){
							Wind.Util.ajaxMaskRemove();
							if(data.state == 'success') {
								layout_edit_pop.hide();
								$('#' + id).html(data.html);
							}else{
								Wind.Util.resultTip({
									error : true,
									msg : data.message,
									zindex : design_zindex
								});
							}
						}
					});
			}, 'html');
		}catch(e){
			$.error(e);
			designUtil.popHide('error');
		}
	});


	//点击弹窗tab
	layout_edit_nav.find('a').on('click', function(e){
		e.preventDefault();
		var $this = $(this),
				role = $this.data('role'),
				type = $this.data('type'),
				index = $this.parent().index(),
				current_content = layout_edit_contents.children(':eq('+ index +')');		//当前内容区

		if(!$this.data('load')) {
			//还没加载
			//current_content.html('<div class="pop_loading"></div>');

			try{
				if(role == 'layoutstyle') {
					//结构样式
					$.post(LAYOUT_EDID_STYLE, {name : layout_id}, function(data){
						if(Wind.Util.ajaxTempError(data)) {
							return false
						}
							current_content.html(data);

							//jquery.scrollFixed
							//current_content.find('.J_scroll_fixed').scrollFixed();

							$this.data('load', true);
							designUtil.layoutFormSub(current_content, role, '');
							designUtil.commonFn();
					});
				}else if(role == 'module') {
					//模块tab
					$.post(this.href, {moduleid : moduleid}, function(data){
						if(Wind.Util.ajaxTempError(data, undefined, design_zindex)) {
							designUtil.popHide('loading');
							return false;
						}

							current_content.html(data);

							if(current_content.find('#J_design_temp_tpl').length) {
								//Wind.use('rangeInsert');
							}

							//jquery.scrollFixed
							//current_content.find('.J_scroll_fixed').scrollFixed();

							//模块模板 global.js
							Wind.Util.buttonStatus($('#J_design_temp_name'), $('#J_design_temp_sub'));

							$this.data('load', true);
							designUtil.commonFn();

							if(type == 'title') {
								title_clone = current_content.find('div.J_mod_title_cont').clone().html();		//添加新标题的html复制
							}

					});
				}
			}catch(e){
				$.error(e);
				designUtil.popHide('error');
			}
		}

		//layout_edit_contents.children(':eq('+ $this.parent().index() +')').children()
	});

	//关闭编辑
	$('#J_layout_edit_close').on('click', function(e){
		e.preventDefault();
		designUtil.popHide();
	});

	//删除结构&模块
	design_del.on('click', function(e){
		e.preventDefault();
		var role = $(this).data('role');
		if(role == 'layout') {
			//删除结构
			var layout = $('#'+ layout_id);

			if(layout.find('.J_mod_box').children().length || layout.find('.J_mod_layout').length) {
				alert('请先清空该结构下的模块或子结构');
				return false;
			}

			layout.remove();

			designUtil.popHide('loading');
		}else{
			//删除模块
			if(confirm('您确定要删除本模块吗？删除后将不可恢复！')) {
				//global.js
				Wind.Util.ajaxMaskShow(design_zindex);

				try{
					$.post(MODULE_BOX_DEL, {moduleid: moduleid}, function(data){
						//global.js
						Wind.Util.ajaxMaskRemove();

						if(data.state == 'success') {
							//移除
							mudule_box.remove();
						}else{
							//global.js
							Wind.Util.resultTip({
								error : true,
								msg : data.message
							});
						}
						designUtil.popHide('loading');
					}, 'json');
				}catch(e){
					$.error(e);
					designUtil.popHide('error');
				}

			}
		}

	});

	//分别设置
	layout_edit_contents.on('click', 'input.J_set_part', function(){
		var $this = $(this),
				last = $this.parents('dd').children(':last'),
				prev_last = last.prev();
		if($this.prop('checked')) {
			last.show();
			prev_last.hide();
		}else{
			last.hide();
			prev_last.show();
		}
	});


	//更新模块列表内容
	function updateModuleList(moduleid, mudule_box, hidepop, apply){
		//有列表内容
		var box = mudule_box.find('.mod_box'),		//模块内容容器
			clone = mudule_box.clone();
		mudule_box.html('<div class="pop_loading"></div>');
		
		try{
			$.post(MODULE_BOX_UPDATE, {moduleid : moduleid}, function(data){
				//global.js
				if(apply == 'apply') {
					Wind.Util.ajaxBtnEnable($('button.J_module_apply'));
				}else{
					var btn = designUtil.getPopBtn();
					Wind.Util.ajaxBtnEnable(btn);
				}

				if(Wind.Util.ajaxTempError(data)) {
					//出错
					mudule_box.html(clone);
					Wind.Util.formBtnTips({
						error : true,
						wrap : btn.parent(),
						msg : data.message
					});
					return;
				}

				//返回列表
				$('#J_module_data_back').click();

				//成功
				if(hidepop) {
					designUtil.popHide('loading');
					Wind.Util.resultTip({
						msg : '操作成功',
						zindex : design_zindex
					});
				}

				mudule_box.html(data);
				//头像
				var avatars = mudule_box.find('img.J_avatar');
				if(avatars.length) {
					Wind.Util.avatarError(avatars);
				}

			});
		}catch(e){
			$.error(e);
		}
	}


	//模块管理
	layout_edit_contents.on('click', 'a.J_data_edit', function(e){
		//显示内容编辑
		e.preventDefault();
		var id = $(this).data('id'),
				//current_edit = $('#J_module_data_'+ id),					//当前编辑内容
				module_data_list = $('#J_module_data_list'),			//显示内容列表
				module_data_edit = $('#J_module_data_edit');			//显示内容编辑区

		module_data_list.hide();
		module_data_edit.show();
		$.post(this.href, function(data){
			//global.js
			if(Wind.Util.ajaxTempError(data)) {
				return false;
			}
			module_data_edit.html(data);
			designUtil.commonFn();
		});

	}).on('click', 'button.J_mod_title_add', function(e){
		//添加新标题
		e.preventDefault();
		var wrap = $(this).parents('.pop_cont');		//默认项
		$('<a style="margin:5px;" class="fr J_mod_title_del" href="">删除此标题</a><div class="J_mod_title_cont">'+title_clone +'</div>').insertBefore(wrap.children(':last')).find('input, .J_color_pick >em').val('').removeAttr('style');

		wrap.parent().scrollTop(9999);
	}).on('click', 'a.J_mod_title_del', function(e){
		//删除标题
		e.preventDefault();
		$(this).next().remove();$(this).remove();
	}).on('click', '.J_pages_wrap a', function(e){
		//推送翻页
		e.preventDefault();

		//翻页前内容
		var clone = tabct_push.clone();

		tabct_push.html('<div class="pop_loading"></div>');

		$.post(this.href)
		.done(function(data){
			if(Wind.Util.ajaxTempError(data)) {
				tabct_push.html(clone.html());
				return false;
			}

			tabct_push.html(data);
			designUtil.commonFn();
		});
	}).on('click', 'a.J_data_push', function(e) {
		//推送
		e.preventDefault();
		var role = $(this).data('role');
		//global.js
		Wind.Util.ajaxMaskShow(design_zindex);

		$.post(this.href, function(data) {
			if (data.state == 'success') {
				//更新弹窗列表
				designUtil.updatePopList(null, false);

				if(role == 'pass') {
					//通过
					tabnav_data.children().data('load', false);
					tabct_data.html('<div class="pop_loading"></div>');
				}

			} else if (data.state == 'fail') {
				//global.js
				Wind.Util.ajaxMaskRemove();
				Wind.Util.resultTip({
					error: true,
					msg: data.message,
					zindex : design_zindex
				});
			}
		}, 'json');
	}).on('click', 'a.J_design_data_ajax', function(e){
		//公共ajax更新
		e.preventDefault();
		var noupdate = $(this).data('noupdate');		//是否更新模块列表

		//global.js
		Wind.Util.ajaxMaskShow(design_zindex);

		$.post(this.href, function(data){
			if(data.state == 'success') {
				//更新弹窗列表
				designUtil.updatePopList(null, noupdate ? false : true, false);
			}else if(data.state == 'fail'){
				//global.js
				Wind.Util.ajaxMaskRemove();
				Wind.Util.resultTip({
					error : true,
					msg : data.message,
					zindex : design_zindex
				});
			}
		}, 'json');
	}).on('click', '#J_design_temp_sub', function(e){
		//模块模板保存
		e.preventDefault();
		var $this = $(this),
			temp_name = $('#J_design_temp_name');

		//global.js
		Wind.Util.ajaxMaskShow(design_zindex);

		$.post($this.data('action'), {
			tpl : $('textarea[name=tpl]').val(),
			tplname : $('#J_design_temp_name').val(),
			moduleid : moduleid
		}, function(data){
			//global.js
			Wind.Util.ajaxMaskRemove();

			if(data.state == 'success') {
				temp_name.val('');
				$this.prop('disabled', true).addClass('disabled');
				//global.js
				Wind.Util.resultTip({
					msg : '保存成功',
					follow : $this,
					zindex : design_zindex
				});
			}else if(data.state == 'fail'){
				//global.js
				Wind.Util.resultTip({
					error : true,
					msg : data.message,
					follow : $this,
					zindex : design_zindex
				});
			}
		}, 'json');
	}).on('click', 'div.J_sign_items', function(e){
		//点击模板模块属性 codemirror冲突
		//$('#J_design_temp_tpl').rangeInsert(this.innerHTML);
	}).on('change', '#J_select_model_type', function(e){
		//模块属性 数据模型
		var select_model = $('#J_select_model'),
				data = DESIGN_MODELS[this.value],
				arr = [];
		if(data) {
			for (i = 0, len = data.length; i<len; i++) {
				arr.push('<option value="'+ data[i].model +'">'+ data[i].name +'</option>')
			}
			select_model.html(arr.join('')).show();

			if (select_model.val()) {
				updateProperty(select_model.val());
			}
 		}else{
			select_model.hide().html('');
		}
	}).on('change', '#J_select_model', function(e){
		//更新数据模块
		updateProperty(this.value);
	}).on('click', '#J_module_update', function(e){
		//更新
		e.preventDefault();
		var $this = $(this);
		Wind.Util.ajaxBtnDisable($this);
		$.post($this.data('url'), {
			moduleid : moduleid
		}, function(data){
			Wind.Util.ajaxBtnEnable($this);
			if(data.state == 'success') {
				Wind.Util.ajaxMaskShow(design_zindex);
				designUtil.updatePopList();
			}else if(data.state == 'fail') {
				Wind.Util.resultTip({
					error : true,
					follow : $this,
					zindex : design_zindex,
					msg : data.message
				});
			}
		}, 'json');
	});

	//重新加载数据模块
	function updateProperty(model){
		Wind.Util.ajaxMaskShow(design_zindex);
		try{
			$.post(layout_edit_nav.find('li.current > a').attr('href'), {
				moduleid : moduleid,
				model : model
			}, function(data){
				Wind.Util.ajaxMaskRemove();
				if (Wind.Util.ajaxTempError(data)) {
					return false;
				}

				layout_edit_contents.children(':visible').html(data);
				designUtil.commonFn();
			}, 'html');
		}catch(e){
			$.error(e)
		}
	}

	//返回内容列表
	layout_edit_contents.on('click', '#J_module_data_back', function(e){
		e.preventDefault();
		$('#J_module_data_list').show();
		$('#J_module_data_edit').hide().html('<div class="pop_loading"></div>');
	});

	//模块编辑提交
	layout_edit_contents.on('click', 'button.J_module_sub', function(e){
		e.preventDefault();
		var $this = $(this),
				form = $this.parents('form'),
				role = $this.data('role'),
				action = $this.data('action'),
				update = $this.data('update'),			//更新对象
				apply = form.data('apply');

		form.ajaxSubmit({
			url : action ? action : form.attr('action'),
			dataType : 'json',
			beforeSubmit : function(){
				if(!apply) {
					Wind.Util.ajaxBtnDisable($this);
				}
			},
			success : function(data, statusText, xhr, $form){
				if(apply) {
					Wind.Util.ajaxBtnEnable(form.find('button.J_module_apply'));
				}

				if(data.state == 'success') {
					//提交后不tab内容重载
					layout_edit_nav.find('a').data('load', false);
					layout_edit_contents.children(':not(:visible)').html('<div class="pop_loading"></div>');

					if(update == 'mod'){
						//更新模块列表
						if(apply) {
							updateModuleList(moduleid, mudule_box, false, 'apply');
						}else{
							updateModuleList(moduleid, mudule_box, true);
						}

					}else if(update == 'title'){
						//编辑标题

						var modtit = mudule_box.children('h2');
						if(modtit.length) {
							modtit.replaceWith(data.html);
						}else{
							mudule_box.prepend(data.html);
						}

						if(!apply) {
							Wind.Util.ajaxBtnEnable($this);
							designUtil.popHide('loading');
							Wind.Util.resultTip({
								msg : data.message
							});
						}

					}else if(update == 'style'){
						//编辑样式

						//移除老的style
						mudule_box.find('style').remove();

						//写入style 样式
						mudule_box.prepend('<style type="text/css" class="_tmp">#'+ data.html.styleDomId[0] +'{'+ data.html.styleDomId[1] +'}#'+ data.html.styleDomId[0] +' a{'+ data.html.styleDomIdLink[1] +'}</style>');

						//更新class
						if(data.html.styleDomClass) {
							mudule_box.removeClass(module_class_mapping).addClass(data.html.styleDomClass)
							//mudule_box.attr('class', 'box J_mod_box '+ data.data.styleDomClass);
						}

						if(!apply) {
							Wind.Util.ajaxBtnEnable($this);
							designUtil.popHide('loading');
							Wind.Util.resultTip({
								msg : data.message
							});
						}
					}else{
						if(update == 'pop') {
							//更新弹窗列表
							designUtil.updatePopList($this, false);
						}else if(update == 'all') {
							//更新弹窗&模块列表
							designUtil.updatePopList($this, true, false);
						}
					}

					//应用成功提示
					if(apply) {
						Wind.Util.formBtnTips({
							wrap : $this.parent(),
							msg : data.message
						});
					}
				}else if(data.state == 'fail'){
					//global.js
					Wind.Util.ajaxBtnEnable($this);
					Wind.Util.formBtnTips({
						error : true,
						wrap : $this.parent(),
						msg : data.message
					});
				}

				form.data('apply', false);
			}
		});
	});

	//应用
	layout_edit_contents.on('click', 'button.J_module_apply', function(e){
		e.preventDefault();
		$(this).parents('form').data('apply', true);
		$(this).siblings('.J_module_sub').click();
		Wind.Util.ajaxBtnDisable($(this))
	});


	//右上角菜单
	Wind.Util.hoverToggle({
		elem : $('#J_design_top_arrow'),		//hover元素
		list : $('#J_design_top_list')
	});


	//保存
	var savedata = {}, dialog_html = '';
	savedata['pageid'] = pageid;
	savedata['uri'] = uri;
	savedata['uniqueid'] = uniqueid;
	savedata['compile'] = document.getElementById('J_compile').value;

	//点击保存
	$('#J_design_submit').on('click', function(){
		var $this = $(this);
		$('#J_mod_box').remove();

		//获取提交内容
		var ii = 0;
		for(i=0;i<mod_wrap.length;i++){
			var clone = $(mod_wrap[i]).clone(),
				box = clone.find('div.J_mod_box');
			clone.find('div.J_mod_layout').removeAttr('style');			//外框的style不提交
			clone.find('style').remove();														//拿掉style标签

			if($.browser.msie) {
				//jQuery1.8 sizzlejs部分 会增加 sizset=""属性
				var elem_sizset = clone.find('[sizset]');
				
				if(elem_sizset.length) {
					elem_sizset.removeAttr('sizset');
				}
			}
			
			//替换模块内容
			for(k=0;k<box.length;k++) {
				var _id = $(box[k]).data('id');
				if(_id) {
					if($.browser.msie) {
						var el_module = document.createElement('design');
						el_module.id = 'D_mod_'+ _id;
						el_module.setAttribute('role', 'module');
						$(box[k]).html(el_module);
					}else{
						$(box[k]).html('<design id="D_mod_'+ _id +'" role="module"></design>');
					}
				}
			}

			var html = clone.html();

			if($.browser.msie) {
				//jQuery1.8 sizzlejs部分 ie下会增加 sizcache***="" 随机数属性
				html = html.replace(/sizcache\d+="(?:null|\d|.+)"/g, '');
			}

			savedata['segment['+ mod_wrap[i].id +']'] = html;
			if(html) {
				ii++;
			}
		}

		if(dtype == 'unique') {
			dialog_html = '<div class="pop_cont">是否应用于其他同类页面？</div>\
				<div class="pop_bottom">\
					<button class="btn btn_submit mr10 J_design_check" data-value="nounique" type="button">是</button>\
					<button class="btn J_design_check" data-value="isunique" type="button">否</button>\
				</div>';
		}/*else if(dtype == 'read'){
			dialog_html = '<div class="pop_cont">请先选择应用于以下哪一项</div>\
				<div class="pop_bottom">\
					<button class="btn btn_submit J_design_check" data-value="" type="button">所有阅读页</button>\
					<button class="btn btn_submit J_design_check" data-value="forum" type="button">当前页所属版块</button>\
					<button class="btn btn_submit J_design_check" data-value="read" type="button">当前页</button>\
				</div>';
		}*/else{
			dialog_html = '';
		}


		if(dialog_html){
			Wind.use('dialog', function(){
				Wind.dialog.html(dialog_html, {
					isMask	: true,
					zIndex : design_zindex,
					callback : function(){
						$('button.J_design_check').on('click', function(e){
							e.preventDefault();
							savedata.type = $(this).data('value');
							Wind.dialog.closeAll();
							save($this, savedata);
						});
					}
				});
			})

		}else{
			//delete savedata.uniqueid;
			savedata.type = dtype;
			save($this, savedata);
		}

	});

	//保存方法
	function save(elem, savedata){
		Wind.Util.ajaxMaskShow(design_zindex);
		try{
			$.ajax({
				url : elem.data('action'),
				type : 'post',
				dataType : 'json',
				data : savedata,
				success : function(data){
					//global.js
					Wind.Util.ajaxMaskRemove();

					if(data.state == 'success') {
						if(data.referer) {
							window.onbeforeunload = null;
							location.href = decodeURIComponent(data.referer);
						}
					}else{
						//global.js
						Wind.Util.resultTip({
							error : true,
							msg : data.message
						});
					}
				}
			});
		}catch(e){
			$.error(e);
		}
	}


	//退出
	$('#J_design_quit').on('click', function(e){
		e.preventDefault();
		if(confirm('您确定要退出页面设计状态？确定退出后，已修改的数据将不会保存。')) {
			$.post(this.href, {pageid: pageid, uri : uri}, function(data){
				if(data.state == 'fail') {
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
					return;
				}
				window.onbeforeunload = null;
				location.href = decodeURIComponent(data.referer);
			}, 'json');
		}
 	});

	//退出 模板权限
	$('#J_design_quit_direct').on('click', function(e){
		e.preventDefault();
		$.post(this.href, {pageid: pageid, uri : uri}, function(data){
			window.onbeforeunload = null;
			location.href = data.referer;
		}, 'json');
	});

	//菜单公共方法
	//关闭
	$('a.J_pop_close').on('click', function(e){
		e.preventDefault();
		menu_pop.hide();
	});
	//拖拽
	menu_pop.draggable( { handle : '.J_drag_handle'} );


	//恢复备份
	$('#J_design_restore').on('click', function(e){
		e.preventDefault();
		if(confirm('您确定要恢复为上一个版本的备份结果吗？')) {
			restoreLoop(this.href, 1);
		}
	});

	//循环请求备份信息
	function restoreLoop(url, step){
		try{
			$.post(url, {pageid: pageid, step : step}, function(data){
				if(data.state == 'success') {
					var status = parseInt(data.data[0]);
					if(status < 7) {

						//global.js
						Wind.Util.resultTip({
							msg : data.data[1]
						});

						restoreLoop(url, step+1);
					}else{
						Wind.Util.resultTip({
							msg : '恢复成功！',
							callback : function(){
								window.onbeforeunload = null;
								//global.js
								location.reload();
							}
						});
					}
				}else if(data.state == 'fail'){
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
				}
			}, 'json');
		}catch(e){
			$.error(e);
		}
	}


/*
 * 导出
*/
	var design_export_pop = $('#J_design_export_pop');
	$('#J_design_export').on('click', function(e){
		e.preventDefault();

		Wind.Util.popPos(design_export_pop);
	});
	$('#J_design_export_btn').on('click', function(e){
		e.preventDefault();
		var v = design_export_pop.find('input:radio:checked').val();
		window.open(this.href +'&pageid='+ pageid+'&charset='+v);
	});


/*
 * 导入
*/
	var design_import_pop = $('#J_design_import_pop');
	$('#J_design_import').on('click', function(e){
		e.preventDefault();
		Wind.Util.popPos(design_import_pop);
	});

	//导入提交
	$('#J_design_import_form').ajaxForm({
		dataType : 'json',
		beforeSubmit : function (arr, $form, options) {
			//global.js
			Wind.Util.ajaxBtnDisable($form.find('button:submit'));
		},
		data : {
			pageid : pageid
		},
		success : function (data, statusText, xhr, $form) {
			//global.js
			var btn = $form.find('button:submit')
			Wind.Util.ajaxBtnEnable(btn);

			if(data.state == 'success') {
				Wind.Util.resultTip({
					elem : btn,
					follow : true,
					msg : '导入成功',
					zindex : design_zindex,
					callback : function(){
						window.onbeforeunload = null;
						//global.js
						Wind.Util.reloadPage(window);
					}
				});
			}else if(data.state == 'fail'){
				//global.js
				Wind.Util.resultTip({
					elem : btn,
					follow : true,
					error : true,
					msg : data.message,
					zindex : design_zindex
				});
			}
		}
	});


/*
 * 更新
*/
	$('#J_design_cache').on('click', function(e){
		e.preventDefault();
		Wind.Util.ajaxMaskShow();
		$.post(this.href, {pageid : pageid}, function(data){
			Wind.Util.ajaxMaskRemove();
			if(data.state == 'success') {
				Wind.Util.resultTip({
					msg : '更新成功',
					callback : function(){
						window.onbeforeunload = null;
						//global.js
						Wind.Util.reloadPage(window);
					}
				});
			}else if(data.state == 'fail'){
				//global.js
				Wind.Util.resultTip({
					error : true,
					msg : data.message
				});
			}
		}, 'json');
	});


/*
 * 清空
*/
	$('#J_design_clear').on('click', function(e){
		e.preventDefault();
		if(confirm('您确定要清空页面上的所有模块？清空后将不可恢复！')) {
			Wind.Util.ajaxMaskShow();
			$.post(this.href, {pageid : pageid, design : 1}, function(data){
				Wind.Util.ajaxMaskRemove();
				if(data.state == 'success') {
					mod_wrap.html('');
					location.reload();
				}else if(data.state == 'fail') {
					Wind.Util.resultTip({
						error : true,
						msg : data.message
					});
				}
			}, 'json');
		}
	});

/*
 * 编辑模式锁定轮循请求
*/
	designLockLoop();
	function designLockLoop(){
		try{
			$.post(DESIGN_LOCK, {pageid: pageid}, function(data){
				if(data.state == 'success') {
					setTimeout(function(){
						designLockLoop();
					}, 60000);
				}
			}, 'json');
		}catch(e){
			$.error(e);
		}
	}

/*
 * 关闭小帖士
*/
	var mini_tips = $('#J_mini_tips'),
		tip_cookie = Wind.Util.cookieGet('designMiniTips');
	if(tip_cookie !== 'closed') {
		//cookie不存在则显示
		mini_tips.show();
	}

	$('#J_mini_tips_close').on('click', function(e){
		e.preventDefault();
		mini_tips.hide();
		//存入cookie
		Wind.Util.cookieSet('designMiniTips','closed','365',document.domain);
	});


})();