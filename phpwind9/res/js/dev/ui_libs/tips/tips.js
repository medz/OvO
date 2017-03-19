/*!
 * PHPWind UI Library 
 * Wind.tips 对话框组件
 * Author: chaoren1641@gmail.com
 */
;(function ( $, window, document, undefined ) {
    var pluginName = 'tips';
    var	empty_fn = $.noop;
    var defaults = {
            id				: 'J_dialog',	// 默认弹出id，如果每次定义不同的id弹出，页面上将弹出多个
            type			: 'warning',	// 默认弹出类型
            message			: ''			// 弹出提示的文字
    };

    function Plugin( element, options ) {
        this.element = element;
        this.options = $.extend( {}, defaults, options) ;
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    Plugin.prototype.init = function () {
    	var element = this.element,options = this.options,dft = this._defaults;

    };

    $.fn[pluginName] = Wind[pluginName]= function ( options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin( this, options ));
            }
        });
    }

})( jQuery, window );
