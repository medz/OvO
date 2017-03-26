package com.phpwind.avatar
{
	import com.phpwind.avatar.DragBox;
	
	import fl.controls.Slider;
	import com.phpwind.avatar.IEditWindow;
	
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.BlendMode;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Matrix;
	import flash.geom.Rectangle;
	import flash.ui.Mouse;
	
	import org.bytearray.gif.player.GIFPlayer;

	public class GIFEditWindow extends Sprite implements IEditWindow
	{
		private var maskBlock:Sprite;
		public var photoSp:GIFPlayer;
		private var photoContainer:Sprite;
		private var photoMask:Sprite;
		private var maskContainer:Sprite;
		private var alphaBg:Sprite;
		private var sideLength:uint;
		private var dragBox:DragBox;
		private var scaleMax:Number;
		private var scaleMin:Number;
		private var smallPreview:Bitmap;
		private var normalPreview:Bitmap;
		private var slider:Slider;
		public function GIFEditWindow(bitmap:GIFPlayer, rect:Rectangle, small:Bitmap, normal:Bitmap, slider:Slider)
		{
			this.smallPreview = small;
			this.normalPreview = normal;
			this.slider  = slider;
			this.photoSp = bitmap;
			this.x = rect.x;
			this.y = rect.y;
			this.sideLength = rect.width;
			this.addEventListener(Event.ADDED_TO_STAGE, onStage);
		}
		private function drawSprite(sideLength:uint, color:uint, alpha:Number=1.0):Sprite
		{
			var sprite:Sprite = new Sprite();
			sprite.graphics.beginFill(color,alpha);
			sprite.graphics.drawRect(0, 0, sideLength, sideLength);
			sprite.graphics.endFill();
			return sprite;
		}
		//载入图片，画出遮罩
		private function onStage(e:Event):void
		{
			this.removeEventListener(Event.ADDED_TO_STAGE, onStage);
			//处理图层
			photoContainer = new Sprite();
			//photoSp = new Bitmap(photoBitMapData, 'auto', true);
			photoContainer.addChild(photoSp);
			
			//计算最大最小的scaleX
			calculateZoom();
			
			//处理遮罩层
			photoMask = drawSprite(sideLength, 0x00000000);
			photoContainer.mask = photoMask;
			addChild(photoContainer);
			addChild(photoMask);
			
			//处理蒙板层
			maskContainer = new Sprite();
			alphaBg = drawSprite(sideLength, 0x00000000, 0.5);
			maskBlock = drawSprite(Math.min(100, photoSp.width, photoSp.height), 0x00000000);
			maskBlock.blendMode = BlendMode.ERASE;
			maskContainer.addChild(alphaBg);
			maskContainer.addChild(maskBlock);
			maskContainer.blendMode = BlendMode.LAYER;
			addChild(maskContainer);
			
			//设置位置
			maskBlock.x=maskBlock.y=150-maskBlock.width/2;
			//画出dragbox
			if(!dragBox)
			{
				dragBox = new DragBox(maskBlock.getBounds(this));
				addChild(dragBox);
				dragBox.addEventListener(DragBox.START_MOVE, onMoveStart);
				dragBox.addEventListener(DragBox.STOPMOVE, onMoveStop);
			}
			movePhoto();
			setHeadPic();
		}
		/**
		 * 初始化缩放
		 */
		private function calculateZoom():void
		{
			if(photoSp.width>sideLength && photoSp.height>sideLength)//大图
			{
				scaleMax=1.0;
				scaleMin=sideLength/Math.max(photoSp.width,photoSp.height);
				photoSp.scaleX= photoSp.scaleY = sideLength/Math.min(photoSp.width,photoSp.height);
			}
			else if(photoSp.width<sideLength && photoSp.height<sideLength)//小图
			{
				scaleMax = sideLength/Math.min(photoSp.width,photoSp.height);
				scaleMin = 1.0;
			}
			else
			{
				scaleMax = sideLength/Math.min(photoSp.width,photoSp.height);
				scaleMin = sideLength/Math.max(photoSp.width,photoSp.height);
			}
			slider.minimum = scaleMin*100;
			slider.maximum = scaleMax*100;
			slider.value = photoSp.scaleX*100;
			slider.addEventListener(Event.CHANGE, sliderZoom);
		}
		private function onMove(event:MouseEvent) : void
		{
			adjustMask();
			movePhoto();
			setHeadPic();
			//判断是否已经超出边界
			var rect:Rectangle = dragBox.getRect(stage);
			if( (photoSp.hitTestPoint(rect.x,rect.y) && 
				photoSp.hitTestPoint(rect.x+rect.width,rect.y) && 
				photoSp.hitTestPoint(rect.x,rect.y+rect.height) && 
				photoSp.hitTestPoint(rect.x+rect.width,rect.y+rect.height)) == false )
			{
				if(photoSp.width>photoSp.height){
					photoSp.height = rect.width;
					photoSp.scaleX = photoSp.scaleY;
				}else{
					photoSp.width = rect.height;
					photoSp.scaleY = photoSp.scaleX;
				}
				slider.value = photoSp.scaleX*100;
			}
		}
		/**
		 * 让洞对准拖拽框
		 */
		private function adjustMask() : void
		{
			maskBlock.x = dragBox.x;
			maskBlock.y = dragBox.y;
			maskBlock.width = dragBox.width;
			maskBlock.height = dragBox.height;
			return;
		}
		/**
		 *	开始拖拽
		 */
		private function onMoveStart(event:Event) : void
		{
			stage.addEventListener(MouseEvent.MOUSE_MOVE, onMove);
		}// end function
		/**
		 * 停止拖拽
		 */
		private function onMoveStop(event:Event) : void
		{
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, onMove);
			setHeadPic();
			//adjustMask();
			//dragBox.isDragging && movePhoto();
			//this.snapp(null);
		}
		/**
		 * 移动图片
		 */
		private function movePhoto() : void
		{
			photoSp.x=(sideLength-photoSp.width)*dragBox.x/(sideLength-dragBox.width);
			photoSp.y=(sideLength-photoSp.height)*dragBox.y/(sideLength-dragBox.height);
		}
		/**
		 * 放大图片
		 */
		public function zoomIn():void
		{
			var r:Number = Math.min(photoSp.scaleX+0.05*(scaleMax-scaleMin),scaleMax);
			photoSp.scaleY=photoSp.scaleX = r;
			movePhoto();
			setHeadPic();
			slider.value = r*100;
		}
		/**
		 * 缩小图片
		 */
		public function zoomOut():void
		{
			var r:Number = Math.max(photoSp.scaleX+0.05*(scaleMin-scaleMax),scaleMin);
			photoSp.scaleY=photoSp.scaleX = r;
			reScaledragBox();
			movePhoto();
			setHeadPic();
			slider.value = r*100;
		}
		/**
		 * 顺时针旋转
		 */
		public function clockwise():void
		{
			var matrix:Matrix = new Matrix(0,1,-1,0,photoSp.bitmapData.height,0);
			var newBitmapData:BitmapData = new BitmapData(photoSp.bitmapData.height,photoSp.bitmapData.width);
			newBitmapData.draw(photoSp.bitmapData,matrix,null,null,null,true);
			photoSp.bitmapData = newBitmapData;
			//交换x,y;
			//photoSp.y=photoSp.x^photoSp.y^(photoSp.x=photoSp.y);
			movePhoto();
			setHeadPic();
		}
		/**
		 * 逆时针旋转
		 */
		public function anticlockwise():void
		{
			var matrix:Matrix = new Matrix(0,-1,1,0,0,photoSp.bitmapData.width);
			var newBitmapData:BitmapData = new BitmapData(photoSp.bitmapData.height,photoSp.bitmapData.width);
			newBitmapData.draw(photoSp.bitmapData,matrix,null,null,null,true);
			photoSp.bitmapData = newBitmapData;
			//交换x,y;
			//photoSp.y=photoSp.x^photoSp.y^(photoSp.x=photoSp.y);
			movePhoto();
			setHeadPic();
		}
		/**
		 * 拉动slider
		 */
		private function sliderZoom(e:Event):void
		{
			photoSp.scaleY=photoSp.scaleX = slider.value/100;
			reScaledragBox();
			movePhoto();
			setHeadPic();
		}
		/**
		 * 生成头像
		 */
		private function setHeadPic():void
		{
			var smallMatrix:Matrix = new Matrix();
			smallMatrix.a = smallMatrix.d = 48*photoSp.scaleX/dragBox.width;
			smallMatrix.tx = (photoSp.x-dragBox.x)*48/dragBox.width;
			smallMatrix.ty = (photoSp.y-dragBox.y)*48/dragBox.height;
			smallPreview.bitmapData.draw(photoSp.bitmapData,smallMatrix,null,null, new Rectangle(0,0,48,48),true);
			
			var normalMatrix:Matrix = new Matrix();
			normalMatrix.a = normalMatrix.d = 120*photoSp.scaleX/dragBox.width;
			normalMatrix.tx = (photoSp.x-dragBox.x)*120/dragBox.width;
			normalMatrix.ty = (photoSp.y-dragBox.y)*120/dragBox.height;
			normalPreview.bitmapData.draw(photoSp.bitmapData,normalMatrix,null,null, new Rectangle(0,0,120,120),true);
		}
		
		/**
		 * 重绘拖拽框大小
		 */
		private function reScaledragBox():void
		{
			var m:Number = Math.min(photoSp.width, photoSp.height);
			if(dragBox.width>m){
				dragBox.rewriteBox(m);
				adjustMask();
			}
		}
	}
}