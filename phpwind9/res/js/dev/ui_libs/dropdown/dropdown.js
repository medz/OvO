/*!
 * PHPWind UI Library 
 * Wind.dropdown 下拉菜单组件
 * Author: chaoren1641@gmail.com
 */
;(function ( $, window, document, undefined ) {
    var pluginName = 'dropdown';
    var	empty_fn = $.noop;
    var defaults = {
            event				: 'click',
    };

    function Plugin( element, selector, options ) {
        this.element = element;
        this.options = $.extend( {}, defaults, options) ;
        this.init();
    }

    Plugin.prototype.init = function () {
    	var element = this.element,options = this.options;
    	
    };

    $.fn[pluginName] = Wind[pluginName]= function ( options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin( $(this), options ));
            }
        });
    }

})( jQuery, window );
