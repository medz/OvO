<?php

/**
 * 奖励扩展点的接口
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskRewardDoBase.php 16435 2012-08-23 09:18:44Z xiaoxia.xuxx $
 * @package src.service.mission.srv.do
 */
abstract class PwTaskRewardDoBase {
	/**
	 * PwTaskGainReward
	 *
	 * @var PwTaskGainReward
	 */
	protected $srv = null;
	
	protected $rewardType = '';
	
	/**
	 * 创建构造函数
	 *
	 * @param PwTaskGainReward $srv
	 */
	public function __construct(PwTaskGainReward $srv = null) {
		$this->srv = $srv;
	}
	
	/**
	 * 奖励获取
	 * 
	 * @param int $uid 用户ID
	 * @param array $reward 奖励设置
	 * @param string $taskname 任务名字
	 * @return boolean
	 */
	public function gainReward($uid, $reward, $taskname) {}
	
	/**
	 * 检查奖励
	 * <note>
	 * 返回的奖励里必须设置有type=> 配置的奖励扩展  比如credit扩展 则type=credit 
	 * 如上：credit和taskExtends里配置的Reward里的key一致
	 * </note>
	 *
	 * @param array $reward
	 * @return array
	 */
	public function checkReward($reward) {
		$_tmp = $reward;
		$keys = explode('-', $_tmp['key']);
		$values = explode('-', $_tmp['value']);
		$descript = $_tmp['descript'];
		unset($_tmp['key'], $_tmp['value'], $_tmp['descript'], $_tmp['type']);
		$var = array_merge(array_combine($keys, $values), $_tmp);
		
		$search = array();
		foreach ($var as $key => $val) {
			$search[] = '{' . $key . '}';
		}
		$reward['descript'] = str_replace($search, array_values($var), $descript);
		return $reward;
	}
}