/*
 * PHPWind WindEditor Plugin
 * @Copyright 	: Copyright 2011, phpwind.com
 * @Descript	: 编辑器@功能
 * @Author		: chaoren1641@gmail.com
 * $Id: windeditor.js 4472 2012-02-19 10:41:01Z chris.chencq $			:
 */
;(function ( $, window, undefined ) {
	
	var WindEditor = window.WindEditor;
	
	var pluginName = 'atremind';
	
	WindEditor.plugin(pluginName,function() {
		var _self = this;
		var editorDoc = _self.editorDoc = _self.iframe[0].contentWindow.document;
		/*plugin_icon = $('<div class="wind_icon" data-control="'+ pluginName +'" unselectable="on"><span class="'+ pluginName +'" title="at"></span></div>').appendTo( _self.pluginsContainer );*/
		var atDialogDom;

		var KEY = { BACKSPACE : 8, TAB : 9, RETURN : 13, ESC : 27, LEFT : 37, UP : 38, RIGHT : 39, DOWN : 40, COMMA : 188, SPACE : 32, HOME : 36, END : 35 };
		var timer;
		//好友数据，便于缓存在浏览器端
		var friendData,recentFriendData;
		var resultBox = '';
		//当前的form
		var form = _self.textarea[0].form;
		var ime_mode = false;//是否处于中文状态 true中文， false英文

		/*
		* 弹窗中的@功能(暂时去掉)
		*/
		/*plugin_icon.on('click',function(e) {
			if($(this).hasClass('disabled')) {
				return;
			}
			if(atDialogDom && atDialogDom.length) {
				_self.showDialog(atDialogDom);
				dialogAfterShow();
			} else {
				$.get(AT_DIALOG_URL,function(data) {
					atDialogDom = $(data).appendTo(document.body);
					//弹窗中的事件交互
					dialogEventInit();
					_self.showDialog(atDialogDom);
					dialogAfterShow();
					//弹窗的关闭事件 
					atDialogDom.find('a.edit_menu_close,a.J_at_close').on('click',function(e) {
						e.preventDefault();
						_self.hideDialog();
					});
				});
			}
		});

		//弹窗每次出来都要处理的一些逻辑
		function dialogAfterShow() {
			//把编辑器中已有的@带入弹窗
			var tempNode = document.createDocumentFragment();
			var arr = [];
			$(_self.editorDoc).find('a').each(function() {
				if(this.href.indexOf(AT_USER_SPACE) !== -1) {
					var username = $(this).text().replace('@','');
					if($.inArray(username,arr) === -1) {
						arr.push(username);
						tempNode.appendChild(this.cloneNode(true));
					}
				}
			});
			
			if(tempNode.childNodes.length) {
				atDialogDom.find('div.info').html('您提到了'+ arr.length + '人：').append(tempNode);
			}
		}

		function dialogEventInit() {
			//展开收起
			atDialogDom.find('dt.J_friend_dt').on('click', function() {
				var $this = $(this),
					parent = $this.parent();
				parent.toggleClass('current').siblings().removeClass('current');

				if(!$this.siblings().length) {
					//未载入其他分组
					$.getJSON($this.data('fanurl'), function(data){
						if(data.state == 'success') {
							var arr = [];
							$.each(data.data, function(key, value){
								arr.push('<dd data-id="'+ key +'" data-name="friend" class="J_friend_item" id="J_firend_dd_'+ key +'">'+ value +'</dd>')
							});
							$this.after(arr.join(''));
						}
					});
				}
			});

			var friend_selected = $('#J_friend_selected'),
				max = atDialogDom.data('max');		

			atDialogDom.on('click', 'dd.J_friend_item', function() {
				//选择好友
				var $this = $(this),
					id = $this.data('id');

				if($this.hasClass('disabled')) {
					return false;
				}
				if(!$this.hasClass('in') && friend_selected.children().length < max) {
					friend_selected.append('<li id="J_friend_'+ id +'"><input type="hidden" name="friend[]" value="'+ id +'" /><a href="#" class="J_friend_name" data-id="'+ id +'">'+ $this.text() +'<span  class="J_friend_del">×</span></a></li>');
					$this.addClass('in');
				}else {
					$this.removeClass('in');
					$('#J_friend_'+ id).remove();
				}
			}).on('click', '.J_friend_del', function(){
				//删除选择
				$(this).parents('li').fadeOut('fast', function(){
					$(this).remove();
				});
				$('#J_firend_dd_'+ $(this).data('id')).removeClass('in');
			}).on('click', 'a', function(e){
				e.preventDefault();
			});

			//点击提交插入编辑器
			atDialogDom.find('.edit_menu_btn').on('click',function(e) {
				e.preventDefault();
				var arr = [];
				friend_selected.find('.J_friend_name').each(function() {
					var username = $(this).text().replace('×','');
					var uid = $(this).data('id');
					arr.push('<span class="J_at" style="color:blue;" data-role="at" data-username="'+ username +'">@'+ username +'</span><span>&nbsp;</span>');
				});
				_self.insertHTML(arr.join('&nbsp'));
				_self.hideDialog();
				friend_selected.empty();
				atDialogDom.find('dd.in').removeClass('in');
			});
		}*/
		//创建@节点
		function createAt (){
			var id = "tmp" + (+new Date());
			_self.insertHTML('<span class="J_at" id="'+ id +'">@</span>');
			var span = editorDoc.getElementById(id),
				range = _self.getRange(),
				sel = _self.getSelection();
			if(editorDoc.body.createTextRange) {
				range.moveToElementText(span);
				range.moveStart("character");
				range.select();
			}else {
				var ospan = span.firstChild;
                range.setStart(ospan, 1);
                range.setEnd(ospan, 1);
                sel.removeAllRanges();
                sel.addRange(range);
			}
			clearTimeout(timer);
			timer = setTimeout(function() {
				//取最近@过的朋友
				showRecentFriendData($(span));
			},0);
		}

		/*
		* 编辑器的键盘@
		*/
		$(_self.editorDoc).on({
			keydown:function(e) {
				var key = e.keyCode;
				var isAt = e.shiftKey && key === 50;
				//是否开启输入法
				ime_mode = e.shiftKey && (key === 229 || key === 197 || key === 0);
				if(isAt) {
					e.preventDefault();
					createAt();
				}
				//监听键盘按键，如果下拉结果是显示的且光标在@中，则判断up、down、esc、enter
				if(resultBox.length && resultBox.is(':visible')) {
					var $span = _self.getRangeNode('span');
					if($span.length) {
						switch (e.which) {
					        case KEY.UP:
					        	e.preventDefault();
					        	movePrev();
					        	return;
					        case KEY.DOWN:
					        	e.preventDefault();
					          	moveNext();
					          	return;
					        case KEY.RETURN:
					        	e.preventDefault();
					        	choose();
					        	return;
					        case KEY.ESC:
					        	hideResultOptions();
					          break;
			      		}
					}
		      	}
			},
			keyup:function(e) {
				var key = e.keyCode;
				//只有在keyup时才能完全获取当前需要提示过滤的名称，keydown时获取不到最后一个字母
				var character = String.fromCharCode(e.which).toLowerCase();
				if(e.which > 36 && e.which <41) {
					//37,38,39,40分别代表上下左右键，过滤菜单时屏蔽这几个键
					return;
				}
				//如果是输入法模式
				//console.log('中文模式:' + ime_mode)
				if(ime_mode){
					var range = _self.getRange();
					//获取光标父节点的最后一个节点
					if(!$.browser.msie){
						var node=range.startContainer;
						//如果正常获取节点内容
						if(node&&node.nodeValue!=null){
							var len=node.length;
							var str=node.nodeValue.substr(len-1,1);
							if(str=="@"){
								//如果发现刚刚输入的是@ 就删除这个@然后构造我们自己的@
								range.setStart(node, len-1);
								range.setEnd(node, len);
								range.deleteContents();
								createAt();
							}
						}
					}else{
						var node=range.parentElement().lastChild;
						//如果正常获取节点内容
						if(node&&node.nodeValue!=null){
							var len=node.length;
							var str=node.nodeValue.substr(len-1,1);
							if(str=="@"){
								node.nodeValue=node.nodeValue.substr(0,len-1)
								createAt();
							}
						}
					}
				}
				//输入法模式完毕
				
				var $span = _self.getRangeNode('span');
				if(!$span.length || !$span.hasClass('J_at')) {
					hideResultOptions();
					return;
				}
				var username = $span.text();
				if(username.indexOf('@') !== 0) {//检查是否有@
					hideResultOptions();
					return;
				}
				showRecentFriendData($span,username.replace('@',''));
				/*if(resultBox.length && resultBox.is(':visible')) {
					var span = _self.getRangeNode('span');
					if(span.length) {
						switch (e.keyCode) {
					        case KEY.UP:
					        	e.preventDefault();
					        	return;
					        case KEY.DOWN:
					        	e.preventDefault();
					          	return;
					        case KEY.RETURN:
					        	e.preventDefault();
					        case KEY.ESC:
					        	hideResultOptions();
					        	return;
			      		}
					}
		      	}
				var span = _self.getRangeNode('span');
				if(!span.length || !span.hasClass('J_at')) {
					hideResultOptions();
					return;
				}
				var username = span.text();
				if(username.indexOf('@') !== 0) {//检查是否有@
					hideResultOptions();
					return;
				}
				clearTimeout(timer);
				//必须以@开头
				if(username === '@') {
					timer = setTimeout(function() {
						showRecentFriendData(span);
					},200);
				}else {
					//在@后输入文字后则在朋友数据中拉取
					username = username.replace('@','');
					showFriendData(username,span);
				}*/
			},
			input:function(e) {
				//console.log(e)
				//console.log(e)
			}
		});
		
		//显示最近@过的朋友
		function showRecentFriendData($span,filterName) {
			var showData;
			if(recentFriendData) {
				if(filterName) {
					showData = filterData(filterName,recentFriendData);
				}else {
					showData = recentFriendData;
				}
				showResultOptions(showData,$span);
			}else{
				$.getJSON(AT_URL,function(data) {
					if(data.state === 'success') {
						recentFriendData = data;//缓存起来
						if(filterName) {
							showData = filterData(filterName,recentFriendData);
						}else {
							showData = recentFriendData;
						}
						showResultOptions(showData,$span);
					} else {
						$.error(data.message);
					}
				});
			}
		}
		//数据是一次性拉取出来的，所以要在前端过滤
		function filterData(username,data) {
			var resultData = {};
			resultData.data = {};//模拟服务器端的数据格式
			$.each(data.data,function(key,value) {
				var re = new RegExp(username);
				if(value.match(re)) {
					resultData.data[key] = value;
				}
			});
			return resultData;
		}
		
		//显示下拉选项
		function showResultOptions(data,$span) {
			var offset = $span.offset();
			if(!resultBox.length) {
				resultBox = $('<div class="edit_menu"></ul></div>');
				$(document.body).append(resultBox);
			}
			var arr = [],i = 0;
			/*if(text === '@') {
				arr.push('<li>选择最近@的人或直接输入</li>');
			}else {
				arr.push('<li>选择昵称或轻敲空格完成输入</li>');
			}*/
			$.each(data.data,function(key,value) {
				arr.push('<li data-id="'+ key +'"><a>'+ value +'</a></li>');
				i++;
			});
			if(i < 1) {
				hideResultOptions();
				return;
			}
			resultBox.html('<ul class="edit_menu_select edit_atul" id="atResult">'+ arr.join('') + '</ul>');
			resultBox.find('li:first').addClass('activate');
			var iframeOffset = _self.iframe.offset();
			resultBox.css({
				left:iframeOffset.left + offset.left,
				top:iframeOffset.top + offset.top + $span.height()
			}).show();
		}

		//隐藏下拉
		function hideResultOptions() {
			if(resultBox && resultBox.length) {
				resultBox.hide();
			}
		}

		//按下箭头函数
		function moveNext() {
			var current = resultBox.find('li.activate');
			current.removeClass('activate');
			var next = current.next();
			if(next.length) {
				next.addClass('activate');
			}else {
				resultBox.find('li').first().addClass('activate');
			}
		}
		//按上箭头函数
		function movePrev() {
			var current = resultBox.find('li.activate');
			current.removeClass('activate');
			var prev = current.prev();
			if(prev.length) {
				prev.addClass('activate');
			}else {
				resultBox.find('li').last().addClass('activate');
			}
		}
		
		//回车选择或者鼠标点击选择
		function choose() {
			_self.focus();
			var current = resultBox.find('li.activate');
			if(current.length) {
				var $span = _self.getRangeNode('span');
				var username = current.text();
				var uid = current.data('id');
				var at = $('<span class="J_at" data-role="at" data-username="'+ username +'">@'+ username +'</span><span>&nbsp;</span>');
				$span.replaceWith(at);
				var range = _self.getRange();
				if(!range) {
					hideResultOptions();
					_self.setFocus($(_self.editorDoc.body));
					return;
				}
				if(range.moveToElementText) {
					//range.moveToElementText(nativeNode);
				}else {
					var node = at.next()[0];
					range.setStart(node, 1);
					range.setEnd(node,1);
					//range.collapsed = true;
					var sel = _self.getSelection();
					sel.removeAllRanges();
					sel.addRange(range);
				}
			}
			hideResultOptions();
		}
		
		//点击当前选中的执行@
		$(document.body).on('mouseenter','#atResult > li',function(e) {
			$(this).addClass('activate').siblings().removeClass('activate');
		}).on('click','#atResult > li',function() {
			choose();
		});
		
		$(_self).on('beforeGetContent.' + pluginName,function() {
			$(editorDoc.body).find('span.J_at').each(function() {
				$(this).replaceWith(this.innerHTML);
			});
		});

	});
})( jQuery, window);