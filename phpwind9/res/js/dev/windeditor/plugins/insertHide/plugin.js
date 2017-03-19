/*
 * PHPWind WindEditor Plugin
 * @Copyright 	: Copyright 2011, phpwind.com
 * @Descript	: 贴子内容隐藏插件
 * @Author		: chaoren1641@gmail.com
 * @Depend		: jquery.js(1.7 or later)
 * $Id: windeditor.js 4472 2012-02-19 10:41:01Z chris.chencq $			:
 */
;(function ( $, window, undefined ) {

	var WindEditor = window.WindEditor;
	var credit = EDIT_CONFIG.enhide.credit;
	var creditSelect = '';
	for(i in credit) {
		creditSelect += '<option value="'+ i +'">'+ credit[i] +'</option>';
	}
	var pluginName = 'insertHide',
		dialog = $('<div class="edit_menu" style="display:none;">\
					<div class="edit_menu_hide">\
						<div class="edit_menu_top"><a href="" class="edit_menu_close">关闭</a><strong>插入隐藏内容</strong></div>\
						<div class="edit_menu_cont">\
							<ul>\
								<li><label><input name="hide_type" type="radio" value="1" checked="checked">回复才可见</label></li>\
								<li><input name="hide_type" type="radio" value="2">\
								<select class="mr5 J_unit">'+ creditSelect +'</select>\
								用户高于<input type="number" class="input length_1 mr5 J_price">时才显示</li>\
							</ul>\
							<textarea></textarea>\
						</div>\
						<div class="edit_menu_bot">\
							<button type="button" class="edit_menu_btn">确定</button><button type="button" class="edit_btn_cancel">取消</button>\
						</div>\
					</div>\
				</div>');

	WindEditor.plugin(pluginName,function() {
		var _self = this;
		var editorDoc = _self.editorDoc = _self.iframe[0].contentWindow.document,
		editorToolbar = _self.toolbar,
		//toolbar中的icon容器
		icon_ul = editorToolbar.find('ul');

		//自定义插入位置,插到insertBlockquote后面
		var plugin_icon = $('<div class="wind_icon" data-control="'+ pluginName +'" unselectable="on"><span unselectable="on" class="'+ pluginName +'" title="插入隐藏内容"></span></div>').appendTo( _self.pluginsContainer );
		plugin_icon.on('click',function() {
			if($(this).hasClass('disabled')) {
				return;
			}
			//如果有选取内容，则不弹窗
			var node	= _self.getRangeNode('div.content_hidden'),
				html = _self.getRangeHTML();
			if(node && node.length) {
				node.find('h5').remove();
				node.replaceWith(node.html());
			}else {
				if(!$.contains(document.body,dialog[0]) ) {
					dialog.appendTo( document.body );
				}
				if(html && $.trim(html) !== "<P></P>") {
					dialog.find('textarea').val(html).hide();
				}else {
					dialog.find('textarea').val('').show();
				}
				_self.showDialog(dialog);
			}
		});

		//弹窗的关闭事件
		dialog.find('a.edit_menu_close,button.edit_btn_cancel').on('click',function(e) {
			e.preventDefault();
			_self.hideDialog();
		});

		//点击插入
		var head = editorDoc.head || editorDoc.getElementsByTagName( "head" )[0] || editorDoc.documentElement;
		var style = "<style>\
			.content_hidden {border:1px dashed #95c376;padding:10px 40px;margin:5px 0;background:#f8fff3;}.content_hidden h5 {font-size:12px;color:#669933;margin-bottom:5px;}</style>";
		$(head).append(style);

		dialog.find('.edit_menu_btn').on('click',function(e) {
			e.preventDefault();
			var textarea = dialog.find('textarea');
			var type = $('input[name=hide_type]:checked').val();
			if(textarea.val() === '') {
				alert('请输入要隐藏的帖子内容');return;
			}
			//script xss
			var snap = $('<div />').text(textarea.val());
			var html;
			if(type == '1') {
				html = '<div class="content_hidden"><h5>回复才可见的内容</h5>'+ snap.text() +'</div>';
			}else {
				var price = dialog.find('input.J_price').val() || 0;
				var numPatt = new RegExp('^[1-9][0-9]*$');
				if(!numPatt.test(price)){
					price = 0;
				}
				var unit = dialog.find('.J_unit option:selected').val();
				html = '<div class="content_hidden" data-price="'+ (price || 0) +'" data-unit="'+ unit +'"><h5>'+ credit[unit] +'大于等于'+ price +'时才显示的内容</h5>'+ snap.text() +'</div>';
			}
			_self.insertHTML(html).hideDialog();
		});


		//切换成可见即所得模式时变成html
		function wysiwyg() {
			var postReg = /\[post]([\s\S]*?)\[\/post\]/ig;
			var hideReg = /\[hide=(\d+)\,(\w+)\s*\]([\s\S]*?)\[\/hide\]/ig;
			var html = $(editorDoc.body).html();
			html = html.replace(postReg,function(all,$1) {
				return '<div class="content_hidden"><h5>回复才可见的内容</h5>'+ $1 +'</div>';
			})
			html = html.replace(hideReg,function(all, $1, $2,$3) {
				return '<div class="content_hidden" data-price="'+ $1 +'" data-unit="'+ $2 +'" ><h5>'+ credit[$2] +'大于等于'+ $1 +'时才显示的内容</h5>'+ $3 +'</div>';
			});
			$(editorDoc.body).html(html);
		}

		//加载插件时把ubb转换成可见即所得
		$(_self).on('ready',function() {
			wysiwyg();
		});

		$(_self).on('afterSetContent',function(event,viewMode) {
			wysiwyg();
		});

		$(_self).on('beforeGetContent',function() {
			$(editorDoc.body).find('div.content_hidden').each(function() {
				$(this).find('h5').remove();
				var price = $(this).data('price');
				var unit = $(this).data('unit');
				if(price && unit) {
					$(this).replaceWith('[hide='+ price +','+ unit +']'+ this.innerHTML +'[/hide]');
				}else {
					$(this).replaceWith('[post]'+ this.innerHTML +'[/post]');
				}

			});
		});

		//控件栏按钮的控制
    	$(_self.editorDoc.body).on('mousedown',function(e) {
    		if( $(e.target).closest('div.content_hidden').length ) {
    			plugin_icon.removeClass('disabled').addClass('activate');
    		}else {
    			_self.enableToolbar();
    			plugin_icon.removeClass('activate');
    		}
    	});
	});


})( jQuery, window);
