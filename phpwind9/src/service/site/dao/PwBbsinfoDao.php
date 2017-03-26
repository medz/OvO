<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 论坛信息.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwBbsinfoDao.php 21328 2012-12-04 11:32:35Z jieyin $
 */
class PwBbsinfoDao extends PwBaseDao
{
    protected $_table = 'bbsinfo';
    protected $_pk = 'id';
    protected $_dataStruct = ['id', 'newmember', 'totalmember', 'higholnum', 'higholtime', 'yposts', 'hposts'];

    public function get($id)
    {
        return $this->_get($id);
    }

    public function update($id, $fields, $increaseFields = [])
    {
        return $this->_update($id, $fields, $increaseFields);
    }
}
