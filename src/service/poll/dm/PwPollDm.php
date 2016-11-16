<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 投票基础表数据模型
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPollDm.php 4308 2012-02-15 02:55:41Z mingxing.sun$
 * @package poll
 */

class PwPollDm extends PwBaseDm
{
    public $poll_id = 0;

    public function __construct($pollid = 0)
    {
        $this->poll_id = $pollid;
    }

    /**
     * 设置投票个数限制
     *
     * @param int $mostVotes
     *                       return object
     */
    public function setOptionLimit($optionLimit)
    {
        $optionLimit = intval($optionLimit);
        $this->_data['option_limit'] = $optionLimit ? $optionLimit : 1;

        return $this;
    }

    /**
     * 设置是否投票后才能查看结果
     *
     * @param  int    $isViewResult
     * @return object
     */

    public function setIsViewResult($isViewResult)
    {
        $this->_data['isafter_view'] = intval($isViewResult);

        return $this;
    }

    public function setIsIncludeImg($isIncludeImg)
    {
        $this->_data['isinclude_img'] = intval($isIncludeImg);

        return $this;
    }

    /**
     * 设置投票发起者UID
     *
     * @param  int    $createdUserid
     * @return object
     */
    public function setCreatedUserid($createdUserid)
    {
        $this->_data['created_userid'] = intval($createdUserid);

        return $this;
    }

    /**
     * 设置注册时间限制
     *
     * @param  string $regtimeLimit
     * @return object
     */
    public function setRegtimeLimit($regtimeLimit)
    {
        $this->_data['regtime_limit'] = $regtimeLimit;

        return $this;
    }

    /**
     * 设置有效时间
     *
     * @param int $validtime
     *                       return object
     */
    public function setExpiredTime($expiredTime)
    {
        $this->_data['expired_time'] = intval($expiredTime);

        return $this;
    }

    /**
     * 设置APP应用扩展类别
     *
     * @param  int    $appType
     * @return object
     */
    public function setAppType($appType)
    {
        $this->_data['app_type'] = $appType;

        return $this;
    }

    /**
     * 设置投票人数
     *
     * @param  int    $voterNum
     * @return object
     */
    public function setVoterNum($voterNum)
    {
        $this->_data['voter_num'] = $voterNum;

        return $this;
    }

    protected function _beforeAdd()
    {
        $this->_data['created_time'] = Pw::getTime();

        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
