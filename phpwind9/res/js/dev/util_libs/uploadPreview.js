/*
 * PHPWind util Library 
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 图片上传预览功能组件
 * @Author	: chaoren1641@gmail.com
 * @Depend	: jquery.js(1.7 or later)
 * $Id: uploadPreview.js 15186 2012-08-01 08:57:18Z hao.lin $		:
 */
;(function ( $, window, document, undefined ) {
    var pluginName = 'uploadPreview';
    var defaults = {
    		maxWidth		: 300,
    		maxHeight		: 500,
    		previewImg		: '.J_previewImg',//要预览的图片元素，如果不存在，则在input上传控件后生成一个
    		maxSize			: 2048,//默认文件限制大小
    		message : '文件大小不能超过'
    };

    function Plugin( element, options ) {
    	if($.browser.msie) {
				return ;
			}
      this.element = element;
      this.options = $.extend( {}, defaults, options) ;
      this.init();
    }
	//图片格式正则
	var IMG_TYPE_PATTERN = /^image\/((png)|(gif)|(jpg)|(jpeg)|(bmp))/i;
	var BASE64_IMG_URL_PATTERN =/^data:image\/((png)|(gif)|(jpg)|(jpeg)|(bmp));base64/i;
	
    Plugin.prototype.init = function () {
    	var element = this.element,options = this.options,
			previewCustom = $( element.data('preview') ),
    		previewImg = previewCustom.length ? previewCustom : $( options.previewImg );

    	if( !previewImg.length ) { //重复
    		previewImg = $('<img class="J_previewImg" style="display:none"/>').insertAfter(element.parent());
    	}else{
		
		}
		previewImg.css({
			'max-width'		: options.maxWidth + 'px',
			'max-height'	: options.maxheight + 'px'
		});

		//拖拽支持
		if (options.dragDrop && element.addEventListener) {
			element.addEventListener("dragover", function(e) { }, false);
			element.addEventListener("dragleave", function(e) {  }, false);
			element.addEventListener("drop", function(e) { }, false);
		}
		
		//文件选择控件选择
		element.on("change", function(e) { 
			var el = this;
			//not ie
			var file = el.files && el.files[0];
			var src, fileName = el.value;
			if(file) {
				if(!IMG_TYPE_PATTERN.test(file.type)) {
					alert('您上传的不是图片格式的文件！');
					el.value = '' ; return;
				}
				if(file.size/1024 > options.maxSize) {
					alert('文件大小不能超过' + options.maxSize/1024 + 'M');
					el.value = '' ; return;
				}
			}
			if ((file && file.getAsDataURL) && (src = file.getAsDataURL()) && (BASE64_IMG_URL_PATTERN.test(src))) {
				previewImg.prop('src', src);
				previewImg.show();
			}else if(file && FileReader) {
				var reader = new FileReader();
				reader.onload = function (e) {
                    previewImg.prop('src', e.target.result);
				};
                reader.readAsDataURL(file);
                previewImg.show();
			}else if(previewImg[0].filters) {//ie
				//el.select();
				//var src = document.selection.createRange().text;
				src = el.value;
				var img_box = previewImg.wrap('<div/>').parent();
				img_box[0].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader (src='"+ src +"', sizingMethod='scale')";
				//previewImg[0].src = src;
			}else {
				previewImg.prop('src', el.value);
				previewImg.show();
			}
			element.prev().html('重新选择');
		});	
    };

    $.fn[pluginName] = Wind[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
            	$.data(this, 'plugin_' + pluginName, new Plugin( $(this), options ));
            }
        });
    };

})( jQuery, window ,document );