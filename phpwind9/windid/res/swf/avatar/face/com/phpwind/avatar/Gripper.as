package com.phpwind.avatar
{
	import com.phpwind.avatar.GripperBitmapData;
	
	import flash.display.Bitmap;
	import flash.display.Sprite;

	public class Gripper extends Sprite
	{
		public function Gripper()
		{
			addChild(new Bitmap(new GripperBitmapData(16,16),'never',false));
		}
	}
}