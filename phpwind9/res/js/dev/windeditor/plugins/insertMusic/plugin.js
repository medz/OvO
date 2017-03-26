/*
 * PHPWind WindEditor Plugin
 * @Copyright 	: Copyright 2011, phpwind.com
 * @Descript	: 插入音乐插件
 * @Author		: chaoren1641@gmail.com
 * @Depend		: jquery.js(1.7 or later)
 * $Id: windeditor.js 4472 2012-02-19 10:41:01Z chris.chencq $			:
 */
;(function ( $, window, undefined ) {

	var WindEditor = window.WindEditor;

	var pluginName = 'insertMusic',
		dialog = $('\
			<div class="edit_menu">\
				<div class="edit_menu_music">\
					<div class="edit_menu_top"><a href="" class="edit_menu_close">关闭</a><strong>插入音乐</strong></div>\
					<div class="edit_menu_cont">\
						<dl class="cc">\
							<dt>地址：</dt>\
							<dd><input type="text" class="input length_5" id="J_input_net_music"></dd>\
						</dl>\
						<!--dl class="cc">\
							<dt>设置：</dt>\
							<dd><label><input type="checkbox">自动播放</label></dd>\
						</dl-->\
					</div>\
					<div class="edit_menu_bot">\
						<button type="button" class="edit_menu_btn">确定</button><button class="edit_btn_cancel" type="button">取消</button>\
					</div>\
				</div>\
			</div>');

	WindEditor.plugin(pluginName,function() {
		var _self = this;
		var editorDoc = _self.editorDoc = _self.iframe[0].contentWindow.document,
			plugin_icon = $('<div class="wind_icon" data-control="'+ pluginName +'"><span class="'+ pluginName +'" title="插入音乐"></span></div>').appendTo(  _self.pluginsContainer  );
			plugin_icon.on('click',function() {
				if($(this).hasClass('disabled')) {
					return;
				}
				if(!$.contains(document.body,dialog[0]) ) {
					dialog.appendTo( document.body );
				}
				_self.showDialog(dialog);
			});

			//弹窗的关闭事件
			dialog.find('a.edit_menu_close,button.edit_btn_cancel').on('click',function(e) {
				e.preventDefault();
				_self.hideDialog();
			});

			var img_path = _self.options.editor_path + 'themes/' + _self.options.theme + '/';

			//插入网络音乐媒体
			dialog.find('.edit_menu_btn').on('click',function(e) {
				e.preventDefault();
				var url = $('#J_input_net_music').val();
				if( url.indexOf('http')!== 0 ) {
					alert('路径格式不正确，请重新输入');
					return;
				}
				//var is_autoplay = + dialog.find(':checkbox').prop('checked');
				var is_autoplay = 0;

				//_self.insertHTML('[mp3='+ is_autoplay +'][/mp3]').hideDialog();
				_self.insertHTML('<img class="j_editor_music_content" width="300" height="40" style="border:1px dashed #ccc;display:block;background:#fffeee url('+ img_path +'music_48.png) center center no-repeat;" src="'+ img_path +'blank.gif" data-url="'+ url +'" data-auto="'+ is_autoplay +'"/>').hideDialog();
			});

			function wysiwyg() {
				var reg = /\[mp3=(\d)\]([\s\S]*?)\[\/mp3\]/ig;
				var	html = $(editorDoc.body).html();
				html = html.replace(reg,function(all, $1, $2) {
					return '<img class="j_editor_music_content" width="300" height="40" style="border:1px dashed #ccc;display:block;background:#fffeee url('+ img_path +'music_48.png) center center no-repeat;" src="'+ img_path +'blank.gif" data-url="'+ $2 +'" data-auto="'+ $1 +'"/>';
				});
				$(editorDoc.body).html(html);
			}

			//加载插件时把ubb转换成可见即所得
			$(_self).on('ready',function() {
    			wysiwyg();
    		});

			$(_self).on('afterSetContent.' + pluginName,function(event,viewMode) {
				wysiwyg();
			});

			//切换成代码模式或者提交时
			$(_self).on('beforeGetContent.' + pluginName,function() {
				$(editorDoc.body).find('img.j_editor_music_content').each(function() {
					var url = $(this).data('url'),
						is_autoplay = $(this).data('auto');
					$(this).replaceWith('[mp3='+ is_autoplay +']'+ url +'[/mp3]');
				});
			});
	});


})( jQuery, window);