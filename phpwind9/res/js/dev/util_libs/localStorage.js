/*
 * PHPWind util Library
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 浏览器本地存储
 * @Author	: chaoren1641@gmail.com
 * @Depend	: jquery.js(1.7 or later)
 * $Id: jquery.lazyload.js 3369 2012-07-26 13:19:30Z chris.chencq $		:
 */
;(function() {
	Wind.Util = Wind.Util || {};
	var localStorageName = 'localStorage',
		storage;
	function serialize(value) {
		//TODO:存储序列化或加密
		return value;
	}
	function deserialize(value) {
		return value;
	}
	Wind.Util.LocalStorage = (function() {
		if (localStorageName in window) { //chrome firefox opera
			storage = window[localStorageName];
			return {
				set: function(key, val) {
					storage.setItem(key, serialize(val));
				},
				get: function(key) {
					return deserialize(storage.getItem(key));
				},
				remove: function(key) {
					storage.removeItem(key);
				},
				clear: function() {
					storage.clear();
				}
			};
		} else if (document.documentElement.addBehavior) { //ie
			var UserData = {
			        userData : null,
			        name : location.hostname,
			        init:function(){
			            if (!UserData.userData) {
			                try {
			                    UserData.userData = document.createElement('INPUT');
			                    UserData.userData.type = "hidden";
			                    UserData.userData.style.display = "none";
			                    UserData.userData.addBehavior ("#default#userData");
			                    document.body.appendChild(UserData.userData);
			                    var expires = new Date();
			                    expires.setDate(expires.getDate()+365);
			                    UserData.userData.expires = expires.toUTCString();
			                } catch(e) {
			                    return false;
			                }
			            }
			            return true;
			        },
			        set : function(key, value) {
			            if(UserData.init()){
			            	try{
			            		UserData.userData.load(UserData.name);
				                UserData.userData.setAttribute(key, value);
				                UserData.userData.save(UserData.name);
			            	}catch(e){}
			            }
			        },
			        get : function(key) {
			            if(UserData.init()){
			            	var value;
			            	try{
			            		UserData.userData.load(UserData.name);
			            		value = UserData.userData.getAttribute(key);
			            	}catch(e){
			            		value = "";
			            	}
			            	return value;
			            }
			        },
			        remove : function(key) {
			            if(UserData.init()){
			            	try{
			            		UserData.userData.load(UserData.name);
					            UserData.userData.removeAttribute(key);
					            UserData.userData.save(UserData.name);
			            	}catch(e){}
			            }
			        },
			        clear: function(){
			        	if(UserData.init()){
			        		try{
			        			UserData.userData.load(UserData.name)
				        		var attributes = UserData.userData.XMLDocument.documentElement.attributes;
				        		for (var i = 0, attr; attr = attributes[i]; i++) {
									UserData.userData.removeAttribute(attr.name);
								}
								UserData.userData.save(UserData.name);
			        		}catch(e){}
			            }
			        }
			    };
			    return UserData;
		}
	})();
})();