/*
 * PHPWind WindEditor Plugin
 * @Copyright 	: Copyright 2011, phpwind.com
 * @Descript	: 贴子内容出售插件
 * @Author		: wengqianshan@me.com
 * @Depend		: jquery.js(1.7 or later)
 * $Id: windeditor.js 4472 2012-02-19 10:41:01Z chris.chencq $			:
 */
;(function ( $, window, undefined ) {
	var SellManage = function(editor){
		this.editor = editor;
		this.credit = window.EDIT_CONFIG.sell.credit;//出售配置信息
		this.creditSelect = '';//option容器
		this.maxSell = EDIT_CONFIG.sell.price || 0;//可设置最大值
		this.maxIncome = EDIT_CONFIG.sell.income || 0;//最大获益值
		this.editAction = false;//是否编辑状态
		this.editNode = null;//当前编辑的节点 .content_sell
		this.sellData = null;//是否已设置出售，设置后{price: 1, unit: "1"} 即content_sell的 data-price 和data-unit
		for(i in this.credit) {
			this.creditSelect += '<option value="'+ i +'">'+ this.credit[i] +'</option>';
		}
		this.dialog = $('<div class="edit_menu" style="display:none;">\
						<div class="edit_menu_hide">\
							<div class="edit_menu_top"><a href="" class="edit_menu_close">关闭</a><strong>插入出售内容</strong></div>\
							<div class="edit_menu_cont">\
								<ul>\
									<li>允许设置最高售价'+ this.maxSell +'，限制最高收入'+ this.maxIncome +'</li>\
									<li>售价：<input name="price" type="number" size="2" id="J_sell_price" class="input length_1 mr5 J_price" max="'+ this.maxSell +'" min="0">\
									<select class="mr5 J_unit">'+ this.creditSelect +'</select>\
									<span id="B_sell_bm"></span></li>\
								</ul>\
								<textarea></textarea>\
							</div>\
							<div class="edit_menu_bot">\
								<button type="button" class="edit_menu_btn">确定</button><button type="button" class="edit_btn_cancel">取消</button>\
							</div>\
						</div>\
					</div>');
	};
	SellManage.prototype = {
		init: function(){
			var _self = this;
			//弹窗的关闭事件
			this.dialog.find('a.edit_menu_close,button.edit_btn_cancel').on('click',function(e) {
				e.preventDefault();
				_self.editor.hideDialog();
			});
			//插入内容
			this.dialog.find('.edit_menu_btn').on('mousedown',function(e) {
				e.preventDefault();
				var price = $('#J_sell_price').val();
				var textarea = _self.dialog.find('textarea');
				var unit = _self.dialog.find('.J_unit option:selected').val();
				//var text = $('<div />').text(textarea.val()).html(); ////script xss
				var text = $('<div />').text(textarea.val()).text(); ////script xss
				var name = _self.credit[unit];
				if(isNaN(price) || parseInt(price) < 0 || price == '') {
					alert('请输入正确的数字');
					$('#J_sell_price').focus();
					return;
					//price = 0;
				}
				if(parseInt(price) > _self.maxSell) {
					alert('最高售价为'+ _self.maxSell + ',不能大于' + _self.maxSell + '哦！');
					$('#J_sell_price').focus();
					return;
				}
				if(textarea.is(':visible') && text === '') {
					alert('请输入要出售的帖子内容');return;
				}
				//保存价格/类型
				_self.sellData = {
					price: price,
					unit: unit
				};
				var node = _self.renderSell({
					price: price,
					unit: unit,
					text: text
				});
				if(_self.editAction === true){
					_self.editSell(_self.editNode, node);
					_self.editor.hideDialog();
					_self.editAction = false;
					_self.editNode = null;
				}else{
					_self.editor.insertHTML(node).hideDialog();
				}
				//更新price
				var body = _self.editor.editorDoc.body;
				$(".content_sell", body).attr("data-price", price);//no data('', '')
				$(".J_price_info", body).html(price + name);
			});
			//=====
		},
		// 传递进来的数据(可为空)；是否显示textarea, 1显示(默认),0不显示;
		showPanel: function(data, isShowTextarea){
			if(!$.contains(document.body, this.dialog[0]) ) {
				this.dialog.appendTo( document.body );
			}
			var textarea = this.dialog.find('textarea');
			var j_price  = this.dialog.find('.J_price');
			var j_unit = this.dialog.find('.J_unit');
			if(data){
				var text = data.text || "";
				var price = data.price || 0;
				var unit = data.unit || 0;
				//设置初始值
				textarea.val(text);
				j_price.val(price);
				j_unit.find("option[value="+unit+"]").attr("selected", "selected");
			}else{
				textarea.val('');
			}
			//是否显示textarea，默认显示
			if(isShowTextarea === 0){
				textarea.hide();
			}else{
				textarea.show();
			}
			this.editor.showDialog(this.dialog);
		},
		//data包含price、unit、text,生成对象,返回DOM对象
		renderSell: function(data){
			if(data !== null){
				var price = data.price || 0,
					unit = data.unit || 0,
					text = data.text || '',
					name = this.credit[unit],
					content = '<div class="content_sell" data-price="'+ price +'" data-unit="'+ unit +'"><a class="icon_delete J_sell_delete" href="#" title="取消内容出售"></a><a class="icon_edit J_sell_edit" href="#" title="编辑出售内容"></a><h6>本帖出售的内容 <span class="content_sell_price_info">(售价<span class="J_price_info">'+ price + name +'</span>)</span></h6>'+ text +'</div>';
				return content;
			}
		},
		editSell: function($old, $new){
			$old.replaceWith($new);
		},
		//获取一段出售内容的纯文本
		getText: function($ele){
			var cnode = $ele.clone();
			cnode.find("h6, .J_sell_delete, .J_sell_edit").remove();
			var text = cnode.text();
			return text || '';
		}
	};



	if(!window.EDIT_CONFIG) {
		$.error('EDIT_CONFIG没有定义，附件上传需要提供配置对象');
		return;
	}
	var WindEditor = window.WindEditor;
	var pluginName = 'insertSell';

	WindEditor.plugin(pluginName,function() {
		var _self = this;
		var sellManage = new SellManage(_self);
			sellManage.init();
		//sellManage.showPanel({price:3, unit: 1}, 0);

		var editorDoc = _self.editorDoc = _self.iframe[0].contentWindow.document,
		editorToolbar = _self.toolbar,
		//toolbar中的icon容器
		icon_ul = editorToolbar.find('ul');
		//自定义插入位置,插到insertBlockquote后面
		var plugin_icon = $('<div class="wind_icon" data-control="'+ pluginName +'" unselectable="on"><span unselectable="on" class="'+ pluginName +'" title="插入出售内容"></span></div>').appendTo( _self.pluginsContainer );
		plugin_icon.on('mousedown',function(e) {
			e.preventDefault();
			if($(this).hasClass('disabled')) {
				return;
			}
			//编辑帖子时读取默认值
			var elems = $(editorDoc).find("body").find(".content_sell");//+body解决IEbug
			if(elems.length > 0 && !sellManage.sellData) {
				var elem_0 = elems.eq(0);
				sellManage.sellData = {
					price: elem_0.attr("data-price"),
					unit: elem_0.attr("data-unit")
				};
			}
			//如果有选取内容，则不弹窗
			var node	= _self.getRangeNode('div.content_sell'),
				html = _self.getRangeHTML();
			if(node && node.length) {
				//TODO 考虑这里设sellManage.editAction = true,用来注明是编辑行为
				sellManage.editAction = true;
				sellManage.editNode = node;
				var price = sellManage.sellData ? sellManage.sellData.price : node.data('price');
				var unit = sellManage.sellData ? sellManage.sellData.unit : node.data('unit');
				var text = sellManage.getText(node);
				sellManage.showPanel({
					price: price,
					unit: unit,
					text: text
				});
			}else {
				if(html && $.trim(html) !== '<P>&nbsp;</P>'){
					if(sellManage.sellData === null){
						sellManage.showPanel({
							text: html
						}, 0);
					}else{
						var node = sellManage.renderSell({
							price: sellManage.sellData.price,
							unit: sellManage.sellData.unit,
							text: html
						});
						_self.insertHTML(node);
					}
				}else{
					if(sellManage.sellData){
						sellManage.showPanel(sellManage.sellData);
					}else{
						sellManage.showPanel();
					}
				}
			}
		});
		//UBB转换
		var credit = window.EDIT_CONFIG.sell.credit;//出售配置信息
		function wysiwyg() {
			var reg = /\[sell=(\d+)\,(\w+)\s*\]([\s\S]*?)\[\/sell\]/ig;
				html = $(editorDoc.body).html();
			html = html.replace(reg,function(all, $1, $2,$3) {
				var price_info = $1 + credit[$2];
				return '<div data-price="'+ $1 +'" data-unit="'+ $2 +'" class="content_sell"><a class="icon_delete J_sell_delete" href="#" title="取消内容出售"></a><a class="icon_edit J_sell_edit" href="#" title="编辑出售内容"></a><h6>本帖出售的内容 <span class="content_sell_price_info">(售价<span class="J_price_info">'+ price_info +'</span>)</span></h6>'+ $3 +'</div>';
			});
			$(editorDoc.body).html(html);
		}

		//加载插件时把ubb转换成可见即所得
		$(_self).on('ready',function() {
			wysiwyg();
		});

		//切换成可见即所得模式时变成html
		$(_self).on('afterSetContent',function(event,html) {
			wysiwyg();
		});

		$(_self).on('beforeGetContent',function() {
			$(editorDoc.body).find('div.content_sell').each(function() {
				$(this).find('h6, .J_sell_edit, .J_sell_delete').remove();
				var price = $(this).data('price');
				var unit = $(this).data('unit');
				$(this).replaceWith('[sell='+ price +','+ unit +']'+ this.innerHTML +'[/sell]');
			});
		});

		//控件栏按钮的控制
    	$(_self.editorDoc.body).on('mousedown',function(e) {
    		if( $(e.target).closest('div.content_sell').length ) {
    			plugin_icon.removeClass('disabled').addClass('activate');
    		}else {
    			_self.enableToolbar();
    			plugin_icon.removeClass('activate');
    		}
    	});
    	//插入出售帖的编辑和删除功能
    	$(_self.editorDoc.body).on('mouseenter.' + pluginName, '.content_sell', function(){
    		$(this).addClass("content_sell_cur");
    	});
    	$(_self.editorDoc.body).on('mouseleave.' + pluginName, '.content_sell', function(){
    		$(this).removeClass("content_sell_cur");
    	});
    	//编辑出售
    	$(_self.editorDoc.body).on('mousedown', '.J_sell_edit', function(e){
    		e.preventDefault();
    		var target = $(e.target);
    		var node = target.closest('.content_sell');
    		sellManage.editAction = true;
    		sellManage.editNode = node;

    		var price = sellManage.sellData ? sellManage.sellData.price : node.data('price');
    		var unit = sellManage.sellData ? sellManage.sellData.unit : node.data('unit');
    		var text = sellManage.getText(node);
    		sellManage.showPanel({
    			price: price,
    			unit: unit,
    			text: text
    		});
    	});
    	//取消出售
    	$(_self.editorDoc.body).on('mousedown', '.J_sell_delete', function(e){
    		e.preventDefault();
    		var target = $(e.target);
    		var wrap = target.closest('.content_sell');
    		var text = sellManage.getText(wrap);
    		Wind.dialog.confirm("取消出售该内容？", function(){
    			wrap.replaceWith(text);
    		})
    	});
	});


})( jQuery, window);
