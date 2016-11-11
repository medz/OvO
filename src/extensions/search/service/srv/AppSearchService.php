<?php
/**
 * 本地搜索
 */
class AppSearchService
{
    const SEARCH_TYPE_THREAD = 1;
    const SEARCH_TYPE_USER = 2;
    const SEARCH_TYPE_FORUM = 3;

    public function countSearch($type, $so)
    {
        $action = $this->_getReportAction($type);
        if (!$action) {
            return new PwError('搜索类型不存在');
        }
        $cout = $action->countSearch($so);

        return $cout;
    }

    public function search($type, $so, $limit = 20, $start = 0)
    {
        $action = $this->_getReportAction($type);
        if (!$action) {
            return new PwError('搜索类型不存在');
        }

        return $action->search($so, $limit, $start);
    }

    public function build($type, $list, $keywords)
    {
        $action = $this->_getReportAction($type);
        if (!$action) {
            return new PwError('搜索类型不存在');
        }

        return $action->build($list, $keywords);
    }

    public function getHotKey($type, $num)
    {
        $action = $this->_getReportAction($type);
        if (!$action) {
            return array();
        }
        $typeid = $this->_getTypeId($type);
        $list = $this->_getSearchRecord()->getByType($typeid, $num);
        foreach ($list as $k => $v) {
            $list[$k]['url'] = WindUrlHelper::createUrl('app/search/'.$type.'/run', array('keywords' => $v['keywords']));
        }

        return $list;
    }

    public function getTypes($is = '', $keywords = '')
    {
        $types = $this->getTypeMap();
        $names = $this->getTypeName();
        $type = array();
        $keyword = $keywords ? array('keywords' => $keywords) : '';
        if ($is) {
            foreach ($types as $k => $id) {
                $type[$k]['name'] = $names[$id];
            }
        } else {
            $_types = Wekit::C('search', 'types');
            foreach ($types as $k => $id) {
                $type[$k]['name'] = $names[$id];
                $type[$k]['url'] = WindUrlHelper::createUrl('app/search/'.$k.'/run', $keyword);
                if (!Pw::ifcheck(Pw::inArray($k, $_types))) {
                    unset($type[$k]);
                }
            }
        }

        return $type;
    }

    protected function _getReportAction($type)
    {
        if (!$type) {
            return null;
        }
        $type = strtolower($type);
        $className = sprintf('App_Search%s', ucfirst($type));
        if (class_exists($className, false)) {
            return new $className();
        }
        $fliePath = 'EXT:search.service.srv.action.'.$className;
        Wind::import($fliePath);

        return new $className();
    }


    /**
     * 获取类型
     *
     * @return array
     */
    public function getTypeMap()
    {
        return array(
            'thread' => self::SEARCH_TYPE_THREAD,
            'user' => self::SEARCH_TYPE_USER,
            'forum' => self::SEARCH_TYPE_FORUM,
        );
    }

    /**
     * 获取类型名称
     *
     * @return array
     */
    public function getTypeName()
    {
        return array(
            self::SEARCH_TYPE_THREAD => '帖子',
            self::SEARCH_TYPE_USER => '用户',
            self::SEARCH_TYPE_FORUM => '版块',
        );
    }


    private function _getTypeId($type)
    {
        $types = $this->getTypeMap();
        if (!isset($types[$type])) {
            return false;
        }

        return $types[$type];
    }

    public function _getOrderBy($so, $orderby = null)
    {
        switch ($orderby) {
            case 'created_time':
                $so->orderbyCreatedTime(0);
                break;
            case 'lastpost_time':
                $so->orderbyLastPostTime(0);
                break;
            default:
                $so->orderbyLastPostTime(0);
                break;
        }

        return $so;
    }

    public function _getLimitTime($daytime = '')
    {
        if (!$daytime) {
            return false;
        }
        $timestamp = Pw::getTime();
        $times = array(
            'today' => $timestamp - 86400,
            'week' => $timestamp - 7 * 86400,
            'month' => $timestamp - 30 * 86400,
            'year' => $timestamp - 365 * 86400,
        );

        return $times[$daytime];
    }

    // **
    public function _limitTimeMap()
    {
        return array(
            'today' => '最近一天',
            'week' => '最近一周',
            'month' => '最近一月',
            'year' => '最近一年',
        );
    }

    // **
    public function _checkRight()
    {
        $loginUser = Wekit::getLoginUser();
        if ($loginUser->gid == 6 || $loginUser->gid == 7) {
            $this->showError('啊哦，你所在的用户组不允许搜索', 'bbs/index/run');
        }
        if ($loginUser->getPermission('app_search_open') < 1) {
            return new PwError('permission.search.allow.not', array('{grouptitle}' => $loginUser->getGroupInfo('name')));
        }

        return true;
    }

    //设置最后搜索时间
    public function _checkSearch()
    {
        $loginUser = Wekit::getLoginUser();
        $search_time_interval = $loginUser->getPermission('app_search_time_interval');
        $stampTime = Pw::getTime();
        if ($stampTime - $loginUser->info['last_search_time'] < $search_time_interval) {
            return new PwError('permission.search.limittime.allow', array('{limittime}' => $search_time_interval));
        }
        Wind::import('EXT:search.service.dm.App_Search_Dm');
        $dm = new App_Search_Dm($loginUser->uid);
        $dm->setLastSearchTime($stampTime);
        Wekit::load('user.PwUser')->editUser($dm, PwUser::FETCH_DATA);

        return true;
    }

    public function _replaceRecord($keywords, $type)
    {
        $loginUser = Wekit::getLoginUser();
        if (!$keywords || !$loginUser || !$type) {
            return false;
        }
        Wind::import('EXT:search.service.dm.App_SearchRecordDm');
        $dm = new App_SearchRecordDm();
        $ds = $this->_getSearchRecord();
        $dm->setKeywords($keywords)
            ->setSearchType($type)
            ->setCreatedUserid($loginUser->uid)
            ->setCreatedTime(Pw::getTime());
        $this->_getSearchRecord()->replaceRecord($dm);
        if ($this->_getSearchRecord()->getByTypeAndKey($keywords, $type)) {
            $dms = new App_SearchRecordDm();
            $dms->addNum(1)->setCreatedUserid($loginUser->uid);
            $this->_getSearchRecord()->update($keywords, $type, $dms);
        } else {
            $dms = new App_SearchRecordDm();
            $dms->setKeywords($keywords)
                ->setSearchType($type)
                ->setCreatedUserid($loginUser->uid)
                ->addNum(1);
            $this->_getSearchRecord()->add($dms);
        }
    }

    /**
     * @return AppSearchRecord
     */
    private function _getSearchRecord()
    {
        return Wekit::load('EXT:search.service.AppSearchRecord');
    }
}
