<?php

Wind::import('APPS:api.controller.OpenBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: AreaController.php 24834 2013-02-22 06:43:43Z jieyin $
 */
class AreaController extends OpenBaseController
{
    public function getAction()
    {
        $result = $this->_getAreaDs()->getArea($this->getInput('id', 'get'));
        $this->output($result);
    }

    public function fetchAction()
    {
        $result = $this->_getAreaDs()->fetchByAreaid($this->getInput('ids', 'get'));
        $this->output($result);
    }

    public function getByParentidAction()
    {
        $result = $this->_getAreaDs()->getAreaByParentid($this->getInput('parentid', 'get'));
        $this->output($result);
    }

    public function getAllAction()
    {
        $result = $this->_getAreaDs()->fetchAll();
        $this->output($result);
    }

    public function getAreaInfoAction()
    {
        $areaid = $this->getInput('areaid', 'get');
        $result = $this->_getAreaService()->getAreaInfo($areaid);
        $this->output($result);
    }

    public function fetchAreaInfoAction()
    {
        $areaids = $this->getInput('areaids', 'get');
        $result = $this->_getAreaService()->fetchAreaInfo($areaids);
        $this->output($result);
    }

    public function getAreaRoutAction()
    {
        $areaid = $this->getInput('areaid', 'get');
        $result = $this->_getAreaService()->getAreaRout($areaid);
        $this->output($result);
    }

    public function fetchAreaRoutAction()
    {
        $areaids = $this->getInput('areaids', 'get');
        $result = $this->_getAreaService()->fetchAreaRout($areaids);
        $this->output($result);
    }

    public function getAreaTreeAction()
    {
        $result = $this->_getAreaService()->getAreaTree();
        $this->output($result);
    }

    public function updateAction()
    {
        $id = $this->getInput('id', 'get');
        list($name, $parentid, $joinname) = $this->getInput(array('name', 'parentid', 'joinname'), 'post');

        Wind::import('WSRV:area.dm.WindidAreaDm');
        $dm = new WindidAreaDm();
        $dm->setAreaid($id);
        isset($name) && $dm->setName($name);
        isset($parentid) && $dm->setParentid($parentid);
        isset($joinname) && $dm->setJoinname($joinname);

        $result = $this->_getAreaDs()->updateArea($dm);
        $this->output(WindidUtility::result($result));
    }

    public function batchaddAction()
    {
        $dms = array();
        list($ids, $name, $parentid, $joinname) = $this->getInput(array('id', 'name', 'parentid', 'joinname'), 'post');
        Wind::import('WSRV:area.dm.WindidAreaDm');
        foreach ($ids as $k => $id) {
            $dm = new WindidAreaDm();
            $dm->setAreaid($id);
            isset($name[$k]) && $dm->setName($name[$k]);
            isset($parentid[$k]) && $dm->setParentid($parentid[$k]);
            isset($joinname[$k]) && $dm->setJoinname($joinname[$k]);
            $dms[] = $dm;
        }
        $result = $this->_getAreaDs()->batchAddArea($dms);
        $this->output(WindidUtility::result($result));
    }

    public function deleteAction()
    {
        $id = $this->getInput('id', 'post');
        $result = $this->_getAreaDs()->deleteArea($id);
        $this->output(WindidUtility::result($result));
    }

    private function _getAreaDs()
    {
        return Wekit::load('WSRV:area.WindidArea');
    }

    private function _getAreaService()
    {
        return Wekit::load('WSRV:area.srv.WindidAreaService');
    }
}
