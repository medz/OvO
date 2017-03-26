/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前后台-地区组件
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later), jquery.form, REGION_JSON页面定义，GV.REGION_CONFIG head全局变量
 * $Id$
 */
;(function ( $, window, document, undefined ) {
	var pluginName = 'region',
		defaults = {
			type : 'region',		//类型(地区或学校) 默认地区
			regioncancl : false
		},
		region_pl = $('<div class="pop_region_list">\
					<ul id="J_region_pop_province" class="cc"></ul>\
					<div class="hr"></div>\
					<ul id="J_region_pop_city" class="cc">\
						<li><span>请选择</span></li>\
					</ul>\
					<div class="hr"></div>\
					<ul id="J_region_pop_district" class="cc">\
						<li><span>请选择</span></li>\
					</ul>\
					<div id="J_school_wrap" style="display:none;"></div>\
			</div>'),
		pop_temp = '<div class="core_pop_wrap" id="J_region_pop">\
				<div class="core_pop">\
					<div style="width:600px;">\
						<div class="pop_top">\
							<a href="#" class="pop_close J_region_close">关闭</a>\
							<strong>选择地区</strong>\
						</div>\
						<div class="pop_cont">\
							<div id="J_region_pl" class="pop_loading"></div>\
						</div>\
						<div class="pop_bottom tac">\
							<button type="submit" class="btn btn_submit mr10 disabled J_region_pop_ok" disabled="disabled">确定</button><button type="button" class="btn J_region_close">关闭</button>\
						</div>\
					</div>\
				</div>\
			</div>';

	function Plugin(element, options) {
		this.element = element;
		this.options = $.extend({}, defaults, options);
		this.init();
	}

	Plugin.prototype = {
		init : function() {
			var _this = this,
				options = this.options,
				elem = this.element,
				type = options.type,
				onevent = (type == 'region' ? 'click' : 'focus');

			//修改模板
			if(type == 'school') {
				region_pl.find('#J_school_wrap').html(setSchool.school_temp).show();
			}else{
				region_pl.find('#J_school_wrap').html('').hide();
			}

			elem.on(onevent, function (e) {
				e.preventDefault();
				var wrap = elem.parents('.J_region_set');

				_this.regionInit(elem.data('pid'), elem.data('cid'), elem.data('did'), elem.data('rank'), wrap, type);

				if(GV.REGION_CONFIG.load) {
					//地区数据已获取后再次选择学校
					setSchool.init(elem);
				}
			});

			//取消
			$('a.J_region_cancl').on('click', function(e){
				e.preventDefault();
				var wrap = $(this).parents('.J_region_set');
				wrap.find('.J_province, .J_city, .J_district').text('');
				wrap.find('input.J_areaid').val('');
				elem.removeData('pid cid did');
				$(this).hide();
			});

		},
		regionInit : function(pid, cid, did, rank, wrap, type){
			var _this = this,
				region_pop = $('#J_region_pop'),
				zindex = _this.options.zindex;

			if(region_pop.length) {
				//隐藏弹窗显示
				region_pop.show();

				_this.hideRank(rank);
				_this.getChild(region_pop, rank, type);

				if(pid) {
					$('#J_province_'+ pid).addClass('current').siblings().removeClass('current');					//弹窗选中当前省
						_this.getCity(pid, cid, did);									//弹窗选中当前市
				}else{
					//重置
					$('#J_region_pop_province > li').removeClass('current');
					$('#J_region_pop_city, #J_region_pop_district').html('<li><span>请选择</span></li>');
					_this.btnDisable();
				}

				//global.js
				Wind.Util.popPos(region_pop);

				//确定
				_this.subOk(wrap, region_pop);
			}else{
				//组装添加弹窗
				$('body').append(pop_temp);

				var region_pop = $('#J_region_pop');
				if(zindex) {
					region_pop.css('zIndex', zindex);
				}

				if ($.browser.msie && $.browser.version < 7) {
					Wind.use('bgiframe',function() {
						region_pop.bgiframe();
					});
				}

				//global.js
				Wind.Util.popPos(region_pop);

				//获取地区数据
				$.ajax({
					url : GV.URL.REGION,
					type : 'post',
					dataType : 'json',
					success : function(data){
						if(data) {
							GV.REGION_CONFIG = data;

							$('#J_region_pl').replaceWith(region_pl);
							
							var region_pop_province = $('#J_region_pop_province'),
									region_pop_city = $('#J_region_pop_city');

							//写入省的html
							region_pop_province.html(_this.showProvince());

							_this.hideRank(rank);

							if(pid) {
								//有默认值
								$('#J_province_'+ pid).addClass('current').siblings().removeClass('current');
								_this.getCity(pid, cid, did);
							}
								
							//显示
							region_pop.show(0, function(){
								//引入弹窗拖动组件
								Wind.use('draggable',function() {
									region_pop.draggable( { handle : '.pop_top'} );
								});
							});

							_this.getChild(region_pop, rank, type);
							_this.regionClose(region_pop);

							//确定
							_this.subOk(wrap, region_pop);

							//global.js
							Wind.Util.popPos(region_pop);

							//调用学校方法
							if(type == 'school') {
								GV.REGION_CONFIG.load = true;
								setSchool.init($('input.J_plugin_school:focus'));
							}
						}
					},
					error : function(){
						region_pop.remove();
						Wind.Util.resultTip({
							error : true,
							msg : '数据请求失败！',
							follow : _this.element
						});
					}
				});

			}
						
		},
		showProvince : function(){
			//显示省
			var province_arr = [];

			//循环省数据
			$.each(GV.REGION_CONFIG, function(i, o){
				province_arr.push('<li id="J_province_'+ i +'" data-id="'+ i +'" data-child="city" data-role="province"><a href="#" class="J_item">'+ o.name +'</a></li>');
			});
					
			return province_arr.join('');
		},
		getCity : function(pid, cid, did){
			//获取城市
			var _this = this,
				pop_city = $('#J_region_pop_city'),
				arr= [],
				data = GV.REGION_CONFIG[pid]['items'];

			//重置区县
			$('#J_region_pop_district').html('<li><span>请选择</span></li>');

			if(!data) {
				//没有城市数据
				pop_city.html('<li><span>请选择</span></li><li><em class="gray">地区信息不完整，请联系管理员</em></li>');
				return;
			}

			$.each(data, function(i, o){
				arr.push('<li id="J_city_'+ i +'" data-id="'+ i +'" data-child="district" data-role="city"><a href="#" class="J_item">'+ o.name +'</a></li>');
			});
					
			//写入城市
			pop_city.html('<li class="current" data-id=""><a href="#" class="J_item">请选择</a></li>'+ arr.join(''));
					
			if(cid){
				//已设城市
				$('#J_city_'+ cid).addClass('current').siblings().removeClass('current');
				_this.getDistrict(data[cid]['items'], did);
			}

		},
		getDistrict : function (data, did){
			//获取区县
			var arr= [],
				pop_district = $('#J_region_pop_district');

			if(!data) {
				pop_district.html('<li><span>请选择</span></li><li><em class="gray">地区信息不完整，请联系管理员</em></li>');
				return;
			}

			$.each(data, function(i, o){
				arr.push('<li id="J_district_'+ i +'" data-id="'+ i +'" data-child="" data-role="district"><a href="#" class="J_item" data-role="district">'+ o +'</a></li>');
			});
			pop_district.html('<li class="current" data-id=""><a href="#" class="J_item">请选择</a></li>'+ arr.join(''));
					
			if(did){
				//高亮当前区县
				$('#J_district_'+ did).addClass('current').siblings().removeClass('current');
			}

		},
		getChild : function (wrap, rank, type){
			var _this = this;
			//弹窗点击获取下级数据
			wrap.on('click', 'a.J_item', function(e){
				e.preventDefault();
				var $this = $(this),
						li = $this.parent(),
						ul = li.parents('ul'),
						id = li.data('id'),
						child = li.data('child');
				
				li.addClass('current').siblings('li.current').removeClass('current');
						
				if($this.data('role') == 'district') {
					if(type !== 'school') {
						//学校 提交不可用
						_this.btnRemoveDisable();
					}
					return;
				}

				if(rank == 'province' && type == 'region') {
					//直到省
					_this.btnRemoveDisable();
					return;
				}

				_this.btnDisable();



				if(!id) {
					//点击“请选择”
					ul.nextAll('ul').html('<li><span>请选择</span></li>');
				}else{
					//点击省
					var data, arr = [];
							
					if(child == 'city') {
						_this.getCity(id);
					}else if(child == 'district'){
						data = GV.REGION_CONFIG[$('#J_region_pop_province > li.current').data('id')]['items'][id]['items'];
						_this.getDistrict(data);
					}

				}
				
			});
		},
		btnDisable : function (){
			//确认不可点
			$('button.J_region_pop_ok').addClass('disabled').attr('disabled', 'disabled');
		},
		btnRemoveDisable : function (){
			//确认可点
			$('button.J_region_pop_ok').removeClass('disabled').removeAttr('disabled');
		},
		subOk : function (wrap, pop){
			//确认
			var _this = this;
			if(wrap.length) {
				var elem = this.element;
				var region_change = wrap.find('a.J_region_change');
					$('button.J_region_pop_ok').off('click').on('click', function(e){
						e.preventDefault();
						var current_lis = pop.find('ul > li.current');
						
						current_lis.each(function(i, o){
							wrap.find('.J_'+ $(this).data('role')).text($(this).text());
						});

						elem.data({
							pid : $(current_lis[0]).data('id'),
							cid : $(current_lis[1]).data('id'),
							did : $(current_lis[2]).data('id')
						});

						wrap.find('input.J_areaid').val($('#J_region_pop_district > li.current').data('id'));
						pop.hide();

						var regioncancl = _this.options.regioncancl;
						if(regioncancl) {
							//显示取消
							wrap.find('a.J_region_cancl').show();
						}
					});
			}
		},
		regionClose : function (wrap){
			//关闭
			wrap.on('click', '.J_region_close', function(e){
				e.preventDefault();
				wrap.hide();
			});
		},
		hideRank : function (rank){
			//隐藏省市级别
			var region_pop_city = $('#J_region_pop_city'),
					region_pop_district = $('#J_region_pop_district');

			if(rank == 'province') {
				region_pop_city.hide().next().hide();
				region_pop_district.hide();

				if(this.options.type == 'region') {
					$('#J_region_pop_province').next().hide();
				}
			}else{
				region_pop_city.show().next().show();
				region_pop_district.show();
			}
		}
	};

	$.fn[pluginName] = function(options) {
		return this.each(function() {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName, new Plugin($(this), options));
			}
		});
	}

})( jQuery, window ,document);