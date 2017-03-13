<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块缓存数据接口.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwForumDbCache.php 21318 2012-12-04 09:24:09Z jieyin $
 */
class PwForumDbCache extends PwBaseMapDbCache
{
    protected $keys = [
        'forum'           => ['forum_%s', ['fid'], PwCache::USE_DBCACHE, 'forum', 0, ['forum.dao.PwForumDao', 'getForum']],
        'forumstatistics' => ['forumstatistics_%s', ['fid'], PwCache::USE_DBCACHE, 'forum', 0, ['forum.dao.PwForumStatisticsDao', 'getForum']],
        'forumextra'      => ['forumextra_%s', ['fid'], PwCache::USE_DBCACHE, 'forum', 0, ['forum.dao.PwForumExtraDao', 'getForum']],
    ];

    public function getKeysByFid($fid)
    {
        $keys = [];
        if ($this->index & PwForum::FETCH_MAIN) {
            $keys[] = ['forum', [$fid]];
        }
        if ($this->index & PwForum::FETCH_STATISTICS) {
            $keys[] = ['forumstatistics', [$fid]];
        }
        if ($this->index & PwForum::FETCH_EXTRA) {
            $keys[] = ['forumextra', [$fid]];
        }

        return $keys;
    }

    public function fetchKeysByFid($fids)
    {
        $keys = [];
        foreach ($fids as $fid) {
            $keys = array_merge($keys, $this->getKeysByFid($fid));
        }

        return $keys;
    }

    public function getForum($fid)
    {
        $data = Wekit::cache()->fetch($this->getKeysByFid($fid));
        $result = [];
        foreach ($data as $key => $value) {
            $result = array_merge($result, $value);
        }

        return $result;
    }

    public function fetchForum($fids)
    {
        $result = [];
        $data = Wekit::cache()->fetch($this->fetchKeysByFid($fids));
        foreach ($data as $key => $value) {
            list(, $fid) = explode('_', $key);
            if (isset($result[$fid])) {
                $result[$fid] = array_merge($result[$fid], $value);
            } else {
                $result[$fid] = $value;
            }
        }

        return $result;
    }

    public function updateForum($fid, $fields, $increaseFields = [])
    {
        Wekit::cache()->batchDelete($this->getKeysByFid($fid));

        return $this->_getDao()->updateForum($fid, $fields, $increaseFields);
    }

    public function updateForumStatistics($fid, $subFids)
    {
        Wekit::cache()->batchDelete($this->getKeysByFid($fid));

        return $this->_getDao()->updateForumStatistics($fid, $subFids);
    }

    public function batchUpdateForum($fids, $fields, $increaseFields = [])
    {
        Wekit::cache()->batchDelete($this->fetchKeysByFid($fids));

        return $this->_getDao()->batchUpdateForum($fids, $fields, $increaseFields);
    }

    public function deleteForum($fid)
    {
        Wekit::cache()->batchDelete($this->getKeysByFid($fid));

        return $this->_getDao()->deleteForum($fid);
    }
}
