<?php

Wind::import('SRV:design.srv.model.PwDesignModelBase');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * <note>
 *  decorateAddProperty 为插入表单值修饰
 *  decorateEditProperty 为修改表单值修饰
 *  _getData 获取数据
 * </note>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignThreadDataService.php 25436 2013-03-15 08:45:34Z gao.wanggao $
 */
class PwDesignThreadDataService extends PwDesignModelBase
{
    /**
     * (non-PHPdoc).
     *
     * @see src/service/design/srv/model/PwDesignModelBase::decorateAddProperty()
     */
    public function decorateAddProperty($model)
    {
        $data = array();
        $forumService = $this->_getFroumService();
        $data['forumOption'] = '<option value="">全部版块</option>'.$forumService->getForumOption();
        $data['specileType'] = array();
        $tType = Wekit::load('forum.srv.PwThreadType')->getTtype();
        foreach ($tType as $k => $v) {
            $data['specileType'][$k] = $v[0];
        }

        return $data;
    }

    /**
     * (non-PHPdoc).
     *
     * @see src/service/design/srv/model/PwDesignModelBase::decorateEditProperty()
     */
    public function decorateEditProperty($moduleBo)
    {
        $model = $moduleBo->getModel();
        $property = $moduleBo->getProperty();
        $data = array();
        $forumService = $this->_getFroumService();
        $data['forumOption'] = '<option value="">全部版块</option>'.$forumService->getForumOption($property['fids']);
        $data['specileType'] = array();
        $tType = Wekit::load('forum.srv.PwThreadType')->getTtype();
        foreach ($tType as $k => $v) {
            $data['specileType'][$k] = $v[0];
        }

        return $data;
    }

    /**
     * (non-PHPdoc).
     *
     * @see src/service/design/srv/model/PwDesignModelBase::decorateSaveProperty()
     */
    public function decorateSaveProperty($property, $moduleid)
    {
        //直接调用版本
        if (isset($property['fids'][0]) && !$property['fids'][0]) {
            $property['fids'] = array();
        }
        $property['mapFid'] = $property['fids'];
        /*
        //下级版块的调用实现
        $mapFid	= $fids = array();
        $srv = Wekit::load('forum.srv.PwForumService');
        $map = $srv->getForumMap();
        if (!is_array($property['fids'])) $property['fids'] = array();
        foreach ($property['fids'] AS $parentid) {
            $fids[] = $srv->getForumsByLevel($parentid, $map);
        }

        foreach ($fids AS $_fids) {
            foreach ($_fids AS $_v) {
                if (!$_v['isshow']) continue;
                $mapFid[] = $_v['fid'];
            }
        }
        $property['mapFid'] = array_unique(array_merge($mapFid, $property['fids']));
        if (isset($property['mapFid'][0]) && !$property['mapFid'][0]) $property['mapFid'] = array();
        */
        return $property;
    }

    protected function getData($field, $order, $limit, $offset)
    {
        Wind::import('SRV:forum.vo.PwThreadSo');
        $so = new PwThreadSo();
        $time = Pw::getTime();
        $so->setDisabled(0);
        $field['tids'] && $so->setTid(explode(' ', $field['tids']));
        if ($field['usernames']) {
            $usernames = explode(' ', $field['usernames']);
            foreach ($usernames as &$username) {
                $username = trim($username);
            }
            $users = Wekit::load('user.PwUser')->fetchUserByName($usernames);
            $uids = array_keys($users);
            if ($uids) {
                $so->setAuthorId($uids);
            } else {
                return array();
            }
        }
        $field['keywords'] && $so->setKeywordOfTitle(trim($field['keywords']));
        $field['mapFid'] && $so->setFid($field['mapFid']); //修正后的fids
        $field['special'] && $so->setSpecial($field['special']);
        $field['istop'] && $so->setTopped($field['istop']);
        $field['ispic'] && $so->setHasImage($field['ispic']);
        //$field['isattach'] && $so->setIsattach($field['isattach']);
        $field['isdigest'] && $so->setDigest($field['isdigest']);

        $field['createdtime'] && $so->setCreateTimeStart($time - intval($field['createdtime']));
        $field['createdtime'] && $so->setCreateTimeEnd($time);

        $field['posttime'] && $so->setLastpostTimeStart($time - intval($field['posttime']));
        $field['posttime'] && $so->setLastpostTimeEnd($time);

        switch ($order) {
            case '2':
                $so->orderbyLastPostTime(false);
                break;
            case '1':
                $so->orderbyCreatedTime(false);
                break;
            case '3':
                $so->orderbyReplies(false);
                break;
            case '4':
                $so->orderbyHits(false);
                break;
            case '5':
                $so->orderbyLike(false);
                break;
        }

        $list = Wekit::load('forum.PwThread')->searchThread($so, $limit, $offset);

        return $this->_buildSignKey($list, $field['ishighlight']);
    }

    /**
     * 用于推送时的指定数据获取.
     *
     * @see src/service/design/srv/model/PwDesignModelBase::_fetchData()
     */
    protected function fetchData($ids)
    {
        $list = Wekit::load('forum.PwThread')->fetchThread($ids);

        return $this->_buildSignKey($list);
    }

    private function _buildSignKey($list, $ishighlight = null)
    {
        $content = $_tType = $_fid = $_aTid = array();
        $_tid = array_keys($list);
        $content = $this->_getContent($_tid);
        foreach ($list as $v) {
            $_fid[] = $v['fid'];
            $_tType[] = $v['topic_type'];
            if ($content[$v['tid']]['aids']) {
                $_aTid[] = $v['tid'];
            }
        }
        $forums = $this->_getForum($_fid);
        $tTypes = $this->_getTopicType($_tType);
        $attachs = $this->_getAttachs($_aTid);
        if ($ishighlight) {
            $highlight = new PwHighlight();
        }
        foreach ($list as $k => $v) {
            if (!$forums[$v['fid']]['isshow']) {
                $v = array();
            }
            $list[$k]['subject'] = $this->_formatTitle($v['subject']);
            if ($ishighlight) {
                $styleArr = $highlight->parseHighlight($v['highlight']);
                $list[$k]['__style'] = array($styleArr['bold'], $styleArr['underline'], $styleArr['italic'], $styleArr['color']);
            }
            $list[$k]['url'] = WindUrlHelper::createUrl('bbs/read/run', array('tid' => $v['tid'], 'fid' => $v['fid']), '', 'pw');
            $list[$k]['content'] = $this->_formatDes($content[$k]['content']);
            $list[$k]['created_time'] = $this->_formatTime($v['created_time']);
            $list[$k]['lastpost_time'] = $this->_formatTime($v['lastpost_time']);
            $list[$k]['created_space'] = WindUrlHelper::createUrl('space/index/run', array('uid' => $v['created_userid']), '', 'pw');
            $list[$k]['created_smallavatar'] = Pw::getAvatar($v['created_userid'], 'small');
            $list[$k]['created_middleavatar'] = Pw::getAvatar($v['created_userid'], 'middle');
            $list[$k]['created_bigavatar'] = Pw::getAvatar($v['created_userid'], 'big');
            $list[$k]['lastpost_smallavatar'] = Pw::getAvatar($v['lastpost_userid'], 'small');
            $list[$k]['lastpost_middleavatar'] = Pw::getAvatar($v['lastpost_userid'], 'middle');
            $list[$k]['lastpost_space'] = WindUrlHelper::createUrl('space/index/run', array('uid' => $v['lastpost_userid']), '', 'pw');

            $list[$k]['forum_name'] = $this->_filterForumHtml($forums[$v['fid']]['name']);
            $list[$k]['forum_url'] = WindUrlHelper::createUrl('bbs/thread/run', array('fid' => $v['fid']), '', 'pw');
            $list[$k]['tType'] = isset($tTypes[$v['topic_type']]['name']) ? $tTypes[$v['topic_type']]['name'] : '';
            $list[$k]['tType_url'] = isset($tTypes[$v['topic_type']]['id']) ? WindUrlHelper::createUrl('bbs/thread/run', array('fid' => $v['fid'], 'type' => $tTypes[$v['topic_type']]['id']), '', 'pw') : '';
            $list[$k]['thumb_attach'] = $attachs[$v['tid']]['path'] ? $attachs[$v['tid']]['path'] : '';
        }

        return $list;
    }

    private function _getContent($ids)
    {
        return Wekit::load('forum.PwThread')->fetchThread($ids, PwThread::FETCH_CONTENT);
    }

    private function _getForum($fids)
    {
        return Wekit::load('forum.PwForum')->fetchForum($fids);
    }

    private function _getTopicType($ids)
    {
        return Wekit::load('forum.PwTopicType')->fetchTopicType($ids);
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

    //TODO
    private function _getAttachs($tids)
    {
        $attachs = array();
        $ds = Wekit::load('attach.PwThreadAttach');
        foreach ($tids as $tid) {
            $_attachs = $ds->getAttachByTid($tid, array(0));
            foreach ($_attachs as $v) {
                if ($v['type'] == 'img') {
                    $attachs[$tid] = $v;
                    break;
                }
            }
        }

        return $attachs;
    }

    private function _getFroumService()
    {
        return Wekit::load('forum.srv.PwForumService');
    }

    private function _getModelDs()
    {
        return Wekit::load('design.PwDesignModel');
    }
}
