<?php


/**
 * 任务服务
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: PwTaskService.php 24025 2013-01-21 03:18:31Z xiaoxia.xuxx $
 */
class PwTaskService
{
    /**
     * 根据用户的行为数据分析并发送自动任务
     *
     * @param array $behavior
     *
     * @return bool
     */
    public function sendAutoTask($behavior)
    {
        if (!Wekit::C('site', 'task.isOpen')) {
            return false;
        }
        $whitBehavior = array('login_days', 'post_days', 'thread_count');
        if (!in_array($behavior['behavior'], $whitBehavior)) {
            return false;
        }
        $tody = Pw::time2str(Pw::getTime(), 'Y-m-d');
        $isSend = false;
        switch ($behavior['behavior']) {
            case 'login_days':
                $isSend = $behavior['number'] % 3 == 0;
                break;
            case 'post_days':
                $is_tody = ($tody == Pw::time2str($behavior['extend_info'], 'Y-m-d'));
                $isSend = $is_tody && $behavior['number'] % 2 == 0;
                break;
            case 'thread_count':
                $isSend = $behavior['number'] == 1 || $behavior['number'] % 50 == 0;
                break;
        }
        if (!$isSend) {
            return false;
        }

        $userTask = new PwTaskApply($behavior['uid']);
        $userTask->autoApplies($this->getAutoApplicableTaskList($behavior['uid'], 1));

        return true;
    }

    /**
     * 开启一个任务
     *
     * @param int    $id
     * @param int    $status 开启1或是关闭0
     * @param int    $order  顺序
     * @param string $title  标题
     *
     * @return PwError|bool
     */
    public function openTask($id, $status, $order = '', $title = '')
    {
        $task = $this->_taskDs()->get($id);
        if (!$task) {
            return new PwError('TASK:id.illegal');
        }
        $taskDm = new PwTaskDm();
        $reward = unserialize($task['reward']);
        $taskDm->setTaskId($id)
            ->setTitle($title)
            ->setViewOrder($order)
            ->setIsOpen($status)
            ->setEndTime($task['end_time'])
            ->setIsAuto($task['is_auto'])
            ->setIsDisplayAll($task['is_display_all'])
            ->setUserGroups(explode(',', $task['user_groups']))
            ->setReward($reward);
        PwTaskDmFactory::addRewardDecoration($taskDm, $reward);

        return $this->_taskDs()->updateTask($taskDm);
    }

    /**
     * 删除一条任务
     * 1：任务信息表
     * 2：任务-用户组 关系表
     * 3：任务-用户 关系表.
     *
     * @param int $id 任务ID
     *
     * @return PwError|bool
     */
    public function deleteTask($id)
    {
        $task = $this->_taskDs()->get($id);
        if (!$task) {
            return true;
        }
        Pw::deleteAttach($task['icon']);
        $this->_taskUserDs()->delete($id);
        $r = $this->_taskDs()->deleteTask($id);
        if ($r instanceof PwError) {
            return $r;
        }

        return true;
    }

    /**
     * 获取任务列表.
     *
     * @param int $page 查询页数
     * @param int $num  返回条数
     *
     * @return array
     */
    public function getTaskList($page = 1, $num = 10)
    {
        $page = abs(intval($page));
        $num = abs(intval($num));
        $start = ($page - 1) * $num;
        $list = $this->_taskDs()->getTaskList($num, $start);
        $nextTask = $this->_taskDs()->fetchNextTaskList(array_keys($list));
        $lang = Wind::getComponent('i18n');
        $taskDb = array();
        foreach ($list as $k => $v) {
            $taskDb[$k] = array(
                'view_order' => $v['view_order'],
                'title'      => $v['title'],
                'is_open'    => $v['is_open'],
                'reward'     => unserialize($v['reward']),
                'start_time' => $v['start_time'],
                'end_time'   => $v['end_time'], );
            if (isset($nextTask[$k])) {
                $taskDb[$k]['msg'] = $lang->getMessage('TASK:delete.error.has.next.task', array('{title}' => $nextTask[$k]['title']));
            }
        }

        return $taskDb;
    }

    /**
     * 获取用户可申领的任务列表.
     *
     * 条件：
     * 1：该用户所在用户组有申领权限的，或是该任务是对任何用户开放的
     * 2：该任务是当前用户没有申领过的（或是用户申领过的周期任务【周期任务除外】)
     * 实现：1、分页实现
     * 2、任务状态
     * 2-1：任务是否已经过期
     * 2-2：任务未开启
     * 2-3：任务的前置任务显示
     *
     * @param int $uid  用户ID
     * @param int $page 页数
     * @param int $num  条数
     *
     * @return array array(count, array())
     */
    public function getApplicableTaskList($uid, $page = 1, $num = 10)
    {
        if (0 > ($uid = intval($uid))) {
            return array(0, array());
        }
        $gids = $this->_getGidsByUid($uid);
        $noPeriodTaskIds = $this->_taskUserDs()->getByIsPeriod($uid, 0);
        $page = abs(intval($page));
        $num = abs(intval($num));
        $start = ($page - 1) * $num;
        $count = $this->_taskDs()->countApplicableTasks(array_keys($noPeriodTaskIds), $gids, Pw::getTime());
        $taskIds = $this->_taskDs()->getApplicableTasks(array_keys($noPeriodTaskIds), $gids, $num, $start, Pw::getTime());

        return array($count, $this->_buildTaskList($this->_taskDs()->gets(array_keys($taskIds))));
    }

    /**
     * 获得用户正在进行中的任务列表.
     *
     * 条件：
     * 1：用户申领成功的任务
     * 2：用户没有完成申领奖励的任务
     * 实现：
     * 1、分页实现
     * 2、任务状态
     * 2-1、是否任务已经过期：过期的任务不可进行接下去的操作
     * 2-2、是否任务已经关闭：已经关闭的任务不可进行接下去的操作
     * 2-3、是否任务已经完成：状态显示为可申领奖励
     * 已过期，已完成，正在进行中
     *
     * @param int $uid 用户ID
     * @param int statu 用户任务类型，PwTask::DOING/UNREWARD/COMPLETE中的组合
     * @param int $page 页数
     * @param int $num  返回数量
     *
     * @return array array(count, array())
     */
    public function getMyTaskListWithStatu($uid, $statu = PwTaskUser::COMPLETE, $page = 1, $num = 10)
    {
        if (0 > ($uid = intval($uid))) {
            return array();
        }
        $page = abs(intval($page));
        $num = abs(intval($num));
        $start = ($page - 1) * $num;
        $myTasks = $this->_taskUserDs()->getMyTaskByStatus($uid, $statu, $num, $start);
        $taskList = $this->_taskDs()->gets(array_keys($myTasks));

        return $this->_buildMyTaskList($taskList, $myTasks);
    }

    /**
     * 自动申领列表：
     * 条件：
     * 1：从task_cache表中获取当前用户最后一个完成的任务ID及周期任务ID列表
     * 2：任务是自动的
     * 3：任务没有过期
     * 4：当前用户所在用户组有权限申请任务
     * 5：并且该任务ID是当前人未申领自动任务中最小的（>最后一个完成的任务ID limit 1)
     * 6：返回任务ID列表：条件2-5产生的任务ID+条件1得到的周期任务ID列表
     * 实现：
     * 1：推送给用户
     * 2： 用户执行acceptTask.
     *
     * @param int $uid   用户ID
     * @param int $limit 条数
     *
     * @return array
     */
    public function getAutoApplicableTaskList($uid, $limit = 1)
    {
        $cacheIds = $this->_taskDs()->getTaskCacheByUid($uid);
        $gids = $this->_getGidsByUid($uid);
        $ids = $this->_taskDs()->getAutoApplicableTask($cacheIds[0], $gids, abs(intval($limit)));
        $periods = isset($cacheIds[1]) ? (array) $cacheIds : array();

        return array_unique(array_keys($ids) + $periods);
    }

    /**
     * 获得任务的自动后置任务
     *
     * @param int $taskid 任务ID
     * @param int $uid    用户ID
     *
     * @return array
     */
    public function getNextAutoApplicableTaskList($taskid, $uid)
    {
        $taskList = $this->_taskDs()->getNextAutoTasks($taskid, Pw::getTime(), Pw::getTime());
        $gids = $this->_getGidsByUid($uid);
        $taskIds = array();
        foreach ($taskList as $id => $task) {
            $_tmp = explode(',', $task['user_groups']);
            if (array_intersect($_tmp, $gids)) {
                $taskIds[] = $id;
            }
        }

        return $taskIds;
    }

    /**
     * 获取一个任务的可选择前置任务
     *
     * @param int $taskid
     *
     * @return array
     */
    public function getPreTasksByTaskId($taskid)
    {
        $tasks = $this->_taskDs()->getAll();

        $taskTree = PwTaskTreeHelper::getTaskTree($tasks);
        unset($tasks[$taskid]);
        if ($taskid) {
            PwTaskTreeHelper::clearAllNextNode($taskTree[$taskid], $tasks, $taskid);
        }

        return empty($tasks) ? array() : PwTaskTreeHelper::cookTree($taskTree['root']['items'],
            array_keys($tasks));
    }

    /**
     * 构建我的任务列表.
     *
     * 每个任务有三个状态信息：
     * 1-tag: 右上角显示:1:已领取，2：已关闭，3：已结束，4：正在进行中，5: 领取奖励--已完成，
     * 2-continue: 按钮是否可以使用
     * 3-percent: 任务进行的进度
     *
     * @param array $taskList 任务信息
     * @param array $myTask   我的任务信息
     *
     * @return array
     */
    private function _buildMyTaskList($taskList, $myTask)
    {
        $time = Pw::getTime();
        foreach ($taskList as $id => &$task) {
            $task['tag'] = 1;
            $task['continue'] = true;
            if ($task['is_open'] == 0) {
                $task['tag'] = 2;
                $task['continue'] = false;
            } else {
                if ($task['end_time'] && ($task['end_time'] < $time)) {
                    $task['tag'] = 3;
                    $task['continue'] = false;
                } elseif (2 == $myTask[$id]['task_status']) {
                    $task['tag'] = 5;
                }
            }
            /*if ($task['pre_task']) {
                $pre_task = $this->_taskDs()->get($task['pre_task']);
                $task['parent'] = $pre_task['title'];
            }*/
            $_tmp = unserialize($task['conditions']);
            $task['conditions'] = is_array($_tmp) ? $_tmp : array('url' => 'run');
            $_tmp = unserialize($task['reward']);
            $task['reward'] = is_array($_tmp) ? $_tmp : array('descript' => '');
            /*任务完成进度：step中保存percent元素*/
            $step = unserialize($myTask[$id]['step']);
            $task['percent'] = is_array($step) && isset($step['percent']) ? $step['percent'] : '';
            /*已有进度并且状态为已领取的变为正在进行中*/
            if ($task['percent'] && $task['tag'] == 1) {
                $task['tag'] = 4;
            }
        }

        return $taskList;
    }

    /**
     * 构建任务输出列表.
     *
     * 每个任务有两个附加信息：
     * 1-parent:前置任务信息
     * 2-reward: 奖励描述
     *
     * @param array $taskList 任务列表
     *
     * @return array
     */
    private function _buildTaskList($taskList)
    {
        $time = Pw::getTime();
        foreach ($taskList as $id => &$task) {
            /*if ($task['pre_task']) {
                $pre_task = $this->_taskDs()->get($task['pre_task']);
                $task['parent'] = $pre_task['title'];
            }*/
            $_tmp = unserialize($task['conditions']);
            $task['conditions'] = is_array($_tmp) ? $_tmp : array('url' => 'run');
            $_tmp = unserialize($task['reward']);
            $task['reward'] = is_array($_tmp) ? $_tmp : array('descript' => '');
        }

        return $taskList;
    }

    /**
     * 根据用户ID获得该用户所拥有的用户组ID列表.
     *
     * @param int   $uid  用户ID
     * @param array $gids 用户组ID列表
     *
     * @return array
     */
    private function _getGidsByUid($uid)
    {
        /* @var $userService PwUserService */
        $userService = Wekit::load('user.srv.PwUserService');
        $gids = $userService->getGidsByUid($uid);
        /*gid等于-1则该任务对所有用户组有效*/
        $gids[] = -1;

        return $gids;
    }

    /**
     * 任务DS.
     *
     * @return PwTask
     */
    private function _taskDs()
    {
        return Wekit::load('task.PwTask');
    }

    /**
     * 用户任务ds.
     *
     * @return PwTaskUser
     */
    private function _taskUserDs()
    {
        return Wekit::load('task.PwTaskUser');
    }
}
