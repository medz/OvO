/*
 * PHPWind util Library
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 焦点图（自动）轮播
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later)
 * @Example	: 首页焦点图
 * $Id: slidePlayer.js 19415 2012-10-13 13:43:36Z hao.lin $
 */
 
;(function ( $, window, document, undefined ) {
    var pluginName = 'slidePlayer',
        defaults = {
            active_class	: 'current',		//当前激活的li项样式
            event			: 'click',			//触发事件，默认click
            //change			: $.noop,	//当选项卡显示时发生,默认什么也不做
            fx					: 0,				//显示时的动画，支持jQuery动画
            //selected		: 0, 				//默认显示项(索引值)
			auto_play		: 0				//自动播放，默认为0表示不自动播放，单位毫秒
        };
        
    function Plugin( element, content, nav, options ) {
        this.element = element;
        this.content = content;
		this.nav = nav;
        this.options = $.extend( {}, defaults, options) ;
        //this._defaults = defaults;
        //this._name = pluginName;
        this.init();
    }

    Plugin.prototype.init = function () {
    	var element = this.element,
    		content = this.content,
			contentList = $(content).children('li'),
			nav  = this.nav,
			navList = $(nav).children(),
            options = this.options,
			auto_play = parseInt(options.auto_play),
			timer;
          	
    	function show(index) {
    		var selected_element = navList.eq(index);
    		selected_element.addClass( options.active_class ).siblings().removeClass( options.active_class );
    		contentList.eq(index).show( options.fx ).siblings().hide( options.fx );
    	}

		element.on('mouseenter', function(){
			//鼠标进入清除计时
			clearTimeout(timer);
		}).on('mouseleave', function(){
			if(auto_play) {
				//离开重新计时
				autoPlay();
			}
		});

    	//按钮绑定事件 event
    	navList.on(options.event, function(e) { 
    		e.preventDefault();
    		e.stopPropagation();
    		var index = $(this).index();
    		show(index);
    	});
		
		//按钮聚焦和点击
    	navList.children('a').on('focus click', function(e) {
    		e.stopPropagation();
    		e.preventDefault();
    		$(this).parent().trigger(options.event);
    	});

		//自动播放
		if(auto_play) {
		
			function autoPlay(){
				var current_index = navList.filter('.'+ options.active_class).index(),	//当前索引值
					index;
						
				if(current_index >= navList.length -1) {
					//到最后返回第一张
					index = 0;
				}else{
					index = current_index + 1;
				}
					
				timer = setTimeout(function(){
					show(index);
					autoPlay();
				}, auto_play);
			}
			
			autoPlay();
		}
    };

    $.fn[pluginName] = function (content, nav, options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin( $(this), content, nav ,options ));
            }
        });
    }

})( jQuery, window );
