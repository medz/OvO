<?php
Wind::import('SRV:user.PwUser');
/**
 * 帮助类
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserHelper.php 21282 2012-12-04 03:25:54Z xiaoxia.xuxx $
 * @package products.u.srv
 */
class PwUserHelper {
	
	/**
	 * 获得登录类型
	 * 
	 * @return array
	 */
	public static function getLoginType() {
		return array(1 => 'UID', 2 => '电子邮箱', 3 => '用户名', 4 => '手机号码');
	}
	
	/**
	 * 获得登录框中的登录方式的提示信息
	 * 
	 * @return string
	 */
	public static function getLoginMessage() {
		$config = Wekit::C('login', 'ways');
		$message = array();
		$ways = self::getLoginType();
		foreach ($config as $id) {
			$message[] = $ways[$id];
		}
		return implode('/', $message);
	}
	
	/** 
	 * 获得出生日期的时间距离
	 * 
	 * @return array  array(array(year), array(month), array(day))
	 */
	public static function getBirthDay() {
		$tyear = date('Y', Pw::getTime());
		$year = range($tyear, $tyear - 100, -1);
		$month = range(1, 12, 1);
		$day = range(1, 31);
		return array($year, $month, $day);
	}
	
	/** 
	 * 注册中需要附加的字段映射表
	 *
	 * @return array
	 */
	public static function getRegFieldsMap() {
		$fields = array(
			'location' => array('title' => '现居住地', 'segment' => 'area'),
			'hometown' => array('title' => '家乡', 'segment' => 'area'),
			'mobile' => array('title' => '手机', 'segment' => 'input'),
			'qq' => array('title' => 'QQ', 'segment' => 'input'),
			'msn' => array('title' => 'MSN', 'segment' => 'input'),
			'aliww' => array('title' => '阿里旺旺', 'segment' => 'input')
		);
		return $fields;
	}
	
	/** 
	 * 判断获得密码强度
	 *
	 * @param string $pwd 密码强度
	 * @return int 返回强度级别：(1：弱,2: 一般, 3： 强, 4：非常强)
	 */
	public static function checkPwdStrong($pwd) {
		$array = array();
		$len = strlen($pwd);
		$i = 0;
		$mode = array('a' => 0, 'A' => 0, 'd' => 0, 'f' => 0);
		while ($i < $len) {
			$ascii = ord($pwd[$i]);
			if ($ascii >= 48 && $ascii <= 57) //数字 
				$mode['d'] ++;
			elseif ($ascii >= 65 && $ascii <= 90) //大写字母 
				$mode['A'] ++;
			elseif ($ascii >= 97 && $ascii <= 122) //小写 
				$mode['a'] ++;
			else
				$mode['f'] ++;
			$i ++;
		}
		/*全是小写字母或是大写字母或是字符*/
		if ($mode['a'] == $len || $mode['A'] == $len || $mode['f'] == $len) {
			return 2;
		}
		/*全是数字*/
		if ($mode['d'] == $len) {
			return 1;
		}
		
		$score = 0;
		/*大小写混合得分20分*/
		if ($mode['a'] > 0 && $mode['A'] > 0) {
			$score += 20;
		}
		/*如果含有3个以内（不包含0和3）数字得分10分,如果包括3个（含3个）以上得分20*/
		if ($mode['d'] > 0 && $mode['d'] < 3 ) {
			$score += 10;
		} elseif ($mode['d'] >= 3) {
			$score += 20;
		}
		/*如果含有一个字符得分10分，含有1个以上字符得分25*/
		if ($mode['f'] == 1) {
			$score += 10;
		} elseif ($mode['f'] > 1) {
			$score += 25;
		}
		/*同时含有：字母和数字 得25分；含有：字母、数字和符号 得30分；含有：大小写字母、数字和符号 得35分*/
		if ($mode['a'] > 0 && $mode['A'] > 0 && $mode['d'] > 0 && $mode['f'] > 0) {
			$score += 35;
		} elseif (($mode['a'] > 0 || $mode['A'] > 0) && $mode['d'] > 0 && $mode['f'] > 0) {
			$score += 30;
		} elseif (($mode['a'] > 0 || $mode['A'] > 0) && $mode['d'] > 0) {
			$score += 25;
		}
		if ($len < 3) $score -= 10;
		if ($score >= 60) {
			return 4;
		} elseif ($score >= 40) {
			return 3;
		} elseif ($score >= 20) {
			return 2;
		}
		return 1;
	}
	
	/** 
	 * 获得安全问题
	 * 
	 * @return array
	 */
	public static function getSafeQuestion() {
		return Wekit::load('SRV:user.srv.PwUserService')->getSafeQuestion();
		/* return array(
			1 => '我爸爸的出生地', 
			2 => '我妈妈的出生地',
			3 => '我的小学校名', 
			4 => '我的中学校名', 
			5 => '我最喜欢的运动', 
			6 => '我最喜欢的歌曲', 
			7 => '我最喜欢的电影', 
			8 => '我最喜欢的颜色'); */
	}
}