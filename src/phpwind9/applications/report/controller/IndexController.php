<?php

/**
 * 举报Controller.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class IndexController extends PwBaseController
{
    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        if (!$this->loginUser->isExists()) {
            $this->forwardAction('u/login/run', ['backurl' => WindUrlHelper::createUrl('my/article/run')]);
        }
        if (!$this->loginUser->getPermission('allow_report')) {
            $this->showError(['report.allow', ['{grouptitle}' => $this->loginUser->getGroupInfo('name')]]);
        }
    }

    /**
     * 举报弹窗.
     */
    public function reportAction()
    {
        list($type, $type_id) = $this->getInput(['type', 'type_id']);
        $this->setOutput($type, 'type');
        $this->setOutput($type_id, 'type_id');
    }

    /**
     * do举报.
     */
    public function doReportAction()
    {
        list($type, $type_id, $reason) = $this->getInput(['type', 'type_id', 'reason'], 'post');
        if (!$type_id) {
            $this->showError('operate.fail');
        }
        $report = Wekit::load('report.srv.PwReportService');
        $result = $report->sendReport($type, $type_id, $reason);
        if ($result instanceof PwError) {
            $this->showError($result->getError());
        }
        $this->showMessage('success');
    }
}
