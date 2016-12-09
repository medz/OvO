<?php

defined('WEKIT_VERSION') || exit('Forbidden');
/**
 * 公告管理服务层
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com> 2012-01-21
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: PwAnnounceService.php 2552 2012-01-12 11:28:21Z mingxing.sun $
 */
class PwAnnounceService
{
    /**
     * 批量格式化公告记录，添加用户名.
     *
     * @param $announceInfo
     *
     * @return array
     */
    public function formatAnnouncesUsername($announceInfos)
    {
        if (!$announceInfos || !is_array($announceInfos)) {
            return array();
        }
        $uids = $result = array();
        foreach ($announceInfos as $value) {
            $uids[] = $value['created_userid'];
        }
        $userInfos = $this->_getPwUser()->fetchUserByUid(array_unique($uids));
        foreach ($announceInfos as $key => $value) {
            $result[$key] = $value;
            $result[$key]['username'] = $userInfos[$value['created_userid']]['username'];
        }

        return $result;
    }

    /**
     * 通过公告记录组装前台显示效果
     * 帖子列表滚动展示标题用.
     *
     * @return array
     */
    public function getAnnounceForBbsScroll()
    {
        $announces = array();
        $announceInfos = $this->_getPwAnnounceDs()->getAnnounceByTimeOrderByVieworder(Pw::str2time(Pw::time2str(Pw::getTime(), 'Y-m-d')), 9, 0);
        foreach ($announceInfos as $value) {
            $announces[$value['aid']] = $value;
            $announces[$value['aid']]['start_date'] = Pw::time2str($value['start_date'], 'Y-m-d');
            if (Pw::strlen($value['subject']) > 18) {
                $announces[$value['aid']]['subject'] = Pw::substrs($value['subject'], 18);
            }
        }

        return $announces;
    }

    /**
     * 加载PwUser Ds 服务
     *
     * @return PwUser
     */
    private function _getPwUser()
    {
        return Wekit::load('user.PwUser');
    }

    /**
     * 获取公告接口 DS.
     *
     * @return PwAnnounce
     */
    protected function _getPwAnnounceDs()
    {
        return Wekit::load('announce.PwAnnounce');
    }
}
