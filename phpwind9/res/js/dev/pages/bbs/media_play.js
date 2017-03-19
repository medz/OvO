/*!
 * PHPWind PAGE JS
 * @Copyright Copyright 2011, phpwind.com
 * @Descript: 视频和音乐播放
 * @Author	: chaoren1641@gmail.com
 * @Depend	: jquery.js(1.7 or later), jquery.jplayer.js(http://jplayer.org/)
 * $Id$
 */
 $(function() {
 	var jPlay_js = window.GV.JS_ROOT + 'util_libs/jPlayer/jplayer.js?v=' + GV.JS_VERSION;
	var jPlay_skin_css = window.GV.JS_ROOT + 'util_libs/jPlayer/skin/blue.monday/jplayer.blue.monday.css?v=' + GV.JS_VERSION;

	//音频
	var audios = $('div.J_audio');
	if( audios.length ) {
		Wind.css(jPlay_skin_css);
		Wind.js(jPlay_js,function() {
			parseAudios();
		});
		var audio_play_template = '\
		<div class="jp-audio">\
			<div class="jp-type-single">\
					<div class="jp-gui jp-interface">\
						<ul class="jp-controls">\
							<li><a href="javascript:;" class="jp-play" tabindex="1">播放</a></li>\
							<li><a href="javascript:;" class="jp-pause" tabindex="1">暂停</a></li>\
							<li><a href="javascript:;" class="jp-stop" tabindex="1">停止</a></li>\
							<li><a href="javascript:;" class="jp-mute" tabindex="1" title="静音">静音</a></li>\
							<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>\
							<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="最大音量">最大音量</a></li>\
						</ul>\
						<div class="jp-progress">\
							<div class="jp-seek-bar">\
								<div class="jp-play-bar"></div>\
							</div>\
						</div>\
						<div class="jp-volume-bar">\
							<div class="jp-volume-bar-value"></div>\
						</div>\
						<div class="jp-time-holder">\
							<div class="jp-current-time"></div>\
							<div class="jp-duration"></div>\
							<ul class="jp-toggles">\
								<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="重复播放">重复播放</a></li>\
								<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="取消重复播放">取消重复播放</a></li>\
							</ul>\
						</div>\
					</div>\
					<div class="jp-no-solution">\
						<span>更新提示：</span>\
						要播放媒体，您需要更新您的浏览器到最新的版本，或更新您的 <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash插件</a>.\
					</div>\
				</div>\
			</div>';
		function parseAudios() {
			audios.each(function(i) {
				var id = 'jp_audio_container_' + i;
				var type = $(this).data('type');
				$(audio_play_template).insertAfter(this).prop('id',id);
				$(this).jPlayer({
					ready: function (event) {
						var options = {}
						options[type] = $(this).data('url');
						console.log(type,$(this).data('url'))
						$(this).jPlayer("setMedia",options);
					},
					swfPath: window.GV.JS_ROOT + 'util_libs/jPlayer',
					supplied: $(this).data('type'),
					wmode: "window",
					cssSelectorAncestor: '#'+id
				});
			});
		}
	}

	//视频
	var videos = $('div.J_video');
	if( videos.length ) {
		Wind.css(jPlay_skin_css);
		Wind.js(jPlay_js,function() {
			parseVideos();
		});
		var video_play_template = '<div class="jp-video jp-video-360p">\
			<div class="jp-type-single">\
				<div id="jquery_jplayer_1" class="jp-jplayer"></div>\
				<div class="jp-gui">\
					<div class="jp-video-play">\
						<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>\
					</div>\
					<div class="jp-interface">\
						<div class="jp-progress">\
							<div class="jp-seek-bar">\
								<div class="jp-play-bar"></div>\
							</div>\
						</div>\
						<div class="jp-current-time"></div>\
						<div class="jp-duration"></div>\
						<div class="jp-controls-holder">\
							<ul class="jp-controls">\
								<li><a href="javascript:;" class="jp-play" tabindex="1">播放</a></li>\
								<li><a href="javascript:;" class="jp-pause" tabindex="1">暂停</a></li>\
								<li><a href="javascript:;" class="jp-stop" tabindex="1">停止</a></li>\
								<li><a href="javascript:;" class="jp-mute" tabindex="1" title="静音">静音</a></li>\
								<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="取消静音">取消静音</a></li>\
								<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="最大音量">最大音量</a></li>\
							</ul>\
							<div class="jp-volume-bar">\
								<div class="jp-volume-bar-value"></div>\
							</div>\
							<ul class="jp-toggles">\
								<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="全屏播放">全屏播放</a></li>\
								<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>\
								<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="重复播放">重复播放</a></li>\
								<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="取消重复播放">取消重复播放</a></li>\
							</ul>\
						</div>\
					</div>\
				</div>\
				<div class="jp-no-solution">\
					<span>更新提示</span>\
					要播放媒体，您需要更新您的浏览器到最新的版本，或更新您的 <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash插件</a>.\
				</div>\
			</div>\
		</div>';
		
		function parseVideos() {
			videos.each(function(i) {
				var id = 'jp_audio_container_' + i;
				var type = $(this).data('type');
				var width = $(this).data('width');
				var height = $(this).data('height');
				var url = $(this).data('url');
				if(type === 'swf') {
					var flash_elem = create_flash(url,width,height,id);
					$(flash_elem).insertAfter(this).prop('id',id);
				}else {
					$(video_play_template).insertAfter(this).prop('id',id);
					$(this).jPlayer({
						ready: function (event) {
							var param = {};
							param[type] = $(this).data('url');
							$(this).jPlayer("setMedia", param);
						},
						swfPath: window.GV.JS_ROOT + 'util_libs/jPlayer',
						supplied: param[type],
						cssSelectorAncestor: '#'+id,
						size: {
							width: $(this).data('width') + 'px',
							height: $(this).data('height') + 'px',
							cssClass: "jp-video-360p"
						}
					});
				}
			});
		}
	}

	function create_flash(url, width, height, id) {
	    if ($.browser.msie) {
	        return '<object classid="CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000" id="' + id + '" width="' + width + '" height="' + height + '">\
						<param name="src" value="' + url + '" />\
						<param name="autostart" value="true" />\
						<param name="loop" value="true" />\
						<param name="allownetworking" value="internal" />\
						<param name="allowscriptaccess" value="never" />\
						<param name="allowfullscreen" value="true" />\
						<param name="quality" value="high" />\
						<param name="wmode" value="transparent">\
					</object>';
	    } else {
	        return '<object type="application/x-shockwave-flash" data="' + url + '" width="' + width + '" height="' + height + '" id="' + id + '" style="">\
						<param name="movie" value="' + url + '">\
						<param name="allowFullScreen" value="true">\
						<param name="autostart" value="true" />\
						<param name="loop" value="true" />\
						<param name="allownetworking" value="internal" />\
						<param name="allowscriptaccess" value="never">\
						<param name="quality" value="high" />\
						<param name="wmode" value="transparent">\
						<div style="height:100%">您还没有安装flash播放器,请点击<a href="http://www.adobe.com/go/getflash" target="_blank">这里</a>安装</div>\
				    </object>';
	    }
	}
 });