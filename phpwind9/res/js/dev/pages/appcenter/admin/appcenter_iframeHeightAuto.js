/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 后台-应用中心iframe高度适应
 * @Author	: linhao87@gmail.com
 * @Depend	: jquery.js(1.7 or later)
 * $Id$
 */
 
 ;(function(){
	//应用中心iframe
	var appcenter_iframe = $('#J_appcenter_iframe');
	
	setInterval(function(){
		iframeResize();
	}, 300);
	
	function iframeResize(){
		var top_iframe = $(top.document).find('#iframe_platform_'+ appcenter_iframe.data('id'));		//后台第一层iframe data-id对应菜单属性
		
		top_iframe.attr('scrolling', 'no');																							//取消滚动
		appcenter_iframe[0].height = $(top_iframe[0].contentWindow).height();									//写入高度
	}
 })();