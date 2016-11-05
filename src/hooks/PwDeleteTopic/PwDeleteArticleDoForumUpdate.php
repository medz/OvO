<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:process.iPwGleanDoHookProcess');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteArticleDoForumUpdate.php 23312 2013-01-08 07:59:39Z jieyin $
 * @package forum
 */

class PwDeleteArticleDoForumUpdate extends iPwGleanDoHookProcess
{
    public $record = array();

    public function gleanData($value)
    {
        if ($value['disabled'] != 2) {
            $fid = $value['fid'];
            isset($this->record[$fid]) || $this->record[$fid] = array('topic' => 0, 'replies' => 0, 'tids' => array());
            $this->record[$fid]['replies'] += $value['replies'];
            $value['disabled'] == 0 && $this->record[$fid]['topic']++;
            $this->record[$fid]['tids'][] = $value['tid'];
        }
    }

    public function run($ids)
    {
        $forums = Wekit::load('forum.PwForum')->fetchForum(array_keys($this->record), PwForum::FETCH_STATISTICS);
        $srv = Wekit::load('forum.srv.PwForumService');
        foreach ($this->record as $fid => $value) {
            $lastinfo = array();
            if ($forums[$fid]['lastpost_tid'] && in_array($forums[$fid]['lastpost_tid'], $value['tids'])) {
                $lastinfo = $this->_getLastinfo($fid);
            }
            $srv->updateStatistics($fid, -$value['topic'], -$value['replies'], 0, $lastinfo);
        }
    }

    protected function _getLastinfo($fid)
    {
        $thread = Wekit::load('forum.PwThread')->getThreadByFid($fid, 1);
        if (empty($thread)) {
            return array('tid' => 0, 'username' => '', 'subject' => '', 'time' => 0);
        }
        $thread = current($thread);

        return array(
            'tid' => $thread['tid'],
            'username' => $thread['lastpost_username'],
            'subject' => ($thread['replies'] ? 'Re:' : '').$thread['subject'],
            'time' => $thread['lastpost_time'],
        );
    }
}
