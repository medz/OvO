package com.phpwind.avatar
{
	import com.phpwind.util.ResizingCursor;
	
	import flash.display.*;
	import flash.events.*;
	import flash.geom.*;
	import flash.ui.Mouse;
	import flash.ui.MouseCursor;
	public class DragBox extends Sprite
	{
		private var bg:Sprite;
		private var dragBoundary:Rectangle;
		private var gripper:Gripper;
		public var isDragging:Boolean = false;
		private var isResizing:Boolean = false;
		private var offX:Number;
		private var offY:Number;
		/**鼠标指针 **/
		private var resizingCursor:ResizingCursor = new ResizingCursor(8,8);
		private var cursorName:String = 'normal';
		private var cursor:Bitmap;
		
		public static var MOVE:String = "moving";
		public static var START_RESIZE:String = "startResize";
		public static var STOPMOVE:String = "stop";
		public static var RESIZE:String = "resizing";
		public static var START_MOVE:String = "startMove";
		
		public function DragBox(dragBoundary:Rectangle):void
		{
			this.dragBoundary = dragBoundary;
			this.x=dragBoundary.x;
			this.y=dragBoundary.y;
			this.addEventListener(Event.ADDED_TO_STAGE, onStage);
		}
		/**
		 * 载入场景中后的初始化
		 */
		private function onStage(e:Event):void
		{
			removeEventListener(Event.ADDED_TO_STAGE, onStage);
			bg = new Sprite();
			drawBox();
			addChild(bg);
			if(!gripper)
			{
				gripper = new Gripper();
				gripper.x=gripper.y=this.width-gripper.width-1;
				addChild(gripper);
			}
			addEventListener(MouseEvent.MOUSE_DOWN,onBgMouseDown);
			addEventListener(MouseEvent.MOUSE_OVER,onBgMouseOver);
			addEventListener(MouseEvent.MOUSE_OUT,onBgMouseOut);
			gripper.addEventListener(MouseEvent.MOUSE_DOWN, onRectDown);
			gripper.addEventListener(MouseEvent.MOUSE_UP, onRectUp);
			gripper.addEventListener(MouseEvent.MOUSE_OVER,onResizingOver);
			gripper.addEventListener(MouseEvent.MOUSE_OUT,onResizingOut);
		}
		/**
		 * 转换鼠标
		 */
		private function onBgMouseOver(e:MouseEvent):void
		{
			if(!this.isResizing)
				Mouse.cursor = MouseCursor.HAND;
		}
		private function onBgMouseOut(e:MouseEvent):void
		{
			Mouse.cursor = MouseCursor.AUTO;
		}
		private function onResizingOver(e:MouseEvent):void
		{
			e.stopPropagation();
			if(this.cursorName == 'normal')
			{
				cursor=new Bitmap(resizingCursor,'never',true);
				cursor.x=e.stageX;
				cursor.y=e.stageY;
				Mouse.hide();
				stage.addChild(cursor);
				stage.addEventListener(MouseEvent.MOUSE_MOVE,moveMouse);
				this.cursorName = 'resizing';
			}
		}
		private function moveMouse(e:MouseEvent):void {
			e.stopPropagation();
			cursor.x=e.stageX;
			cursor.y=e.stageY;
		}
		private function onResizingOut(e:MouseEvent):void
		{
			e.stopPropagation();
			if(!this.isResizing && this.cursorName=='resizing' )
			{
				stage.removeChild(cursor);
				stage.removeEventListener(MouseEvent.MOUSE_MOVE,moveMouse);
				Mouse.show();
				if(this.hitTestPoint(e.stageX,e.stageY))
					Mouse.cursor = MouseCursor.HAND;
				this.cursorName = 'normal';
			}
		}
		/**
		 * 绘制边框
		 */
		private function drawBox():void
		{
			bg.graphics.clear();
			bg.graphics.beginFill(0, 0);
			bg.graphics.lineStyle(1, 0x00ffffff);
			bg.graphics.drawRect(0, 0, this.dragBoundary.width, this.dragBoundary.height);
			bg.graphics.endFill();
		}
		/**
		 * 拖动
		 */
		public function onBgMouseDown(e:MouseEvent):void
		{
			isDragging = true;
			this.dispatchEvent(new Event(START_MOVE));
			var rect:Rectangle = new Rectangle(0, 0, 298-width, 298-height);
			this.startDrag(false, rect);
			stage.addEventListener(MouseEvent.MOUSE_UP, dragStop);
		}
		public function dragStop(e:MouseEvent):void
		{
			/*if (!this.bg.hitTestPoint(event.stageX, event.stageY))
			{
				CustomCursor.getInstance().showNormal();
			}*/
			this.stopDrag();
			stage.removeEventListener(MouseEvent.MOUSE_UP, dragStop);
			this.dispatchEvent(new Event(STOPMOVE));
			this.isDragging = false;
		}
		/**
		 * 拉伸
		 */
		private function onRectDown(event:MouseEvent) : void
		{
			event.stopPropagation();
			this.isResizing = true;
			this.dispatchEvent(new Event(START_MOVE));
			
			this.offX = event.target.width - event.target.mouseX;
			this.offY = event.target.height - event.target.mouseY;
			
			stage.addEventListener(MouseEvent.MOUSE_MOVE, resizing);
			stage.addEventListener(MouseEvent.MOUSE_UP, onRectUp);
		}
		
		private function onRectUp(event:MouseEvent) : void
		{
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, resizing);
			stage.removeEventListener(MouseEvent.MOUSE_UP, onRectUp);
			this.dispatchEvent(new Event(STOPMOVE));
			this.isResizing = false;
			onResizingOut(event);
		}
		private function resizing(event:MouseEvent) : void
		{
			if((this.width+this.x)>297||(this.height+this.y)>297)
			{
				this.dispatchEvent(new Event(STOPMOVE));
				this.onRectUp(event);
			}
			if (event.stageX > this.x && event.stageY > this.y)
			{
				dragBoundary.width = dragBoundary.height = Math.max(50,mouseX+offX,mouseY+offY);
				drawBox();
				gripper.x = dragBoundary.width-gripper.width-1;
				gripper.y = dragBoundary.height-gripper.height-1;
			}
		}
		public function rewriteBox(w:Number):void
		{
			dragBoundary.width = dragBoundary.height = w;
			drawBox();
			gripper.x = dragBoundary.width-gripper.width-1;
			gripper.y = dragBoundary.height-gripper.height-1;
		}
	}
}