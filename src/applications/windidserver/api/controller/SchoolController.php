<?php

Wind::import('APPS:api.controller.OpenBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: SchoolController.php 24834 2013-02-22 06:43:43Z jieyin $
 * @package
 */
class SchoolController extends OpenBaseController
{
    public function getAction()
    {
        $result = $this->_getSchoolDs()->getSchool($this->getInput('id', 'get'));
        $this->output($result);
    }

    public function fetchAction()
    {
        $result = $this->_getSchoolDs()->fetchSchool($this->getInput('ids', 'get'));
        $this->output($result);
    }

    public function getSchoolByAreaidAndTypeidAction()
    {
        $result = $this->_getSchoolDs()->getSchoolByAreaidAndTypeid($this->getInput('areaid', 'get'), $this->getInput('typeid', 'get'));
        $this->output($result);
    }

    public function searchAction()
    {
        $start = (int) $this->getInput('start', 'get');
        $limit = (int) $this->getInput('limit', 'get');
        $name = $this->getInput('name', 'get');
        $typeid = $this->getInput('typeid', 'get');
        $areaid = $this->getInput('areaid', 'get');
        $firstchar = $this->getInput('first_char', 'get');
        !$limit && $limit = 10;
        !$start && $start = 0;
        Wind::import('WINDID:service.school.vo.WindidSchoolSo');
        $schoolSo = new WindidSchoolSo();
        $schoolSo->setName($name)
            ->setTypeid($typeid)
            ->setFirstChar($firstchar)
            ->setAreaid($areaid);
        $result = $this->_getSchoolDs()->searchSchool($schoolSo, $limit, $start);
        $this->output($result);
    }

    public function searchDataAction()
    {
        $start = (int) $this->getInput('start', 'get');
        $limit = (int) $this->getInput('limit', 'get');
        $name = $this->getInput('name', 'get');
        $typeid = $this->getInput('typeid', 'get');
        $areaid = $this->getInput('areaid', 'get');
        $firstchar = $this->getInput('first_char', 'get');
        !$limit && $limit = 10;
        !$start && $start = 0;
        Wind::import('WINDID:service.school.vo.WindidSchoolSo');
        $schoolSo = new WindidSchoolSo();
        $schoolSo->setName($name)
            ->setTypeid($typeid)
            ->setFirstChar($firstchar)
            ->setAreaid($areaid);
        $result = $this->_getSchoolService()->searchSchool($schoolSo, $limit, $start);
        $this->output($result);
    }

    public function addAction()
    {
        list($name, $firstchar, $typeid, $areaid) = $this->getInput(array('name', 'first_char', 'typeid', 'areaid'), 'post');
        Wind::import('WSRV:school.dm.WindidSchoolDm');
        $dm = new WindidSchoolDm();
        isset($name) && $dm->setName($name);
        isset($firstchar) && $dm->setFirstChar($firstchar);
        isset($typeid) && $dm->setTypeid($typeid);
        isset($areaid) && $dm->setAreaid($areaid);
        $result = $this->_getSchoolDs()->addSchool($dm);
        $this->output(WindidUtility::result($result));
    }

    public function batchAddAction()
    {
        list($name, $firstchar, $typeid, $areaid) = $this->getInput(array('name', 'first_char', 'typeid', 'areaid'), 'post');
        Wind::import('WSRV:school.dm.WindidSchoolDm');
        foreach ($name as $k => $v) {
            $dm = new WindidSchoolDm();
            isset($name[$k]) && $dm->setName($name[$k]);
            isset($firstchar[$k]) && $dm->setFirstChar($firstchar[$k]);
            isset($typeid[$k]) && $dm->setTypeid($typeid[$k]);
            isset($areaid[$k]) && $dm->setAreaid($areaid[$k]);
            $dms[] = $dm;
        }
        $result = $this->_getSchoolDs()->batchAddSchool($dms);
        $this->output(WindidUtility::result($result));
    }

    public function updateAction()
    {
        $ids = $this->getInput('id', 'get');
        list($name, $firstchar, $typeid, $areaid) = $this->getInput(array('name', 'first_char', 'typeid', 'areaid'), 'post');
        Wind::import('WSRV:school.dm.WindidSchoolDm');
        foreach ($name as $k => $id) {
            $dm = new WindidSchoolDm();
            $dm->setSchoolid($id);
            isset($name[$k]) && $dm->setName($name[$k]);
            isset($firstchar[$k]) && $dm->setFirstChar($firstchar[$k]);
            isset($typeid[$k]) && $dm->setTypeid($typeid[$k]);
            isset($areaid[$k]) && $dm->setAreaid($areaid[$k]);
            $dms[] = $dm;
        }
        $result = $this->_getSchoolDs()->batchAddSchool($dms);
        $this->output(WindidUtility::result($result));
    }

    public function deleteAction()
    {
        $schoolid = (int) $this->getInput('id', 'post');
        $result = $this->_getSchoolDs()->deleteSchool($schoolid);
        $this->output(WindidUtility::result($result));
    }

    private function _getSchoolDs()
    {
        return Wekit::load('WSRV:school.WindidSchool');
    }

    private function _getSchoolService()
    {
        return Wekit::load('WSRV:school.srv.WindidSchoolService');
    }
}
