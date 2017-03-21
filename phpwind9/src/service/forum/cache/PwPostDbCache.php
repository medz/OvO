<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 回复缓存数据接口.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPostDbCache.php 22207 2012-12-19 17:13:36Z jieyin $
 */
class PwPostDbCache extends PwBaseDbCache
{
    protected $keys = [
        'post'      => ['post_%s', ['pid'], PwCache::USE_DBCACHE, 'forum', 0, ['forum.dao.PwPostsDao', 'getPost']],
        'post_list' => ['post_list_%s_%s_%s_%s_%s', ['tver', 'tid', 'limit', 'offset', 'asc'], PwCache::USE_DBCACHE, 'forum', 0],
        'post_tver' => ['post_tver_%s', ['tid'], PwCache::USE_DBCACHE, 'forum', 0, 0],
    ];

    public function fetchKeysByPid($pids)
    {
        $keys = [];
        foreach ($pids as $pid) {
            $keys[] = ['post', [$pid]];
        }

        return $keys;
    }

    public function getPost($pid)
    {
        return Wekit::cache()->get('post', [$pid]);
    }

    public function fetchPost($pids)
    {
        $result = [];
        $data = Wekit::cache()->fetch($this->fetchKeysByPid($pids));
        foreach ($data as $key => $value) {
            list(, $pid) = explode('_', $key);
            if (isset($result[$pid])) {
                $result[$pid] = array_merge($result[$pid], $value);
            } else {
                $result[$pid] = $value;
            }
        }

        return $result;
    }

    public function getPostByTid($tid, $limit, $offset, $asc)
    {
        $orderby = $asc ? 'ASC' : 'DESC';
        $tver = Wekit::cache()->get('post_tver', [$tid]);
        $data = Wekit::cache()->get('post_list', [$tver, $tid, $limit, $offset, $orderby]);
        if ($data === false) {
            $result = $this->_getDao()->getPostByTid($tid, $limit, $offset, $asc);
            Wekit::cache()->set('post_list', array_keys($result), [$tver, $tid, $limit, $offset, $orderby]);
        } else {
            $result = $this->fetchPost($data);
        }

        return $result;
    }

    public function addPost($fields)
    {
        if ($fields['tid'] && (! isset($fields['disabled']) || $fields['disabled'] == 0)) {
            $this->clearPostListCache($fields['tid']);
        }

        return $this->_getDao()->addPost($fields);
    }

    public function updatePost($pid, $fields, $increaseFields = [])
    {
        if (isset($fields['disabled']) || isset($fields['created_time']) || isset($fields['tid'])) {
            $this->updatePostList($pid, isset($fields['tid']) ? $fields['tid'] : 0);
        }
        Wekit::cache()->delete('post', [$pid]);

        return $this->_getDao()->updatePost($pid, $fields, $increaseFields);
    }

    public function batchUpdatePost($pids, $fields, $increaseFields = [])
    {
        if (isset($fields['disabled']) || isset($fields['created_time']) || isset($fields['tid'])) {
            $this->batchUpdatePostList($pids, isset($fields['tid']) ? $fields['tid'] : 0);
        }
        Wekit::cache()->batchDelete($this->fetchKeysByPid($pids));

        return $this->_getDao()->batchUpdatePost($pids, $fields, $increaseFields);
    }

    public function deletePost($pid)
    {
        $this->updatePostList($pid);
        Wekit::cache()->delete('post', [$pid]);

        return $this->_getDao()->deletePost($pid);
    }

    public function batchDeletePost($pids)
    {
        $this->batchUpdatePostList($pids);
        Wekit::cache()->batchDelete($this->fetchKeysByPid($pids));

        return $this->_getDao()->batchDeletePost($pids);
    }

    /*
    public function revertPost($tids) {
        foreach ($tids as $_tid) {
            $this->clearPostListCache($_tid);
        }
        Wekit::cache()->batchDelete($this->fetchKeysByPid($pids));
        return $this->_getDao()->revertPost($tids);
    }*/

    /**
     * 清除一个帖子的列表缓存.
     *
     * @param int $tid
     */
    public function clearPostListCache($tid)
    {
        Wekit::cache()->increment('post_tver', [$tid]);
    }

    /**
     * 更新一个回复，清除所属帖子的列表缓存.
     *
     * @param int $pid
     * @param int $tid
     */
    public function updatePostList($pid, $tid = 0)
    {
        $post = $this->getPost($pid);
        $this->clearPostListCache($post['tid']);
        if ($tid && $tid != $post['tid']) {
            $this->clearPostListCache($tid);
        }
    }

    /**
     * 更新多个回复时，清除所属帖子的列表缓存.
     *
     * @param array $tids
     * @param int   $fid
     */
    public function batchUpdatePostList($pids, $tid = 0)
    {
        $posts = $this->fetchPost($pids);
        $tids = [];
        foreach ($posts as $post) {
            $tids[] = $post['tid'];
        }
        if ($tid) {
            $tids[] = $tid;
        }
        $tids = array_unique($tids);
        foreach ($tids as $_tid) {
            $this->clearPostListCache($_tid);
        }
    }
}
