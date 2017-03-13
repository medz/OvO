<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignSegment.php 13231 2012-07-04 05:07:41Z gao.wanggao $
 */
class PwDesignSegment
{
    public function getSegment($segment, $pageid)
    {
        if (!$segment || $pageid < 1) {
            return [];
        }

        return $this->_getDao()->getSegment($segment, $pageid);
    }

    public function getSegmentByPageid($pageid)
    {
        if ($pageid < 1) {
            return [];
        }

        return $this->_getDao()->getSegmentByPageid($pageid);
    }

    public function replaceSegment($segment, $pageid, $tpl = '', $struct = '')
    {
        if (!$segment || $pageid < 1) {
            return false;
        }
        $data['segment'] = $segment;
        $data['page_id'] = $pageid;
        $data['segment_tpl'] = $tpl;
        $data['segment_struct'] = $struct;

        return $this->_getDao()->replaceSegment($data);
    }

    public function deleteSegment($segment, $pageid)
    {
        if (!$segment || $pageid < 1) {
            return false;
        }

        return $this->_getDao()->deleteSegment($segment, $pageid);
    }

    public function deleteSegmentByPageid($pageid)
    {
        if ($pageid < 1) {
            return false;
        }

        return $this->_getDao()->deleteSegmentByPageid($pageid);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignSegmentDao');
    }
}
