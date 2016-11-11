<?php
defined('WEKIT_VERSION') or exit(403);

/**
 * App_XmReplyForLz_CommonDao - dao
 *
 * @author 蝦米 <>
 * @copyright
 * @license
 */
class App_XmReplyForLz_CommonDao extends PwBaseDao
{

    protected $_table = 'bbs_threads_content';
    protected $_pk = 'tid';
    protected $_dataStruct = array('tid', 'use_reply_for_lz');

    public function update($id, $fields)
    {
        return $this->_update($id, $fields);
    }

}