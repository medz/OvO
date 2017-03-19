<?php

/**
 * 用户消息配置dao.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwMessageConfigDao extends PwBaseDao
{
    protected $_pk = 'uid';
    protected $_table = 'message_config';
    protected $_dataStruct = ['uid', 'privacy', 'notice_types'];

    /**
     * 获取用户消息配置.
     *
     * @param int $uid
     *
     * @return bool
     */
    public function getMessageConfig($uid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE uid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$uid]);
    }

    /**
     * 获取用户消息配置.
     *
     * @param array $uids
     *
     * @return array
     */
    public function fetchMessageConfig($uids)
    {
        return $this->_fetch($uids);
    }

    /**
     * 用户配置.
     *
     * @param array $data
     *
     * @return int
     */
    public function setMessageConfig($data)
    {
        if (!($data = $this->_filterStruct($data))) {
            return false;
        }
        $data = [
            'uid'          => $data['uid'],
            'privacy'      => $data['privacy'],
            'notice_types' => $data['notice_types'],
        ];
        $sql = $this->_bindSql('REPLACE INTO %s SET %s ', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }
}
