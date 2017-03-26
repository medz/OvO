/**
 * PHPWind util Library
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 水平滚动，图片懒加载，适用勋章滚动
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later)
 * $Id$:
 */
;(function ( $, window, document, undefined ) {
    var pluginName = 'lazySlide';
    var defaults = {
    		dis_cls_prev : 'pre_disabled',				//上一组按钮 不可用状态class
    		dis_cls_next : 'next_disabled',				//下一组按钮 不可用状态class
    		html_arr	: []
    };

    function Plugin( element, options ) {
        this.element = element;
        this.options = $.extend( {}, defaults, options) ;
        this.init();
    }
    
    Plugin.prototype = {
		init : function (){
			var element = this.element,
				options = this.options,
				prev = element.find('.J_lazyslide_prev'),										//上一组
				next = element.find('.J_lazyslide_next'),										//下一组
				list = element.find('.J_lazyslide_list'),										//列表
				step_length = options.step_length,													//移动
				item_width = list.children().first().outerWidth(true),		//单个子元素宽度
				step_width = step_length * item_width,											//一组滚动宽度
				html_arr = options.html_arr,																//滚动加载html数组
				dis_cls_prev = options.dis_cls_prev,												//上一组按钮 不可用状态class
				dis_cls_next = options.dis_cls_next;												//下一组按钮 不可用状态class

			//截断默认显示的部分	
			html_arr.splice(0, step_length);
		
			if(!html_arr.length) {
				//数量不足
				next.addClass(dis_cls_next);
				return false;
			}else{
				next.removeClass(dis_cls_next);
			}
		
			//下一组
			next.bind('click', function(e){
				e.preventDefault();
				slide('next', next);
			});
			
			//上一组
			prev.bind('click', function(e){
				e.preventDefault();
				slide('prev', prev);
			});
			
			var lock;
			function slide(dir, btn){
				//移动函数
				
				//不可点
				if(btn.hasClass(dis_cls_prev) || btn.hasClass(dis_cls_next)) {
					return false; 
				}
				
				//重复点击锁定
				if(lock) {
					return false;
				}
				lock = true;
				
				var left = Number(list.css('marginLeft').replace('px', '')),	//负值
					move = 0;
					
				if(dir === 'next') {
					//点击下一组
					var _html_arr = html_arr.splice(0, step_length);	//截取成需插入html的新数组
					

					if(_html_arr.length) {
						//截取数组，写入html
						list.append(_html_arr.join(''));
						
						//数组空了
						//if(!html_arr.length){
							//next.addClass(dis_cls);
						//}
					}else{
						
					}

					
					var list_width = list.children().length * item_width;	//总宽度
					if(list_width + left - step_width >= step_width){
					
						//未显示区域宽度大于等于可显示区宽度
						move = left - step_width;
						
					}else{
						//未显示区域宽度小于可显示区宽度
						move = -(list_width - step_width);
						
						//下一组按钮不可点
						
					}
					
					//上一组按钮可点
					prev.removeClass(dis_cls_prev);
				}else{
					//点击上一组
					if(left < -step_width) {
						//左侧内容大于一组滚动宽度
						move = left + step_width;
						
					}else{
					
						move = 0;
						
					}
					
					//下一组按钮可点
					next.removeClass(dis_cls_next);
				}
				
				//执行滚动
				list.animate({marginLeft : move}, 'slow', function(){
					
					//点击上一组，且移动位置为0
					if(dir === 'prev' && move === 0) {
						prev.addClass(dis_cls_prev);
					}
					
					//点击下一组，且html数组已空，且左侧滚动最大
					if(dir === 'next' && !html_arr.length && step_width - Number(list.css('marginLeft').replace('px', '')) === list_width) {
						next.addClass(dis_cls_next);
					}
					
					lock = false;
				});

			}

		}
    };

    $.fn[pluginName] = Wind[pluginName]= function ( options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin( $(this), options ));
            }
        });
    };

})( jQuery, window ,document );

