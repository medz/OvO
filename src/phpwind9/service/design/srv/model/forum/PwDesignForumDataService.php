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
        $data = array();
        $forumService = $this->_getFroumService();
        $data['forumOption'] = '<option value="">全部版块</option>'.$forumService->getForumOption();

        return $data;
    }

    public function decorateEditProperty($moduleBo)
    {
        $model = $moduleBo->getModel();
        $property = $moduleBo->getProperty();
        $data = array();
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
        $_username = array();
        $_tids = array();
        foreach ($list as $k => $v) {
            $_tids[] = $v['lastpost_tid'];
        }
        $thread = $this->_getThread($_tids);
        foreach ($list as $k => $v) {
            $list[$k]['name'] = $this->_filterForumHtml($v['name']);
            if ($v['type'] == 'category') {
                $list[$k]['forum_url'] = WindUrlHelper::createUrl('bbs/cate/run', array('fid' => $v['fid']), '', 'pw');
            } else {
                $list[$k]['forum_url'] = WindUrlHelper::createUrl('bbs/thread/run', array('fid' => $v['fid']), '', 'pw');
            }
            $list[$k]['descrip'] = $this->_formatDes($v['descrip']);
            $list[$k]['logo'] = $v['logo'] ? Pw::getPath($v['logo']) : '';

            $lastthread = $thread[$v['lastpost_tid']];
            $list[$k]['lastpost_time'] = $this->_formatTime($lastthread['lastpost_time']);

            $list[$k]['lastpost_smallavatar'] = $lastthread['lastpost_userid'] ? Pw::getAvatar($lastthread['lastpost_userid'], 'small') : '';
            $list[$k]['lastpost_middleavatar'] = $lastthread['lastpost_userid'] ? Pw::getAvatar($lastthread['lastpost_userid'], 'middle') : '';

            $list[$k]['lastpost_userid'] = $lastthread['lastpost_userid'];
            $list[$k]['lastpost_username'] = $lastthread['lastpost_username'];
            $list[$k]['lastpost_space'] = $lastthread['lastpost_userid'] ? WindUrlHelper::createUrl('space/index/run', array('uid' => $lastthread['lastpost_userid']), '', 'pw') : '';
            $list[$k]['lastthread_space'] = $lastthread['created_userid'] ? WindUrlHelper::createUrl('space/index/run', array('uid' => $lastthread['created_userid']), '', 'pw') : '';
            $list[$k]['lastthread_smallavatar'] = $lastthread['created_userid'] ? Pw::getAvatar($lastthread['created_userid'], 'small') : '';
            $list[$k]['lastthread_middleavatar'] = $lastthread['created_userid'] ? Pw::getAvatar($lastthread['created_userid'], 'middle') : '';
            $list[$k]['lastthread_username'] = $lastthread['created_username'];
            $list[$k]['lastthread_time'] = $this->_formatTime($lastthread['created_time']);
            $list[$k]['lastthread'] = $this->_formatTitle($lastthread['subject']);
        }

        return $list;
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
