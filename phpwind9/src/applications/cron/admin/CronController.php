<?php

Wind::import('ADMIN:library.AdminBaseController');

 /**
  * the last known user to change this file in the repository  <$LastChangedBy$>.
  *
  * @author $Author$ Foxsee@aliyun.com
  * @copyright ©2003-2103 phpwind.com
  * @license http://www.phpwind.com
  *
  * @version $Id$
  */
 class CronController extends AdminBaseController
 {
     public function run()
     {
         $list = $this->_getCronDs()->getList();
         foreach ($list as $key => &$cron) {
             //if ($cron['isopen'] == 2) unset($list[$key]);
            $cron['type'] = $this->_getLoopType($cron['loop_type']);
             list($day, $hour, $minute) = explode('-', $cron['loop_daytime']);
             if ($cron['loop_type'] == 'week') {
                 $cron['type'] .= '星期'.$this->_capitalWeek($day);
             } elseif ($day == 99) {
                 $cron['type'] .= '最后一天';
             } else {
                 $cron['type'] .= $day ? $day.'日' : '';
             }
             if ($cron['loop_type'] == 'week' || $cron['loop_type'] == 'month') {
                 $cron['type'] .= $hour.'时';
             } else {
                 $cron['type'] .= $hour ? $hour.'时' : '';
             }

             $cron['type'] .= $minute ? $minute.'分' : '00分';
         }
         $this->setOutput($list, 'list');
     }

     public function addAction()
     {
         $this->setOutput($this->_getCronFileList(), 'fileList');
         $this->setOutput($this->_getLoopType(), 'loopType');
     }

     public function doaddAction()
     {
         $type = $this->getInput('looptype', 'post');
         $isopen = $this->getInput('isopen', 'post');
         $filename = $this->getInput('filename', 'post');
         $subject = $this->getInput('subject', 'post');
         if (! $subject && ! $filename) {
             $this->showError('operate.fail');
         }
         $dm = new PwCronDm();
         $dm->setSubject($subject)
            ->setLooptype($type)
            ->setCronfile($filename)
            ->setIsopen($isopen)
            ->setCreatedtime(Pw::getTime());
         switch ($type) {
            case 'month':
                $day = $this->getInput('month_day', 'post');
                $hour = $this->getInput('month_hour', 'post');
                $nexttime = $this->_getCronService()->getNextTime('month', $day, $hour);
                $dm->setLoopdaytime($day, $hour)->setNexttime($nexttime);
                break;
            case 'week':
                $day = $this->getInput('week_day', 'post');
                $hour = $this->getInput('week_hour', 'post');
                $nexttime = $this->_getCronService()->getNextTime('week', $day, $hour);
                $dm->setLoopdaytime($day, $hour)->setNexttime($nexttime);
                break;
            case 'day':
                $hour = $this->getInput('day_hour', 'post');
                $nexttime = $this->_getCronService()->getNextTime('day', 0, $hour);
                $dm->setLoopdaytime(0, $hour)->setNexttime($nexttime);
                break;
            case 'hour':
                $minute = $this->getInput('hour_minute', 'post');
                $nexttime = $this->_getCronService()->getNextTime('hour', 0, 0, $minute);
                $dm->setLoopdaytime(0, 0, $minute)->setNexttime($nexttime);
                break;
            case 'now':
                $time = (int) $this->getInput('now_time', 'post');
                $type = $this->getInput('now_type', 'post');
                if (! $time) {
                    $this->showError('operate.fail');
                }
                $minute = $type == 'minute' ? $time : 0;
                $hour = $type == 'hour' ? $time : 0;
                $day = $type == 'day' ? $time : 0;
                $nexttime = $this->_getCronService()->getNextTime('now', $day, $hour, $minute);
                $dm->setLoopdaytime($day, $hour, $minute)->setNexttime($nexttime);
                break;
            default:
                $this->showError('operate.fail');
        }
         if (! $isopen) {
             $dm->setNexttime(0);
         }
         $resource = $this->_getCronDs()->addCron($dm);
         if ($resource instanceof PwError) {
             $this->showError($resource->getError());
         }
         $this->showMessage('operate.success');
     }

     public function editAction()
     {
         $cronId = (int) $this->getInput('id', 'get');
         if ($cronId < 1) {
             $this->showError('operate.fail');
         }
         $info = $this->_getCronDs()->getCron($cronId);
         if (! $info) {
             $this->showError('operate.fail');
         }
         list($info['day'], $info['hour'], $info['minute']) = explode('-', $info['loop_daytime']);
         $this->setOutput($info, 'info');
         $this->setOutput($this->_getCronFileList(), 'fileList');
         $this->setOutput($this->_getLoopType(), 'loopType');
     }

     public function doeditAction()
     {
         $cronId = (int) $this->getInput('id', 'post');
         if ($cronId < 1) {
             $this->showError('operate.fail');
         }
         $type = $this->getInput('looptype', 'post');
         $isopen = $this->getInput('isopen', 'post');
         $filename = $this->getInput('filename', 'post');
         $subject = $this->getInput('subject', 'post');
         if (! $subject && ! $filename) {
             $this->showError('operate.fail');
         }
         $dm = new PwCronDm($cronId);
         $dm->setSubject($subject)
            ->setLooptype($type)
            ->setCronfile($filename)
            ->setIsopen($isopen)
            ->setCreatedtime(Pw::getTime());
         switch ($type) {
            case 'month':
                $day = $this->getInput('month_day', 'post');
                $hour = $this->getInput('month_hour', 'post');
                $nexttime = $this->_getCronService()->getNextTime('month', $day, $hour);
                $dm->setLoopdaytime($day, $hour)->setNexttime($nexttime);
                break;
            case 'week':
                $day = $this->getInput('week_day', 'post');
                $hour = $this->getInput('week_hour', 'post');
                $nexttime = $this->_getCronService()->getNextTime('week', $day, $hour);
                $dm->setLoopdaytime($day, $hour)->setNexttime($nexttime);
                break;
            case 'day':
                $hour = $this->getInput('day_hour', 'post');
                $nexttime = $this->_getCronService()->getNextTime('day', 0, $hour);
                $dm->setLoopdaytime(0, $hour)->setNexttime($nexttime);
                break;
            case 'hour':
                $minute = $this->getInput('hour_minute', 'post');
                $nexttime = $this->_getCronService()->getNextTime('hour', 0, 0, $minute);
                $dm->setLoopdaytime(0, 0, $minute)->setNexttime($nexttime);
                break;
            case 'now':
                $time = (int) $this->getInput('now_time', 'post');
                $type = $this->getInput('now_type', 'post');
                if (! $time) {
                    $this->showError('operate.fail');
                }
                $minute = $type == 'minute' ? $time : 0;
                $hour = $type == 'hour' ? $time : 0;
                $day = $type == 'day' ? $time : 0;
                $nexttime = $this->_getCronService()->getNextTime('now', $day, $hour, $minute);
                $dm->setLoopdaytime($day, $hour, $minute)->setNexttime($nexttime);
                break;
            default:
                $this->showError('operate.fail');
        }
         if (! $isopen) {
             $dm->setNexttime(0);
         }
         $resource = $this->_getCronDs()->updateCron($dm);
         if ($resource instanceof PwError) {
             $this->showError($resource->getError());
         }
         $this->showMessage('operate.success');
     }

     public function dodeleteAction()
     {
         $cronId = (int) $this->getInput('id', 'post');
         if ($cronId < 1) {
             $this->showError('operate.fail');
         }
         $resource = $this->_getCronDs()->deleteCron($cronId);
         if ($resource instanceof PwError) {
             $this->showError($resource->getError());
         }
         $this->showMessage('operate.success');
     }

    /**
     * 导入系统计划任务
     * Enter description here ...
     */
    public function importAction()
    {
        $this->_getCronService()->updateSysCron();
        $this->showMessage('operate.success');
    }

     private function _getLoopType($select = '')
     {
         $array = ['month' => '每月', 'week' => '每周', 'day' => '每日', 'hour' => '每小时', 'now' => '每隔'];

         return $select ? $array[$select] : $array;
     }

     private function _capitalWeek($select = 0)
     {
         $array = ['日', '一', '二', '三', '四', '五', '六'];

         return $array[$select];
     }

     private function _getCronFileList()
     {
         // $dir = Wind::getRealPath(trim('SRV:cron.srv.do.'), false);
         $dir = app('path').'/phpwind9/classmap/service/cron/srv/do/';
         $fileList = WindFolder::read($dir);
         foreach ((array) $fileList as $k => $file) {
             if (Pw::substrs($file, 8, 0, false) != 'PwCronDo') {
                 unset($fileList[$k]);
             }
         }

         return $fileList;
     }

     private function _getCronDs()
     {
         return Wekit::load('cron.PwCron');
     }

     private function _getCronService()
     {
         return Wekit::load('cron.srv.PwCronService');
     }
 }
