<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * <note>
 *  decorateAddProperty 为插入表单值修饰
 *  decorateEditProperty 为修改表单值修饰
 *  _getData 获取数据
 * </note>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignForumDataService.php 22678 2012-12-26 09:22:23Z jieyin $
 */
class PwDesignForumDataService extends PwDesignModelBase
{
    public function decorateAddProperty($model)
    {
        $data = [];
        $forumService = $this->_getFroumService();
        $data['forumOption'] = '<option value="">全部版块</option>'.$forumService->getForumOption();

        return $data;
    }

    public function decorateEditProperty($moduleBo)
    {
        $model = $moduleBo->getModel();
        $property = $moduleBo->getProperty();
        $data = [];
        $forumService = $this->_getFroumService();
        $data['forumOption'] = '<option value="">全部版块</option>'.$forumService->getForumOption($property['fids']);

        return $data;
    }

    protected function getData($field, $order, $limit, $offset)
    {
        $so = new PwForumSo();
        //if ($field['fids']) {//if (count($field['fids']) > $limit) $field['fids'] = array_slice($field['fids'], 0, $limit);

        if ($field['fids'] && $field['fids'][0]) {
            $so->setFid($field['fids']);
        }
        switch ($order) {
            case '1':
                $so->orderbyThreads(false);
                break;
            case '2':
                $so->orderbyTodaythreads(false);
                break;
            case '3':
                $so->orderbyArticle(false);
                break;
            case '4':
                $so->orderbyLastPostTime(false);
                break;
        }
        $list = Wekit::load('forum.PwForum')->searchDesignForum($so, $limit, $offset);

        return $this->_buildSignKey($list);
    }

    private function _buildSignKey($list)
    {
        $_username = [];
        $_tids = [];
        foreach ($list as $k => $v) {
            $_tids[] = $v['lastpost_tid'];
        }

        $thread = $this->_getThread($_tids);
        foreach ($list as $k => $v) {

            $lastthread = $thread[$v['lastpost_tid']];
            $newValue = [
                'name' => $this->_filterForumHtml($v['name']),
                'forum_url' => $this->getFromUrl($v['type'], $v['fid']),
                'descrip' => $this->_formatDes($v['descrip']),
                'logo' => $v['logo'] ? Pw::getPath($v['logo']) : '',
                'lastpost_time' => $this->_formatTime($lastthread['lastpost_time']),
                'lastpost_smallavatar' => $lastthread['lastpost_userid'] ? Pw::getAvatar($lastthread['lastpost_userid'], 'small') : '',
                'lastpost_middleavatar' => $lastthread['lastpost_userid'] ? Pw::getAvatar($lastthread['lastpost_userid'], 'middle') : '',
                'lastpost_userid' => $lastthread['lastpost_userid'],
                'lastpost_username' => $lastthread['lastpost_username'],
                'lastpost_space' => $lastthread['lastpost_userid'] ? WindUrlHelper::createUrl('space/index/run', ['uid' => $lastthread['lastpost_userid']], '', 'pw') : '',
                'lastthread_space' => $lastthread['created_userid'] ? WindUrlHelper::createUrl('space/index/run', ['uid' => $lastthread['created_userid']], '', 'pw') : '',
                'lastthread_smallavatar' => $lastthread['created_userid'] ? Pw::getAvatar($lastthread['created_userid'], 'small') : '',
                'lastthread_middleavatar' => $lastthread['created_userid'] ? Pw::getAvatar($lastthread['created_userid'], 'middle') : '',
                'lastthread_username' => $lastthread['created_username'],
                'lastthread_time' => $this->_formatTime($lastthread['created_time']),
                'lastthread' => $this->_formatTitle($lastthread['subject']),
            ];

            $list[$k] = array_merge($v, $newValue);
        }

        return $list;
    }

    private function getFromUrl($type, $fid)
    {
        if ($type == 'category') {
            return WindUrlHelper::createUrl('bbs/cate/run', ['fid' => $fid], '', 'pw');
        }

        return WindUrlHelper::createUrl('bbs/thread/run', ['fid' => $fid], '', 'pw');
    }

    private function _getThread($tids)
    {
        return Wekit::load('forum.PwThread')->fetchThread($tids);
    }

    private function _getForum($fids)
    {
        return Wekit::load('forum.PwForum')->fetchForum($fids);
    }

    /**
     * 过滤版块名称html
     * Enter description here ...
     *
     * @param string $forumname
     */
    private function _filterForumHtml($forumname)
    {
        return strip_tags($forumname);
        //return  preg_replace('/<SPAN(.*)>(.*)<\/SPAN>/isU', '\\2', $forumname);
    }

    private function _getFroumService()
    {
        return Wekit::load('forum.srv.PwForumService');
    }

    private function _getComponentDs()
    {
        return Wekit::load('design.PwDesignComponent');
    }
}
