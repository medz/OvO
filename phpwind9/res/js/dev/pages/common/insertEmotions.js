/**
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 前台-表情插入
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later), global.js
 * $Id$
 */

 /**
 * PHPWind util Library 
 * @Copyright 愚人码头
 * @Descript: 光标位置插入代码
 * @Author	: 愚人码头 http://www.css88.com/archives/3627
 * $Id: jquery.dateSelect.js 3985 2012-02-06 07:38:25Z hao.lin $
 */
;(function ( $, window, document, undefined ) {
	$.fn.extend({
		rangeInsert: function(myValue,t){
			var $t=$(this)[0];

			if (document.selection) {//ie
				this.focus();
				var sel = document.selection.createRange();
				sel.text = myValue;
				this.focus();
                sel.moveStart ('character', -l);
		        var wee = sel.text.length;
                if(arguments.length == 2){
                    var l = $t.value.length;
                    sel.moveEnd("character", wee+t );
                    t<=0?sel.moveStart("character",wee-2*t-myValue.length):sel.moveStart("character",wee-t-myValue.length);

                    sel.select();
                }
			} else if ($t.selectionStart || $t.selectionStart == '0') {
                var startPos = $t.selectionStart;
                var endPos = $t.selectionEnd;
                var scrollTop = $t.scrollTop;
                $t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
                this.focus();
                $t.selectionStart = startPos + myValue.length;
                $t.selectionEnd = startPos + myValue.length;
                $t.scrollTop = scrollTop;
                if(arguments.length == 2){
                    $t.setSelectionRange(startPos-t,$t.selectionEnd+t);
                    this.focus();
                }
            }
            else {
                this.value += myValue;
                this.focus();
            }
		}
	});

})(jQuery, window ,document);



var emotions_temp = '<div id="J_emotions_pop" style="z-index:11;position:absolute;" class="core_menu pop_show_mini">\
						<div class="core_arrow_top" style="left:0;"><em></em><span></span></div><a href="#" id="J_emotions_close" class="pop_close">关闭</a>\
						<div id="J_emotions_menu"></div>\
						<div class="ct" id="J_emotions_pl"><div class="pop_loading"></div></div>\
					</div>',
		page_size = 30,		//单页表情数
		emo_data;

function insertEmotions(elem, input, wrap) {
	var emotions_pop = $('#J_emotions_pop');

	if(emotions_pop.length) {
		if(wrap) {
			//移入容器里,定位由页面写
			emotions_pop.appendTo(wrap);
		}else{
			//移入body里
			emotions_pop.appendTo('body');
		}
		emotionsPos(elem, emotions_pop, wrap);
	}else{

		if(wrap) {
			wrap.append(emotions_temp);
		}else{
			$('body').append(emotions_temp);
		}

		var emotions_pop = $('#J_emotions_pop'),
				emotions_menu = $('#J_emotions_menu'),
				emotions_pl = $('#J_emotions_pl');

		//定位
		emotionsPos(elem, emotions_pop, wrap);

		$.getJSON(GV.URL.EMOTIONS, function(data){
			try{
				if(data.state == 'success') {
					var nav_arr = [],
						index = 0;
					emo_data = data.data;

					emotions_pl.html('');

					//循环读取菜单和表情
					$.each(data.data,function(i, o){
						index++;
						nav_arr.push('<li class="'+ (index === 1 ? 'current' : '') +'"><a href="">'+ o.category +'</a></li>');

						var emotion_arr = [],
								page_count = Math.ceil(o['emotion'].length/page_size);

						$.each(o.emotion, function(i, o){
							emotion_arr.push('<li><a href="#" class="J_emotions_item" data-sign="'+ o.sign +'"><img '+ (index === 1 ? 'src=\"'+o.url+'\"' : 'data-src=\"'+o.url+'\"') +'></a></li>');
						});




						//翻页写入
						if(page_count > 1) {
							emotions_pl.append('<div style="'+ (index === 1 ? '' : 'display:none') +'"><ul class="cc">'+ emotionsShowPage( 1, i) +'</ul></div>');

							var page = [];
							for(var j = 1; j <= page_count; j++) {
								page.push('<a href="javascript:;" class="'+ ( j===1 ? 'current':'' ) +' J_emotions_page" data-index="'+ i +'">'+ j +'</a>');
							}

							emotions_pl.children('div').eq(i).append('<div class="show_page J_emo_page">'+ page.join('') +'</div>');

						}else{
							//表情写入
							emotions_pl.append('<div style="'+ (index === 1 ? '' : 'display:none') +'"><ul class="cc">'+ emotion_arr.join('') +'</ul></div>');
						}

					});

					//点击页码
					$('.J_emo_page').on('click', 'a.J_emotions_page', function(e){
						e.preventDefault();
						var $this = $(this);
						$this.parent().prev('ul').html(emotionsShowPage( parseInt(this.innerHTML), $this.data('index')));
						$this.addClass('current').siblings().removeClass('current');
					});

					//菜单写入
					emotions_menu.prepend('<div class="hd"><ul class="cc">'+ nav_arr.join('') +'</ul></div>');

					//点击菜单
					emotions_menu.on('click', 'a', function(e){
						e.preventDefault();
						var container = emotions_pl.children().eq($(this).parent().index());

						$(this).parent().addClass('current').siblings().removeClass('current');
						container.show().siblings().hide();

						emotionsShowImg(container);

					});



					//关闭
					$('#J_emotions_close').on('click', function(e){
						e.preventDefault();
						emotions_pop.hide();
					});

				}else if(data.state == 'fail'){
					Wind.Util.resultTips({
						error : true,
						msg : data.message
					});
				}

			}catch(e) {
				$.error(e);
			}

		});

	}


	//点击表情
	$('#J_emotions_pl').off('click').on('click', 'a.J_emotions_item', function(e){
		e.preventDefault();
		//jquery.insertContent  ie7 data('sign') 有时获取不到
		input.rangeInsert(this.getAttribute('data-sign'));

		$('#J_emotions_pop').hide();
	});

}

//显示当前页
function emotionsShowPage(index, i){
	var data = emo_data[i]['emotion'];
	var len = (index*page_size > data.length ? data.length : index*page_size),
			arr = [];

	for(var i = (index-1)*page_size; i <= len - 1; i++) {
		arr.push('<li><a href="#" class="J_emotions_item" data-sign="'+ data[i].sign +'"><img src="'+data[i].url+'"></a></li>');
	}

	return arr.join('');
}

//图片src写入
function emotionsShowImg(wrap){
	var imgs = wrap.find('img');
	if(imgs.data('src')) {
		imgs.attr('src', function () {
			return $(this).data('src');
			}).data('src', '');
	}
}

//表情弹窗定位
function emotionsPos(elem, pop, wrap){
	if(wrap) {
		//容器内计算边距
		pop.css({
			left : elem.offset().left - wrap.offset().left - 30,
			top : elem.offset().top - wrap.offset().top + elem.height() + 5
		}).show();;
	}else{
		pop.css({
			left : elem.offset().left - 25,
			top : elem.offset().top + elem.outerHeight() + 15
		}).show();;
	}

}

