<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * 任务系统
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: ManageController.php 24028 2013-01-21 03:22:10Z xiaoxia.xuxx $
 */
class ManageController extends AdminBaseController
{
    private $perpage = 10;

    /* (non-PHPdoc)
     * @see WindController::run()
     */
    public function run()
    {
        $page = intval($this->getInput('page'));
        $page < 1 && $page = 1;
        /* @var $taskDs PwTask */
        $taskDs = Wekit::load('task.PwTask');
        $count = $taskDs->countAll();
        $list = [];
        if ($count > 0) {
            $totalPage = ceil($count / $this->perpage);
            $page = $page < 1 ? 1 : ($page > $totalPage ? intval($totalPage) : $page);
            $list = $this->_taskService()->getTaskList($page, $this->perpage);
        }
        $this->setOutput($count, 'count');
        $this->setOutput($list, 'list');
        $this->setOutput($page, 'page');
        $this->setOutput($this->perpage, 'perpage');
        $this->setOutput(Wekit::C('site', 'task.isOpen'), 'isOpen');
    }

    /**
     * 开启操作.
     */
    public function openAction()
    {
        $tasks = $this->getInput('task');
        $isopen = intval($this->getInput('isOpen'));
//		if (!$tasks) $this->showMessage();
        /* @var $taskService PwTaskService */
        $taskService = Wekit::load('task.srv.PwTaskService');
        foreach ($tasks as $id => $item) {
            $status = isset($item['status']) && $item['status'] == 1 ? 1 : 0;
            /*$dm = new PwTaskDm($id);
            $dm->setTitle($item['title'])->setSequence($item['sequence'])->setStatus($status);*/
            $result = $taskService->openTask($id, $status, $item['sequence'], $item['title']);
            if ($result instanceof PwError) {
                $this->showError($result->getError());
            }
        }
        $config = new PwConfigSet('site');
        $config->set('task.isOpen', $isopen)->flush();
        Wekit::load('SRV:nav.srv.PwNavService')->updateNavOpen('task', $isopen);
        $this->showMessage('TASK:edittask.success');
    }

    /**
     * 添加任务
     */
    public function addAction()
    {
        $pre_tasks = $this->_taskService()->getPreTasksByTaskId(0);
        $this->setOutput($pre_tasks, 'pre_tasks');

        //【任务奖励/完成条件】
        /* @var $taskExtends PwTaskExtends */
        $taskExtends = Wekit::load('APPS:task.service.PwTaskExtends');
        $this->setOutput($taskExtends->getRewardTypeList(), 'rewardList');
        $this->setOutput($taskExtends->getConditionTypeList(), 'conditionList');
        //【用户组】
        /* @var $userGroup PwUserGroups */
        $userGroup = Wekit::load('usergroup.PwUserGroups');
        $groups = $userGroup->getAllGroups();
        $groupTypes = $userGroup->getTypeNames();
        $this->setOutput($groups, 'groups');
        $this->setOutput($groupTypes, 'groupTypes');
        $this->setOutput(Pw::time2str(Pw::getTime(), 'Y-m-d'), '_current');
    }

    /**
     * 添加任务提交.
     */
    public function doAddAction()
    {
        $dm = $this->setDm(0);
        if (($r = $this->_taskDs()->addTask($dm)) instanceof PwError) {
            $this->showError($r->getError());
        }
        $this->showMessage('TASK:add.task.success', 'task/manage/run');
    }

    /**
     * 编辑任务
     */
    public function editAction()
    {
        //【用户组】
        /* @var $userGroup PwUserGroups */
        $userGroup = Wekit::load('usergroup.PwUserGroups');
        $groups = $userGroup->getAllGroups();
        $groupTypes = $userGroup->getTypeNames();
        $this->setOutput($groups, 'groups');
        $this->setOutput($groupTypes, 'groupTypes');

        $id = $this->getTaskId();
        $task = $this->_taskDs()->get($id);
        $task['start_time'] = $task['start_time'] ? Pw::time2str($task['start_time'], 'Y-m-d') : '';
        $task['end_time'] = $task['end_time'] == PwTaskDm::MAXENDTIME ? '' : Pw::time2str($task['end_time'], 'Y-m-d');
        if ($task['user_groups'] == -1) {
            $this->setOutput(1, 'isAll');
        } else {
            $task['user_groups'] = explode(',', $task['user_groups']);
        }

        $task['conditions'] = unserialize($task['conditions']);
        $task['reward'] = unserialize($task['reward']);

        //[任务奖励/完成条件]
        /* @var $taskExtends PwTaskExtends */
        $taskExtends = Wekit::load('APPS:task.service.PwTaskExtends');
        $this->setOutput($taskExtends->getRewardTypeList($task['reward']), 'rewardList');
        $this->setOutput($taskExtends->getConditionTypeList($task['conditions']), 'conditionList');

        $pre_tasks = $this->_taskService()->getPreTasksByTaskId($id);
        $this->setOutput($pre_tasks, 'pre_tasks');
        $this->setOutput($task, 'task');
        $this->setOutput($groups, 'groups');
        $this->setOutput(Pw::time2str(Pw::getTime(), 'Y-m-d'), '_current');
    }

    /**
     * 编辑任务提交.
     */
    public function doEditAction()
    {
        $id = $this->getTaskId();
        $task = $this->_taskDs()->get($id);
        if (!$task) {
            $this->showError('TASK:id.illegal');
        }
        $dm = $this->setDm($id);
        $dm->setIsOpen($task['is_open']);
        if (($r = $this->_taskDs()->updateTask($dm)) instanceof PwError) {
            $this->showError($r->getError());
        }
        if (($dm->getField('icon') != $task['icon']) && $task['icon']) {
            Pw::deleteAttach($task['icon']);
        }
        $this->showMessage('TASK:edittask.success', 'task/manage/run');
    }

    /**
     * 删除任务
     */
    public function delAction()
    {
        $id = $this->getTaskId();
        if (($r = $this->_taskService()->deleteTask($id)) instanceof PwError) {
            $this->showError($r->getError());
        }
        $this->showMessage('TASK:del.success');
    }

    /**
     * @return PwTaskService
     */
    private function _taskService()
    {
        return Wekit::load('task.srv.PwTaskService');
    }

    /**
     * @return PwTask
     */
    private function _taskDs()
    {
        return Wekit::load('task.PwTask');
    }

    /**
     * 设置dm.
     *
     * @return PwTaskDm
     */
    private function setDm($id)
    {
        $condition = $this->getInput('condition');
        $dm = PwTaskDmFactory::getInstance($condition['type'], $condition['child']);
        PwTaskDmFactory::addRewardDecoration($dm, $this->getInput('reward'));

        $icon = $this->saveIcon();
        $user_groups = $this->getInput('user_groups');
        $is_display_all = $this->getInput('is_display_all');
        /*如果全选用户组，则设置该用户组为-1*/
        /* @var $userGroup PwUserGroups */
        $userGroup = Wekit::load('usergroup.PwUserGroups');
        $groups = $userGroup->getAllGroups();
        if (!$user_groups || !array_diff(array_keys($groups), $user_groups)) {
            $user_groups = [-1];
        }
        $startTime = $this->getInput('start_time');
        $endTime = $this->getInput('end_time');
        $dm->setTaskId($id)->setTitle($this->getInput('title'))
            ->setDescription($this->getInput('description'))
            ->setIcon($icon)
            ->setStartTime($startTime ? Pw::str2time($startTime) : 0)
            ->setEndTime($endTime ? Pw::str2time($endTime.' 23:59:59') : PwTaskDm::MAXENDTIME)
            ->setPeriod($this->getInput('period'))
            ->setPreTask($this->getInput('pre_task'))
            ->setUserGroups($user_groups)
            ->setIsAuto($this->getInput('is_auto'))
            ->setIsDisplayAll($is_display_all)
            ->setConditions($condition);

        return $dm;
    }

    /**
     * 获取任务id.
     *
     * @return int
     */
    private function getTaskId()
    {
        $id = intval($this->getInput('id'));
        $this->setOutput($id, 'id');

        return $id;
    }

    /**
     * 上传图标.
     *
     * @return string
     */
    private function saveIcon()
    {
        $taskUpload = new PwTaskIconUpload(80, 80);
        $upload = new PwUpload($taskUpload);
        if (($result = $upload->check()) === true) {
            $result = $upload->execute();
        }
        if ($result !== true) {
            $this->showError($result->getError());
        }
        $path = $taskUpload->getPath();

        return $path ? $path : $this->getInput('oldicon');
    }
}
