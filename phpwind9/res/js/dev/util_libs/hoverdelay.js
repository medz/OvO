// jQuery Hover Delay 1.0.0 (20110908)
// By John Terenzio | http://terenz.io/ | MIT License
(function($){

	var

	// original jQuery hover method
	oldHover = $.fn.hover,

	// new hover method with delay
	newHover = function(handlerIn, handlerOut, delay){
		return $(this).each(function(){

			// the timeout
			var timeout;

			// bind to original hover, but use delay
			$(this).hover(function(e){
				if (timeout) {
					timeout = clearTimeout(timeout);
				} else {
					var elem = this;
					timeout = setTimeout(function(){
						timeout = handlerIn.call(elem, e); // shortcut to set timeout to undefined, pass original event object to handler
					}, delay);
				}
			}, function(e){
				if (timeout) {
					timeout = clearTimeout(timeout);
				} else  {
					var elem = this;
					timeout = setTimeout(function(){
						timeout = handlerOut.call(elem, e); // shortcut to set timeout to undefined, pass original event object to handler
					}, delay);
				}
			});

		});
	};

	// hack original jQuery hover method to use old or new depending on whether or not a delay is passed
	$.fn.hover = function(handlerIn, handlerOut, delay){
		if (typeof delay === 'number') {
			newHover.call(this, handlerIn, handlerOut, delay);
		} else {
			oldHover.call(this, handlerIn, handlerOut);
		}
	};

})(jQuery);