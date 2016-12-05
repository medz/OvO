<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:credit.bo.PwCreditBo');
Wind::import('SRV:user.dm.PwUserInfoDm');

/**
 * 帖子删除扩展服务接口--更新用户发帖数，积分等信息.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteTopicDoUserUpdate.php 17512 2012-09-06 04:50:49Z xiaoxia.xuxx $
 */
class PwDeleteTopicDoUserUpdate extends iPwGleanDoHookProcess
{
    public $recode = array();
    private $operatorCredit = true;

    /**
     * over write construct to support the second param.
     *
     * @param PwGleanDoProcess $srv            [description]
     * @param bool             $operatorCredit [description]
     */
    public function __construct($srv)
    {
        parent::__construct($srv);
        $this->operatorCredit = $this->srv->isDeductCredit ? true : false;
    }

    /**
     * collect the data.
     *
     * @param array $value [description]
     */
    public function gleanData($value)
    {
        if ($value['disabled'] != 2) {
            if (!isset($this->recode[$value['created_userid']])) {
                $this->recode[$value['created_userid']] = array('postnum' => 0, 'digest' => 0);
            }
            // use ++ is can't +1
            $this->recode[$value['created_userid']]['postnum'] += 1;
            // use ++ is can't +1
            if ($value['digest']) {
                $this->recode[$value['created_userid']]['digest'] += 1;
            }

            if ($this->operatorCredit) {
                $this->_operateCredit($value);
            }
        }
    }

    /**
     * 积分操作.
     *
     * @param array $value 帖子
     */
    protected function _operateCredit($value)
    {
        Wind::import('SRV:forum.bo.PwForumBo');
        $forum = new PwForumBo($value['fid']);
        PwCreditBo::getInstance()->operate(
            'delete_topic', PwUserBo::getInstance($value['created_userid']), true, array(
                'operator' => $this->srv->user->username,
                'title'    => $value['subject'],
            ),
            $forum->getCreditSet('delete_topic'));
    }

    /**
     * update the user info about thread.
     *
     * @param array $ids [description]
     *
     * @return boid
     */
    public function run($ids)
    {
        if ($this->recode) {
            foreach ($this->recode as $key => $value) {
                $dm = new PwUserInfoDm($key);
                $dm->addPostnum(-$value['postnum']);
                if ($value['digest']) {
                    $dm->addDigest(-$value['digest']);
                }
                Wekit::load('user.PwUser')->editUser($dm, PwUser::FETCH_DATA);
            }
            if ($this->operatorCredit) {
                PwCreditBo::getInstance()->execute();
            }
        }
    }
}
