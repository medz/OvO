 /**
 * PHPWind util Library 
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 阻止弹出层的父层滚动
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later)
 * $Id$
 */
;(function ( $, window, document, undefined ) {

/*! Copyright (c) 2011 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.0.6
 * 
 * Requires: 1.2.2+
 */
	var types = ['DOMMouseScroll', 'mousewheel'];

	if ($.event.fixHooks) {
	    for ( var i=types.length; i; ) {
	        $.event.fixHooks[ types[--i] ] = $.event.mouseHooks;
	    }
	}

	$.event.special.mousewheel = {
	    setup: function() {
	        if ( this.addEventListener ) {
	            for ( var i=types.length; i; ) {
	                this.addEventListener( types[--i], handler, false );
	            }
	        } else {
	            this.onmousewheel = handler;
	        }
	    },
	    
	    teardown: function() {
	        if ( this.removeEventListener ) {
	            for ( var i=types.length; i; ) {
	                this.removeEventListener( types[--i], handler, false );
	            }
	        } else {
	            this.onmousewheel = null;
	        }
	    }
	};

	$.fn.extend({
	    mousewheel: function(fn) {
	        return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
	    },
	    
	    unmousewheel: function(fn) {
	        return this.unbind("mousewheel", fn);
	    }
	});


	function handler(event) {
	    var orgEvent = event || window.event, args = [].slice.call( arguments, 1 ), delta = 0, returnValue = true, deltaX = 0, deltaY = 0;
	    event = $.event.fix(orgEvent);
	    event.type = "mousewheel";
	    
	    // Old school scrollwheel delta
	    if ( orgEvent.wheelDelta ) { delta = orgEvent.wheelDelta/120; }
	    if ( orgEvent.detail     ) { delta = -orgEvent.detail/3; }
	    
	    // New school multidimensional scroll (touchpads) deltas
	    deltaY = delta;
	    
	    // Gecko
	    if ( orgEvent.axis !== undefined && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
	        deltaY = 0;
	        deltaX = -1*delta;
	    }
	    
	    // Webkit
	    if ( orgEvent.wheelDeltaY !== undefined ) { deltaY = orgEvent.wheelDeltaY/120; }
	    if ( orgEvent.wheelDeltaX !== undefined ) { deltaX = -1*orgEvent.wheelDeltaX/120; }
	    
	    // Add event and delta to the front of the arguments
	    args.unshift(event, delta, deltaX, deltaY);
	    
	    return ($.event.dispatch || $.event.handle).apply(this, args);
	}



	//
    var pluginName = 'scrollFixed';
	var defaults = {
		type : 'normal'			//弹出类型，默认常规。'iframe'暂不支持，dialog组件会移动弹窗
	};
	
	function Plugin(element, options) {
		this.element = element;
		this.options = $.extend({}, defaults, options);
        this.init();
    }
	
    Plugin.prototype.init = function () {
		var _this = this,
			element = this.element,
			options = this.options,
			type = options.type
		var wrap_height;

		if(type == 'normal') {
			//容器滚动高度-padding上下值
			wrap_height = element[0].scrollHeight - parseInt(element.css('paddingTop').replace('px', '')) - parseInt(element.css('paddingBottom').replace('px', ''))
		}else{
			return;
		}

		element.on('mousewheel', function(event, delta, deltaX, deltaY){
			if(delta < 0 && element.height() + element.scrollTop() == wrap_height) {
				//向下滚至最大值
				event.preventDefault();
				event.stopPropagation();
			}

			if(delta > 0 && element.scrollTop() == 0) {
				//向上滚至最小值
				event.preventDefault();
				event.stopPropagation();
			}
		});
    };

	
    $.fn[pluginName] = Wind[pluginName]= function (options ) {
        return this.each(function () {
			new Plugin( $(this), options );
        });
    };

})(jQuery, window ,document);
