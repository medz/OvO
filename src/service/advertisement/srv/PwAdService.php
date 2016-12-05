<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 广告服务
 *
 * @author Zhu Dong <zhudong0808@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z zhudong $
 */
class PwAdService
{
    public function addAdPosition($id, $identifier, $type, $width, $height, $status, $schedule)
    {
        list($id, $width, $heigth, $status) = array(intval($id), intval($width), intval($heigth), intval($status));
        if ($status > 1) {
            return new PwError('ADVERTISEMENT:position.status.error');
        }
        if (empty($id)) {
            return new PwError('ADVERTISEMENT:position.error');
        }
        if ($identifier && $this->_getAdDs()->getByIdentifier($identifier)) {
            return new PwError('ADVERTISEMENT:position.exists');
        }
        if (!array_key_exists($type, $this->_getAdDs()->getAdType())) {
            return new PwError('ADVERTISEMENT:type.error');
        }
        $checkPostition = $this->_getAdDs()->getByPid($id);
        if ($checkPostition) {
            return new PwError('ADVERTISEMENT:position.exists');
        }
        Wind::import('SRV:advertisement.dm.PwAdDm');
        $dm = new PwAdDm();
        $dm->setPid($id)
            ->setIdentifier($identifier)
            ->setType($type)
            ->setWidth($width)
            ->setHeight($height)
            ->setStatus($status)
            ->setSchedule($schedule);
        $result = $this->_getAdDs()->addAdPosition($dm);
        if (!$result) {
            return new PwError('ADVERTISEMENT:add.fail');
        }
        $this->_updateAdcache();

        return true;
    }

    public function editAdPosition($id, $identifier, $type, $width, $height, $status, $schedule, $showType, $condition)
    {
        list($id, $width, $heigth, $status, $showType, $condition) = array(intval($id), intval($width), intval($heigth), intval($status), intval($showType), $condition);
        if ($status > 1) {
            return new PwError('ADVERTISEMENT:position.status.error');
        }
        if (empty($id)) {
            return new PwError('ADVERTISEMENT:position.error');
        }
        if (!array_key_exists($type, $this->_getAdDs()->getAdType())) {
            return new PwError('ADVERTISEMENT:type.error');
        }
        $checkPostition = $this->_getAdDs()->getByPid($id);
        if (!$checkPostition) {
            return new PwError('ADVERTISEMENT:position.not.exists');
        }
        Wind::import('SRV:advertisement.dm.PwAdDm');
        $dm = new PwAdDm($id);
        $dm->setIdentifier($identifier)
            ->setType($type)
            ->setWidth($width)
            ->setHeight($height)
            ->setStatus($status)
            ->setSchedule($schedule)
            ->setShowType($showType)
            ->setCondition($condition);
        $result = $this->_getAdDs()->editAdPosition($dm);
        if (!$result) {
            return new PwError('ADVERTISEMENT:edit.fail');
        }
        $this->_updateAdcache();

        return true;
    }

    public function changeAdPositionStatus($id, $status)
    {
        list($id, $status) = array(intval($id), intval($status));
        if ($status > 1) {
            return new PwError('ADVERTISEMENT:position.status.error');
        }
        if (empty($id)) {
            return new PwError('ADVERTISEMENT:position.error');
        }
        $checkPostition = $this->_getAdDs()->getByPid($id);
        if (!$checkPostition) {
            return new PwError('ADVERTISEMENT:position.not.exists');
        }
        Wind::import('SRV:advertisement.dm.PwAdDm');
        $dm = new PwAdDm($id);
        $dm->setStatus($status);
        $result = $this->_getAdDs()->editAdPosition($dm);
        if (!$result) {
            return new PwError('ADVERTISEMENT:edit.fail');
        }
        $this->_updateAdcache();

        return true;
    }

    public function getParamsByTypeId($typeId)
    {
        switch ($typeId) {
            case 1:
                $params = array('mid', 'fid');
                break;
            case 2:
                $params = array('pid', 'fid');
                break;
            case 3:
                $params = array('fid', 'floorid');
                break;
            case 4:
                $params = array('proid');
                break;
            default:
                $params = array();
        }

        return $params;
    }

    public function getPortals()
    {
        return array('1' => '门户1', '2' => '门户2', '3' => '门户3', '4' => '门户4');
    }

    public function getAdShowState($currentAd, $mid, $fid, $pid, $floorid, $proid)
    {
        if (!$currentAd['status'] || !$this->_getScheduleResult($currentAd['schedule']) || !$this->_getConditionResult($currentAd['condition'], $mid, $fid, $pid, $floorid, $proid)) {
            return false;
        }

        return true;
    }

    private function _getScheduleResult($schedule)
    {
        $schedule = unserialize($schedule);
        if (empty($schedule)) {
            return false;
        }
        $timestamp = Pw::getTime();
        foreach ($schedule as $value) {
            if ($timestamp > $value[0] && (!$value[1] || $timestamp < $value[1])) {
                return true;
            }
        }

        return false;
    }

    private function _getConditionResult($condition, $mid, $fid, $pid, $floorid, $proid)
    {
        $condition = unserialize($condition);
        if (empty($condition)) {
            return true;
        }
        foreach ($condition as $key => $value) {
            if ($key == 'mid') {
                $result[] = (in_array($mid, $condition['mid']) || empty($condition['mid'])) ? 1 : 0;
            } elseif ($key == 'fid') {
                $result[] = (in_array($fid, $condition['fid']) || empty($condition['fid'])) ? 1 : 0;
            } elseif ($key == 'pid') {
                $result[] = (in_array($pid, $condition['pid']) || empty($condition['pid'])) ? 1 : 0;
            } elseif ($key == 'floorid') {
                $result[] = (in_array($floorid, $condition['floorid']) || empty($condition['floorid'])) ? 1 : 0;
            } elseif ($key == 'proid') {
                $result[] = (in_array($proid, $condition['proid']) || empty($condition['proid'])) ? 1 : 0;
            }
        }
        if (count($result) == array_sum($result)) {
            return true;
        }

        return false;
    }

    /*
    public function buildUrl($params){
        foreach ($params as $value) {
            if($value == 'mid') {
                $router = Wind::getComponent('router');
                $m = $router->getModule();
                $c = $router->getController();
                $url[] = 'mid='.$m.'.'.$c;
            }elseif($value == 'fid'){
                $url[] = 'fid=<?php echo $fid;?>';
            }elseif($value == 'pid'){
                $router = Wind::getComponent('router');
                $m = $router->getModule();
                $c = $router->getController();
                $url[] = 'pid='.$m.'.'.$c;
            }elseif($value == 'floorid'){
                $url[] = 'floorid=<?php echo $read[lou];?>';
            }elseif($value == 'proid'){
                $url[] = 'proid=<?php echo $proid;?>';
            }
        }
        $url = implode('&', $url);
        return $url;
    }
    */
    public function getModeByMid($mid)
    {
        $modes = $this->_getAdDs()->getModes();
        foreach ($modes as $key => $value) {
            if (in_array($mid, $value['src']) !== false) {
                $mode = $key;
                break;
            }
        }

        return $mode;
    }

    public function getInstalledPosition()
    {
        $ads = $this->_getAdDs()->getAllAd();

        return $ads;
    }

    private function _updateAdcache()
    {
        $ads = $this->_getAdDs()->getAllAd();
        Wekit::cache()->set('advertisement', $ads);
    }

    private function _getAdDs()
    {
        return Wekit::load('SRV:advertisement.PwAd');
    }
}
