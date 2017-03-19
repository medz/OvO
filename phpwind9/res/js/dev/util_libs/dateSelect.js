 /**
 * PHPWind util Library 
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: select控件的日期组件
 * @Author	: linhao87@gmail.com
 * @Depend	: core.js、jquery.js(1.7 or later)
 * $Id: dateSelect.js 21313 2012-12-04 08:55:39Z hao.lin $
 */
;(function ( $, window, document, undefined ) {
    var pluginName = 'dateSelect';

	function Plugin(element) {
		this.element = element;
        this.init();
    }
	
    Plugin.prototype.init = function () {
		var element = this.element,
			year = element.find('select.J_date_year'),
			month = element.find('select.J_date_month'),
			day = element.find('select.J_date_day');
		
		//页面加载后判断大小及闰月
		dayInit(monthType(month.val()));
		
		//点击select后判断大小及闰月
		year.on('change', function(){
			if(month.val() === '2') {
				var day_num = (leapYear($(this).val()) ? 29 : 28);
				dayInit(day_num);
			}
		});
		
		month.on('change', function(){
			dayInit(monthType($(this).val()));
		});
		
		
		//判断是否闰年
		function leapYear(val){
			return (val%4 === 0 ? true : false);
		}
		
		//判断大小月
		function monthType(m_v){
			var day_num;
			switch(m_v){
				case '1' :
				case '3' :
				case '5' :
				case '7' :
				case '8' :
				case '10' :
				case '12' :{
					day_num = 31;
					break;
				}
				case '2' : {
					if(leapYear(year.val())) {
						day_num = 29;
					}else{
						day_num = 28;
					}
					break;
				}
				default : {
					day_num = 30;
					break;
				}
			}
			return day_num;
		}
		
		//初始化日期列表
		function dayInit(day_num){
			var _day = parseInt(day.val());
			
			if(!$.browser.msie) {
				//非ie 隐藏多出的天数
				day.children().show().filter('option:gt('+ (day_num-1) +')').hide();
			}else{
				//ie 重新组装
				var arr = [];
				for(i=1;i<=day_num;i++) {
					arr.push('<option value="'+ i +'">'+ i +'日</option>');
				}

				day.html(arr.join('')).children('[value='+ _day +']').prop('selected', true);
			}
			
			//当前日期超出则初始为1号
			if(_day > day_num) {
				day.children('option:first').prop('selected', true);
			}
		}
		
    };

	
    $.fn[pluginName] = Wind[pluginName]= function (options ) {
        return this.each(function () {
			new Plugin( $(this) );
        });
    };

})(jQuery, window ,document);
