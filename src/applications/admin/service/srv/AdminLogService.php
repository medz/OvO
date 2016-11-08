<?php
/**
 * 后台管理日志服务
 *
 * @author Zerol Lin <zerol.lin@me.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package admin
 * @subpackage service.srv
 */
class AdminLogService
{
    private $logfile = '';

    public function __construct()
    {
        $this->logfile = Wind::getRealPath(Wekit::app()->logFile, true);
        if (!WindFile::isFile($this->logfile)) {
            WindFile::write($this->logfile, "<?php die;?>\n");
        }
    }

    public function log($request, $adminUser, $m, $c, $a)
    {
        $this->addLog($request, $adminUser, "$m/$c/$a");
    }

    public function loginLogFailed($request, $adminUser)
    {
        $this->addLog($request, $adminUser, 'Login Failed');
    }

    public function loginLog($request, $adminUser)
    {
        $this->addLog($request, $adminUser, 'Login Successful');
    }

    public function clear()
    {
        $num = 500;
        $logs = $this->readLog();
        if ($logs[0] < $num) {
            $num = $logs[0];
        }
        $output = array_slice($logs, -$num);
        $output = '<?php die;?>\n'.implode("\n", $output);
        WindFile::write($this->logfile, $output);
    }

    /**
     * Enter description here .
     * ..
     *
     * @param  WindHttpRequest         $request
     * @param  int                     $offset
     * @return array(countLoginFailed, lastLoginTime)
     */
    public function checkLoginFailed($request, $offset = 2048)
    {
        $logs = $this->readLog($offset);
        if ($logs[0] <= 0) {
            return 0;
        }

        $online_ip = $request->getClientIp();
        $countLoginFailed = $lastLoginTime = 0;
        for ($i = $logs[0]; $i > 0; $i--) {
            if (false !== strpos($logs[$i], "|Login Failed|$online_ip|")) {
                $countLoginFailed++;
                if (empty($lastLoginTime)) {
                    $record = explode('|', $logs[$i]);
                    $lastLoginTime = (int) $record[4];
                }
            }
        }

        return array($countLoginFailed, $lastLoginTime);
    }

    public function readLog($offset = 1024000)
    {
        $fp = @fopen($this->logfile, 'rb');
        if (!$fp) {
            return array(0);
        }

        flock($fp, LOCK_SH);
        $size = filesize($this->logfile);
        $size > $offset ? fseek($fp, -$offset, SEEK_END) : $offset = $size;
        $logs = $offset > 0 ? fread($fp, $offset) : '';
        fclose($fp);

        $logs = explode("\n", trim($logs));
        $logs[0] = count($logs) - 1;

        return $logs;
    }

    private function addLog($request, $adminUser, $router_info)
    {
        $admin_name = str_replace('|', '&#124;', $adminUser);
        $router_info = str_replace('|', '&#124;', $router_info);
        $post_data = $request->isPost() ? $this->arr2str($request->getPost()) : '';
        $request_uri = str_replace('|', '&#124;', $request->getRequestUri());
        $online_ip = $request->getClientIp();
        $timestamp = time();
        $record = "|$admin_name|$router_info|$online_ip|$timestamp|$request_uri|$post_data|\n";
        WindFile::write($this->logfile, $record, 'ab');
    }

    private function arr2str($log)
    {
        $log = (array) $log;
        $data = '';
        foreach ($log as $key => $val) {
            $key = str_replace(array("\n", "\r", '|'), array('\n', '\r', '&#124;'), $key);
            if (is_array($val)) {
                $data .= "$key=array(".$this->arr2str($val).')';
            } else {
                $val = str_replace(array("\n", "\r", '|'), array('\n', '\r', '&#124;'), $val);
                if ($key == 'password' || $key == 'repassword') {
                    $data .= "$key=***, ";
                } else {
                    $data .= "$key=$val, ";
                }
            }
        }

        return rtrim($data, ', ');
    }
}
