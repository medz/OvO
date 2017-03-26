package
{
	import com.adobe.images.JPGEncoder;
	import com.adobe.utils.IntUtil;
	import com.phpwind.avatar.EditWindow;
	
	import fl.controls.Slider;
	
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.Loader;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.DataEvent;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.net.FileFilter;
	import flash.net.FileReference;

	import flash.events.ErrorEvent;
    import flash.events.Event;
	import flash.events.IOErrorEvent;
    import flash.net.URLLoader;
    import flash.net.URLLoaderDataFormat;
    import flash.net.URLRequest;
	import flash.net.URLRequestHeader;
    import flash.net.URLRequestMethod;
    import flash.net.URLVariables;
	
	import flash.net.navigateToURL;
	import flash.utils.ByteArray;
	import flash.ui.ContextMenu;
	import flash.ui.ContextMenuItem;
	
	import org.bytearray.gif.player.GIFPlayer;
	import fl.motion.MotionEvent;
	import flash.media.Camera;
    import flash.media.Video;
    import flash.sampler.StackFrame;
	import flash.external.ExternalInterface;
	import com.phpwind.util.UploadPostHelper;
	
	public class Crop extends MovieClip
	{
		public static const  RETURN_ERROR:String = '图片上传出现错误，请重新选择';
		public static const  IO_ERROR:String     = '图片上传出现IO错误，请检查网路';
		public static const UPLOAD_WAIT:String   = '图片上传中，请等待...';
		
		private var isGIF:Boolean;
		private var loader:Loader;
		private var editwindow:EditWindow;
		private var imageCanvas:Rectangle;
		private var bigPreview:Bitmap;
		private var smallPreview:Bitmap;
		private var normalPreview:Bitmap;
		private var file:FileReference;
		private var maxSize:uint;
		private var video:Video;
		private var camera:Camera;
		public function Crop()
		{
			var contextMenu_:ContextMenu = new ContextMenu();
            contextMenu_.hideBuiltInItems();
			var menu:ContextMenuItem = new ContextMenuItem("Powered by phpwind", false, false);
			//menu.addEventListener(Event.OPEN,goPHPWind);
            contextMenu_.customItems.push(menu);
            this.contextMenu = contextMenu_;
			
			this.stop();
			
			file = new FileReference();
			file.addEventListener(Event.SELECT, selectHandler);//选择图片
			file.addEventListener(Event.CANCEL, cancelHandler);//取消选择
			file.addEventListener(Event.COMPLETE, loadFile);//载入完成
			
			getimg.addEventListener(MouseEvent.CLICK, upload);//点击选择按钮
			cameraButton.addEventListener(MouseEvent.CLICK,openCamera);
			localUploadButton.addEventListener(MouseEvent.CLICK,swichLocalUpload);
			
			loader = new Loader();
			loader.contentLoaderInfo.addEventListener(Event.COMPLETE, loadImg);//载入图片
			
			//load default image
			var mvParams:Object = stage.loaderInfo.parameters; 
			var avatar:String = String(mvParams["avatar"]);
			//avatar = 'http://localhost/NextWind/www/attachment/avatar/000/00/00/2_middle.jpg';
			if(avatar!== 'undefined') {
				var imageLoader1:Loader = new Loader();
				
				var imageLoader2:Loader = new Loader();
				
				var imageLoader3:Loader = new Loader();
				
				
				var urlRequest:URLRequest = new URLRequest(avatar);
				//disabled flash cache 跨子域图片不显示
				//urlRequest.requestHeaders.push(new URLRequestHeader("pragma", "no-cache"));
				//urlRequest.requestHeaders.push(new URLRequestHeader("Expires", "Thu, 01 Jan 1970 00:00:00 GMT, -1"));
				//urlRequest.requestHeaders.push(new URLRequestHeader("Cache-Control", "no-cache, no-store, must-revalidate"));
				imageLoader1.load(urlRequest); 
				imageLoader2.load(urlRequest); 
				imageLoader3.load(urlRequest); 
				imageLoader1.contentLoaderInfo.addEventListener(Event.COMPLETE, imageLoaded1); 
				imageLoader2.contentLoaderInfo.addEventListener(Event.COMPLETE, imageLoaded2); 
				imageLoader3.contentLoaderInfo.addEventListener(Event.COMPLETE, imageLoaded3); 
				function imageLoaded1(e:Event):void { 
					imageLoader1.width = imgPreview200_200_.width - 1;
					imageLoader1.height = imgPreview200_200_.height -1;
					imageLoader1.x = 1;
					imageLoader1.y = 1;
					// 把 imageLoader 加入到loaderImage_mc显示清单中
					imgPreview200_200_.addChild(imageLoader1); 
				} 
				function imageLoaded2(e:Event):void { 
					imageLoader2.width = imgPreview120_120_.width - 1;
					imageLoader2.height = imgPreview120_120_.height -1;
					imageLoader2.x = 1;
					imageLoader2.y = 1;
					// 把 imageLoader 加入到loaderImage_mc显示清单中
					imgPreview120_120_.addChild(imageLoader2);
				} 
				function imageLoaded3(e:Event):void { 
					imageLoader3.width = imgPreview48_48_.width - 1;
					imageLoader3.height = imgPreview48_48_.height -1;
					imageLoader3.x = 1;
					imageLoader3.y = 1;
					// 把 imageLoader 加入到loaderImage_mc显示清单中
					imgPreview48_48_.addChild(imageLoader3);
				} 
			}
		}
		
		private function goPHPWind(e:Event)
		{
			navigateToURL(new URLRequest('http://www.phpwind.net'),'_blank');
		}
		private function loadImg(e:Event):void
		{
			var bitmap:GIFPlayer = new GIFPlayer(true);;
			if (isGIF){
				bitmap.loadBytes(file.data);
			}else{
				var _tmp:Bitmap = Bitmap(loader.content);
				bitmap.bitmapData = _tmp.bitmapData;
			}
			gotoEditWindow(bitmap,isGIF);
			//swapChildren(editwindow,tip);
		}
		//切称到编辑界面
		private function gotoEditWindow(bitmap:GIFPlayer,isGIF:Boolean):void
		{
			if(this.currentFrame==1)
			{
				this.gotoAndStop(2);
				zoomIn.addEventListener(MouseEvent.CLICK, zoomInHandler);
				zoomOut.addEventListener(MouseEvent.CLICK, zoomOutHandler);
				clockwise.addEventListener(MouseEvent.CLICK, clockwiseHandler);
				anticlockwise.addEventListener(MouseEvent.CLICK, anticlockwiseHandler);
				reupload.addEventListener(MouseEvent.CLICK, upload);
				save.addEventListener(MouseEvent.CLICK, saveImg);
			}
			//判断旋转是否显示
			clockwise.visible = anticlockwise.visible = !isGIF;
			
			//建立预览框
			//200*200
			if(bigPreview)
				this.removeChild(bigPreview);
			bigPreview = new Bitmap(new BitmapData(200,200),'never',true);
			bigPreview.x = 350;
			bigPreview.y = 54;
			addChild(bigPreview);
			
			//120*120
			if(normalPreview)
				this.removeChild(normalPreview);
			normalPreview = new Bitmap(new BitmapData(118,118),'never',true);
			normalPreview.x = 575;
			normalPreview.y = 54;
			addChild(normalPreview);
			
			//48*48
			if(smallPreview)
				this.removeChild(smallPreview);
			smallPreview = new Bitmap(new BitmapData(46,46),'never',true);
			smallPreview.x = 575;
			smallPreview.y = 250;
			addChild(smallPreview);
			
			//载入到窗口
			if(editwindow){
				this.removeChild(editwindow);
				editwindow = null;
			}
			
			//主图显示区域
			imageCanvas = new Rectangle(0,54,299,299);
			editwindow = new EditWindow(
				bitmap,
				imageCanvas, 
				bigPreview,
				smallPreview, 
				normalPreview, 
				slider,
				isGIF
			);
			addChild(editwindow);
		}
		//切换到摄像头界面
		private function openCamera(e:MouseEvent):void
		{
			this.gotoAndStop(3);
			localUploadButton.addEventListener(MouseEvent.CLICK,swichLocalUpload);//更换实例要得新绑定

			if(bigPreview) {
				//隐藏第一帧里的三个预览框
				bigPreview.visible = false;
				smallPreview.visible = false;
				normalPreview.visible = false;
			}
			//如果是从第二个编辑框点过来，编辑框移除
			if(editwindow) {
				removeChild(editwindow);
				editwindow = null;
			}
			if(video) {
				return;
			}
			if (!Camera.isSupported) {
                return;
            }
			cameraLoading.visible = true;
			camera = Camera.getCamera();
			
			if (camera == null) {
				cameraLoading.visible = false;
                return;
            }
			cameraErrorInfo.visible = false;
			//创建显示摄像头的容器
			
			video = new Video(400, 320);
			
			// 把视频放进去
			//video.smoothing = true;
			camera.setQuality(0, 100);
			camera.setMode(400, 320, 24, false);
			video.attachCamera(camera);
            video.x = 160;
            video.y = 50;
            addChild(video);
			captrueButton.visible = true;
			captrueButton.addEventListener(MouseEvent.CLICK,captureCamera);
			return;
		}
		//摄像头捕获
		private function captureCamera(e:MouseEvent):void
		{
			e.target.visible = false;
            var bitmapData:BitmapData = new BitmapData(video.width, video.height);
            bitmapData.draw(video);
			video.attachCamera(null);
			video.visible = false;
			var bitmap:GIFPlayer = new GIFPlayer(true);
			bitmap.bitmapData = bitmapData;
			/*var bit:BitmapData=new BitmapData(video.width,video.height);
			bit.draw(video);
			var bitmap:Bitmap=new Bitmap(bit);*/

			if(editwindow){
				removeChild(editwindow);
				editwindow = null;
			}
			removeChild(video);
			video = null;
			this.gotoAndStop(1);

			localUploadButton.addEventListener(MouseEvent.CLICK,swichLocalUpload);//更换实例要得新绑定
			cameraButton.addEventListener(MouseEvent.CLICK,openCamera);//更换实例要得新绑定
			
			gotoEditWindow(bitmap,false);
		}
		//切换到本地上传模式
		private function swichLocalUpload(e:MouseEvent):void
		{
			if(video) {
				video.attachCamera(null);
				removeChild(video);
				video = null;
			}
			if(editwindow){
				removeChild(editwindow);
				editwindow = null;
			}
			
			this.gotoAndStop(1);
			if(bigPreview) {
				bigPreview.visible = false;
				smallPreview.visible = false;
				normalPreview.visible = false;
			}

			cameraButton.removeEventListener(MouseEvent.CLICK,openCamera);
			cameraButton.addEventListener(MouseEvent.CLICK,openCamera);//更换实例要得新绑定

			getimg.removeEventListener(MouseEvent.CLICK,upload);
			getimg.addEventListener(MouseEvent.CLICK, upload);//点击选择按钮
			return;
		}
		private function zoomInHandler(e:MouseEvent):void
		{
			editwindow.zoomIn();
		}
		private function zoomOutHandler(e:MouseEvent):void
		{
			editwindow.zoomOut();
		}
		private function clockwiseHandler(e:MouseEvent):void
		{
			editwindow.clockwise();
		}
		private function anticlockwiseHandler(e:MouseEvent):void
		{
			editwindow.anticlockwise();
		}
		/**
		 * 选择图片
		 */
		private function upload(e:MouseEvent):void
		{	
			hideTip();
			var filetype:Array = [new FileFilter('All Image Files(*.jpg;*.jpeg;*.gif;*.png)','*.jpg;*.jpeg;*.gif;*.png')];
			file.browse(filetype);
		}
		/**
		 * 载入本地的图
		 */
		private function loadFile(e:Event):void
		{
			if (file.type == '.gif'){
				isGIF = true;
				//隐藏两个按钮
				loader.loadBytes(file.data);
			}else{
				isGIF = false;
				loader.loadBytes(file.data);
			}
			
		}
		/***
		 * 选择文件
		 */
		private function selectHandler(e:Event):void
		{
			file.load();
			save && (save.enabled = true);
		}
		/**
		 * 取消选择
		 */
		private function cancelHandler(e:Event):void
		{
		}
		
		/***
		 * 上传图片
		 */
		private function saveImg(e:MouseEvent):void
		{
			var jpgeEncoder_:JPGEncoder;
			var bit2:ByteArray;
			if(isGIF){
				bit2 = editwindow.outputGIF();
			} else {
				jpgeEncoder_ = new JPGEncoder(90);
				bit2 = jpgeEncoder_.encode(bigPreview.bitmapData);
			}
			
			//var saveFace:String = stage.loaderInfo.parameters.postAction as String;
			var mvParams:Object = stage.loaderInfo.parameters; 
			
			var uid:String = String(mvParams["uid"]);
			var token:String = String(mvParams["token"]);
			
			var _requestURL:String = String(mvParams["requestURL"]);
			
			var queryStr_:String = "uid=" + uid +"&token=" + token;
			var urlVariables_:URLVariables = new URLVariables(queryStr_);
           	var urlRequest_:URLRequest = new URLRequest(_requestURL);
            urlRequest_.method = URLRequestMethod.POST;
            urlRequest_.data = urlVariables_;
            
            urlRequest_.contentType = "multipart/form-data; boundary=" + UploadPostHelper.getBoundary();
            urlRequest_.data = UploadPostHelper.getPostData("image.jpg", bit2, urlVariables_);
            
			var _urlLoader:URLLoader = new URLLoader();
			_urlLoader.dataFormat = URLLoaderDataFormat.BINARY;
            
            _urlLoader.addEventListener(Event.COMPLETE, onUploadComplete);
            _urlLoader.addEventListener(IOErrorEvent.IO_ERROR, onIOError);
            _urlLoader.load(urlRequest_);
		}
		
		protected function onIOError(event_:IOErrorEvent):void
        {
            //dispatchEvent(new ErrorEvent(ErrorEvent.ERROR, false, false, "图片保存失败12，网络连接错误！"));
			//ExternalInterface.call("Wind.Util.resultTip({error : true,msg : '图片保存失败,网络连接错误!'})");
			showTip('图片保存失败,网络连接错误!');
        }
        
        protected function onUploadComplete(event_:Event):void
        {
        	showTip('上传成功');
        	/*
        	dispatchEvent(new Event(Event.COMPLETE));
			var url:String = "javascript:uploadSuccess();void(0);";
			var urlRequest_:URLRequest = new URLRequest(url);
			//var header:URLRequestHeader=new URLRequestHeader("charset","utf-8");
			//urlRequest_.requestHeaders.push(header);
			navigateToURL(urlRequest_,'_self');
        	*/
        }


        private function errorHandlers(e:IOErrorEvent):void
		{
			showTip(Crop.IO_ERROR);
		}
		
		//显示错误信息
		private function showTip(txt:String):void
		{
			warning.text = txt; 
			warning.visible = true;
		}
		
		// 隐藏错误信息
		private function hideTip():void
		{
			warning.visible = false;
		}
		/*private function errorHandlers(e:IOErrorEvent):void
		{
			showTip(Crop.IO_ERROR);
		}
		
		//显示错误信息
		private function showTip(txt:String):void
		{
			if(this.currentFrame == 1)
			{
				warning.text = txt; 
				warning.visible = true;
			}else{
				tip.slider.warning.text = txt;
				tip.play();
				save.enabled = false;
			}
		}
		
		// 隐藏错误信息
		private function hideTip():void
		{
			if(this.currentFrame == 1)
			{
				warning.visible = false;
			}else{
				tip.gotoAndStop(0);
			}
		}**/
	}
}