<?php
require_once 'windid/src/windid/WindidApi.php';
require_once 'windid/src/windid/service/client/bo/WindidClientBo.php';
require_once 'windid/src/windid/library/WindidUtility.php';


$notify = array(
	'999'=>'test',   		//通讯测试接口
	'101'=>'addUser',		//注册用户
	'111'=>'synLogin',		//同步登录
	'112'=>'synLogout',		//同步登出
	'201'=>'editUser',		//编辑用户基本信息(用户名，密码，邮箱，安全问题)
	'202'=>'editUserInfo',  //编辑用户详细资料
	'203'=>'uploadAvatar',  //上传头像
	'211'=>'editCredit', 	//编辑用户积分
	'222'=>'editMessageNum', //同步用户未读私信
	'301'=>'deleteUser',	//删除用户
);

//check
$_windidkey = getInput('windidkey', 'get');
$_time = (int)getInput('time', 'get');
$_clentid = (int)getInput('clientid', 'get');
WindidClientBo::getInstance();
$client = Windid::client();
if (WindidUtility::appKey($client->clientId, $_time, $client->clientKey, $_GET, $_POST) != $_windidkey)  $this->showError('fail');
$time = Windid::getTime();
if ($time - $_time > 120) showError('timeout');


$operation = (int)getInput('operation', 'get');
$uid = (int)getInput('uid', 'get');
if (!$uid) showError('fail');
if (!isset($notify[$operation])) showError('fail');
$method = $notify[$operation];
$notify = new notify();
if(!method_exists($notify, $method)) showError('fail');
$result = $notify->$method($uid);
if ($result == true) showMessage('seccess');
showError('fail');



function getInput($key, $method = 'get') {
	switch ($method) {
	   case 'get':
		  return isset($_GET[$key]) ? $_GET[$key] : null;
	   case 'post':
		  return isset($_POST[$key]) ? $_POST[$key] : null;	  
	   default:
			return null;
	}
}


function showError($message = '', $referer = '', $refresh = false) {
	echo $message;
	exit();
}

function showMessage($message = '', $referer = '', $refresh = false) {
	echo $message;
	exit();
}


class notify{
	
	public function test($uid) {
		return $uid ? true : false;
	}
	
	
	public function addUser($uid) {
		$api = WindidApi::api('user');
		$user = $api->getUser($uid);
		//你系统的   addUser($user);
		return true;
	}
	
	public function editUser($uid) {
		$api = WindidApi::api('user');
		$user = $api->getUser($uid);
		//你系统的   editUser($user);
		return true;
	}

	


}
?>