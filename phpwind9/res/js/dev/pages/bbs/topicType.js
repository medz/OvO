 /**
 * PHPWind util Library 
 * @Copyright Copyright 2012, phpwind.com
 * @Descript: 主题分类弹窗组件
 * @Author	: wengqianshan@me.com
 * @Depend	: core.js、jquery.js(1.7 or later)
 * $Id: 
 */
/**
 * 主题分类联动js
 * fid	版块id
 * url	接口
 * callback	回调
 */
;(function($, window){
	var ShowTopicPop = function(options){
		this.fid = options.fid;//版块ID，必填项
		this.url = options.url;
		this.callback = options.callback;//回调，会传递已选中的分类数据
		this.tmpl = '<div class="pop_cont">\
						<div class="tac" id="J_topictype_pop">\
						</div>\
					</div>\
					<div class="pop_bottom">\
						<button type="button" class="btn btn_submit" id="J_btn_topictype_ok">提交</button>\
						<button type="button" class="btn" id="J_btn_topictype_cancel">取消</button>\
					</div>';	
		this.wrap = null;//弹窗的内容区域
		this.data = null;//分类数据
	};
	ShowTopicPop.prototype = {
		init: function(){
			var _this = this;
			//弹窗
			Wind.use('dialog', function(){
				_this.showDialog();
				_this.wrap = $("#J_topictype_pop");
				//加载数据
				_this.getData();
				//select绑定chang事件
				_this.wrap.on('change', 'select', function(){
					//select的索引值
					var sIndex = $(this).data('index');
					var oIndex = $(this).find("option:selected").data('index');
					//如果当前没有选择，就删除后面的select
					if(oIndex === undefined){
						$(this).nextAll('select').remove();
						return;
					}
					var selects = _this.wrap.find('select');
					var len = selects.length;
					var dIndex = selects.index(this);
					//是否要新建select，只有当select的个数小于等于当前的索引值+1的时候才需要新创建
					var ifCreate = len <= (dIndex + 1);
					
					//定位当前数据
					//console.log($(_this).data("data")[oIndex])
					if($(this).data("data") && $(this).data("data")[oIndex]){
						var currData = $(this).data("data")[oIndex].items;
						if(currData === undefined || currData.length < 1){
							$(this).nextAll('select').remove();
							return;
						}
						_this.showNext(currData, sIndex, ifCreate);
					}
				});
				//绑定确定按钮点击事件，把选中的数据传会给调用者
				_this.wrap.parent().parent().on('click', '#J_btn_topictype_ok', function(e){
					var selects = _this.wrap.find('select');
					var data = [];
					var need = false;//用来标记是否选择完毕
					selects.each(function(){
						var option = $(this).find("option:selected");
						if(option.text() === "请选择分类" && option.val() == 0){
							need = true;
						}
						data.push({
							name: option.text(),
							val: option.val()
						})
					})
					if(need === true){
						alert('请选择分类');
					}else{
						_this.callback(data);
						_this.hideDialog();
					}
				});
				_this.wrap.parent().parent().on('click', '#J_btn_topictype_cancel', function(e){
					_this.hideDialog();
				});
			});
		},
		//显示弹窗
		showDialog: function(){
			var _this = this;
			Wind.dialog.html(_this.tmpl, {
				type: 'html',
				width: 280,
				title: '请选择主题分类'
			});
		},
		//关闭弹窗
		hideDialog: function(){
			Wind.dialog.closeAll();
		},
		//获取数据
		getData: function(){
			var _this = this;
			$.post(this.url, {fid: this.fid}, function(data){
				var state = data.state;
				if(state === 'success'){
					var data = data.data;
					_this.data = data;
					_this.render(_this.data);
				}
			}, 'json');
		},
		//增加一个select
		render: function(data, index){
			this.wrap.append(this.createSelect(data, index));
		},
		//根据数据和索引创建select节点
		createSelect: function(data, index){
			var index = index || 0;
			var select = $('<select data-index="'+index+'" style="margin:5px;"></select>');
			var select_0 = select[0];
			select.data('data', data);
			//只有第一级才显示请选择分类 吐槽:无语!!如果需要支持无限级，需要去掉这个判断
			if(index === 0){
				select_0.add(new Option('请选择分类', 0));
			}
			$(data).each(function(key){
				var option = new Option(this.title, this.val);
				//给option设置索引，绑定事件时用
				$(option).data('index', key);
				if(this.selected){
					$(option).attr("selected", true);
				}
				select_0.add(option);
			});
			return select;
		},
		//联动交互
		showNext: function(data, index, ifCreate){
			if(!this.data){
				return false;
			}
			//如果不是新建select，就把后面的select全部删除
			if(!ifCreate){
				var select = this.wrap.find('select').eq(index);
				select.nextAll('select').remove();
			}
			this.render(data, index + 1)
			
		}
	};
	window.ShowTopicPop = ShowTopicPop;
})(jQuery, window);	