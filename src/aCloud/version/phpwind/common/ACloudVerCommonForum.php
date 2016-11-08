<?php

! defined('ACLOUD_PATH') && exit('Forbidden');

define('FORUM_INVALID_PARAMS', 401);
define('FORUM_FAVOR_MAX', 402);
define('FORUM_FAVOR_ALREADY', 403);
define('FORUM_NOT_EXISTS', 404);

class ACloudVerCommonForum extends ACloudVerCommonBase
{
    public function getPrimaryKeyAndTable()
    {
        return array('bbs_forum', 'fid');
    }

    /**
     * 获取版块列表
     *
     * @return array
     */
    public function getAllForum()
    {
        $result = $this->getPwForum()->getForumList();
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    /**
     * 根据版块id获取版块列表
     *
     * @param  int   $fid
     * @return array
     */
    public function getForumByFid($fid)
    {
        $result = $this->getPwForum()->getForum(intval($fid));
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    /**
     * 根据版块id获取子版块
     *
     * @param  int   $fid
     * @return array
     */
    public function getChildForumByFid($fid)
    {
        $fid = intval($fid);
        if ($fid < 1) {
            return $this->buildResponse(FORUM_NOT_EXISTS);
        }
        $result = $this->getPwForum()->getSubForums(intval($fid));
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function getForumOption($fids)
    {
        $fids && $fids = explode(',', $fids);
        $result = $this->getPwForumService()->getForumOption($fids);
        if ($result instanceof PwError) {
            return $this->buildResponse(- 1, $result->getError());
        }

        return $this->buildResponse(0, $result);
    }

    public function getForumsByRange($startId, $endId)
    {
        list($startId, $endId) = array(intval($startId), intval($endId));
        if ($startId < 0 || $startId > $endId || $endId < 1) {
            return array();
        }
        $sql = sprintf('SELECT * FROM %s WHERE isshow = 1 AND fid >= %s AND fid <= %s', ACloudSysCoreS::sqlMetadata('{{bbs_forum}}'), ACloudSysCoreS::sqlEscape($startId), ACloudSysCoreS::sqlEscape($endId));
        $query = Wind::getComponent('db')->query($sql);
        $result = $query->fetchAll('fid', PDO::FETCH_ASSOC);
        $fids = array_keys($result);
        $forumDomain = $this->_getDomainDs()->fetchByTypeAndId('forum', $fids);
        $data = array();
        foreach ($result as $k => $v) {
            $v['domain'] = '';
            if (isset($forumDomain[$k])) {
                $v['domain'] = $forumDomain[$k]['domain'];
            }
            $data[] = $v;
        }
        if (! ACloudSysCoreS::isArray($data)) {
            return array();
        }

        return $this->buildForumData($data);
    }

    private function buildForumData($data)
    {
        list($result, $siteUrl) = array(array(), ACloudSysCoreCommon::getGlobal('g_siteurl', $_SERVER ['SERVER_NAME']));
        foreach ($data as $value) {
            $value ['forumurl'] = 'http://'.$siteUrl.'/index.php?m=bbs&c=thread&fid='.$value ['fid'];
            $result [$value ['fid']] = $value;
        }

        return $result;
    }

    private function getPwForum()
    {
        return wekit::load('SRV:forum.PwForum');
    }

    private function getPwForumService()
    {
        return wekit::load('SRV:forum.srv.PwForumService');
    }

    /**
     * @return PwDomain
     */
    private function _getDomainDs()
    {
        return Wekit::load('domain.PwDomain');
    }
}
