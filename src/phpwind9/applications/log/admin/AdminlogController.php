<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * Enter description here .
 * ..
 *
 * @author Zerol Lin <zerol.lin@me.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class AdminlogController extends AdminBaseController
{
    public function run()
    {
        $keyword = $this->getInput('keyword');
        $page = (int) $this->getInput('page');

        /* @var $menuService AdminMenuService */
        $menuService = Wekit::load('ADMIN:service.srv.AdminMenuService');
        $authStruts = $menuService->getMenuAuthStruts();
        $menus = $menuService->getMenuTable();

        /* @var $logService AdminLogService */
        $logService = Wekit::load('ADMIN:service.srv.AdminLogService');
        $logs = $logService->readLog();

        $count = $logs[0];
        $perpage = 10;
        $page < 1 && $page = 1;
        if ($count % $perpage == 0) {
            $numofpage = floor($count / $perpage);
        } else {
            $numofpage = floor($count / $perpage) + 1;
        }
        if ($page > $numofpage) {
            $page = $numofpage;
        }
        $pagemin = min(($page - 1) * $perpage, $count);
        $pagemax = min($pagemin + $perpage - 1, $count);

        $num = 0;
        for ($i = $logs[0]; $i > 0; $i--) {
            if (empty($keyword) || false !== strpos($logs[$i], $keyword)) {
                if ($num >= $pagemin && $num <= $pagemax) {
                    $detail = explode('|', $logs[$i]);
                    if (false !== strpos($detail[2], '/')) {
                        list($m, $c, $a) = explode('/', $detail[2]);
                        $authKeys = [];
                        if (isset($authStruts[$m][$c][$a])) {
                            $authKeys += $authStruts[$m][$c][$a];
                        }
                        if (isset($authStruts[$m][$c]['_all'])) {
                            $authKeys += $authStruts[$m][$c]['_all'];
                        }
                        !empty($authKeys) && $detail[2] = $menus[$authKeys[0]]['name'];
                    }
                    $result[] = $detail;
                }
                $num++;
            }
        }

        $this->setOutput($this->isFounder($this->loginUser->username), 'isFound');
        $this->setOutput($keyword, 'keyword');
        $this->setOutput($result, 'logs');
        $this->setOutput(['keyword' => $keyword], 'searchData');
        $this->setOutput($page, 'page');
        $this->setOutput($num, 'count');
        $this->setOutput($perpage, 'perpage');

        $this->setTemplate('manage_admin');
    }

    public function clearAction()
    {
        if (!$this->isFounder($this->loginUser->username)) {
            $this->showError('fail');
        }

        /* @var $logService AdminLogService */
        $logService = Wekit::load('ADMIN:service.srv.AdminLogService');
        $logService->clear();
        $this->showMessage('success');
    }
}
