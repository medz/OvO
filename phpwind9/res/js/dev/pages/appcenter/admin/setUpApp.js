/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 应用中心安装
 * @Author	: siweiran@gmail.com
 * @Depend	: jquery.js(1.7 or later), dialog
 * $Id$
 */
 
(function(){
		//安装app类
	var setUpApp = {
			hash : null,//保存每次返回的hash值
			up_form : $('#uploadForm'),
			loadStep : $('#loadStep'),
			up_hidden : $("#J_file_input"),
			up_btn : $('#J_upload_btn'),
			schedule : $('#J_install_schedule'),
			percent : $('#J_percent'),
			percent_num : 0,
			cc : $('#J_cc'),
			init : function(){
				var _this = this;
				_this.up_btn.bind('click',function(e){//安装按钮事件
					e.preventDefault();
					$('#J_up_del').hide();
					_this.up_btn.css('visibility', 'hidden');
					_this.cc.show();
					_this.showStep();
				})
				//点击'重试'
				$('.redo').live('click',function(e){
					e.preventDefault();
					var p = $(this).parent();
					//_this.loadStep.find("p:last").remove();
					_this.loadStep.html('');
					p.remove();
					_this.percent_num = 0;
					_this.schedule.css('width', _this.percent_num + '%');
	    			_this.percent.html(_this.percent_num + '%');
					_this.requestStep(conf.stepUrl,{step:0,hash:_this.hash})
				})
				//上传成功之后点击返回
				$('.backup').live('click',function(e){
					e.preventDefault();
					window.location.href=window.location.href;
				})
			},
			showAjaxData : function(url, data, type, dataType, sucCallBack){
		        $.ajax({
		            url: url,
		            data: data,
		            type: type ? type : "POST",
		            dataType: dataType,
		            success: function (data) {
		                sucCallBack(data);
		            },
		            error: function () {
		             resultTip({
		                   error: true,
		                    msg: "请求出错,请重试",
		                   follow: false
		                });
		            }
		        })
			},
			showStep : function(){
				var _this=this;
				var callback = function(data){
					var msg = data.message,params=data.data;
			    	if(data.state === 'success'){
			    	 	$.each(msg,function(k,v){
			        		$("<p class='step_msg tips_loading'>"+v+"</p>").appendTo(_this.loadStep);
			        	})
			    		if(params === undefined || params.step === undefined){
			    			_this.schedule.css('width', '100%');
			    			_this.percent.html('100%');
			    			
			    			_this.loadStep.children().removeClass('tips_loading');
			    			$("<div><a href='javascript:;' class='backup'>请返回</a></div>").appendTo(_this.loadStep);
			    		}else{
			    			_this.percent_num += 15;
			    			if(_this.percent_num >= 100) {
	        					_this.percent_num = 99;
	        				}
			    			_this.schedule.css('width', _this.percent_num + '%');
			    			_this.percent.html(_this.percent_num + '%');
			    			
			    			_this.loadStep.children().removeClass('tips_loading');
			    			_this.hash = params.hash;//保存每次传递回来的hash值
			    			_this.requestStep(conf.stepUrl,{step:params.step,hash:params.hash});
			    		}
			    	}else{
			    		$.each(msg,function(k,v){
			    			$("<p class='step_msg'>"+v+"</p>").appendTo(_this.loadStep);
			    		});
			    		if(params === undefined || params.step === undefined){
			    			$("<div><a href='javascript:;' class='backup'>返回</a></div>").appendTo(_this.loadStep);
			    		} else {
			    			_this.step = params.step;
							_this.hash = params.hash;//保存每次传递回来的hash值
							$("<div><a href='javascript:;' class='redo'>请重试</a>或者<a href='javascript:;' class='backup'>返回</a></div>").appendTo(_this.loadStep);
			    		}
					}
				}			
				var url = this.up_form.attr('action');
				this.showAjaxData(url,{file:_this.up_hidden.val()},"GET",'json',callback)
			},
			
			 requestStep : function(url,params){
				var _this=this,me = arguments.callee;//递归调用自身
		        $.ajax({
		            url: url,
		            data: params,
		            type: "GET",
		            dataType: "JSON",
		            success: function (data) {
		        		var msg = data.message,params=data.data;
		            	if(data.state === 'success'){
		            		_this.loadStep.children().removeClass('tips_loading');
		        	 		$.each(msg,function(k,v){
		            			$("<p class='step_msg tips_loading'>"+v+"</p>").appendTo(_this.loadStep);
		            		});
		        			if(params.step === undefined){
		        				_this.schedule.css('width', '100%');
				    			_this.percent.html('100%');
		        				
		        				_this.loadStep.children().removeClass('tips_loading');
		        				$("<p><a href='javascript:;' class='backup'>请返回</a></p>").appendTo(_this.loadStep);
		        			}else{
		        				_this.percent_num += 15;
		        				if(_this.percent_num >= 100) {
		        					_this.percent_num = 99;
		        				}
				    			_this.schedule.css('width', _this.percent_num + '%');
				    			_this.percent.html(_this.percent_num + '%');
				    			
		        				//递归请求
		        				setTimeout(function(){me.apply(_this,[url,{step:params.step,hash:params.hash}])},1000);
		        			}
		            	}
		            	else {
		            		_this.step = params.step;
							_this.hash = params.hash;//保存每次传递回来的hash值
							_this.loadStep.children().removeClass('tips_loading');
		        			$.each(msg,function(k,v){
		        				$("<p class='step_msg'>"+v+"</p>").appendTo(_this.loadStep);
		        			})
		        			$("<div><a href='javascript:;' class='redo'>请重试</a>或者<a href='javascript:;' class='backup'>返回</a></div>").appendTo(_this.loadStep);
		            	}
		            },
		            error : function () {
		            	_this.loadStep.children().removeClass('tips_loading');
		            	$("<div>请求出错,请检查网络</div>").appendTo(_this.loadStep);
		            }
		        })
			}		
	}
	setUpApp.init()
})()