<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignFtp.php 22612 2012-12-25 14:02:14Z gao.wanggao $
 */
class PwDesignFtp
{
    private $_config;
    private $_ftp = null;

    public function __construct()
    {
        $this->_config = Wekit::C('attachment');
    }

    public function delete($path)
    {
        return $this->getFtp()->delete($path);
    }

    public function upload($sourceFile, $desFile)
    {
        return $this->getFtp()->upload($sourceFile, $desFile);
    }

    public function download($filename, $localname = '')
    {
        return $this->getFtp()->download($localname, $filename);
    }

    public function getFtp()
    {
        if ($this->_ftp == null) {
            $this->_ftp = new WindSocketFtp(array(
                'server'  => $this->_config['ftp.server'],
                'port'    => $this->_config['ftp.port'],
                'user'    => $this->_config['ftp.user'],
                'pwd'     => $this->_config['ftp.pwd'],
                'dir'     => $this->_config['ftp.dir'],
                'timeout' => $this->_config['ftp.timeout'],
            ));
        }

        return $this->_ftp;
    }
}
