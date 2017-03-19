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
