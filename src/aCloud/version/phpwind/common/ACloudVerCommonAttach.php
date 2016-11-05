<?php

! defined('ACLOUD_PATH') && exit('Forbidden');

define('ATTACH_INVALID_PARAMS', 851);

class ACloudVerCommonAttach extends ACloudVerCommonBase
{
    public function getPrimaryKeyAndTable()
    {
        return array('attachs_thread', 'aid');
    }

    /**
     *
     * 获取帖子图片附件地址信息
     *
     * @param  array $aids
     * @return array 图片地址
     */
    public function getImgAttaches($aids)
    {
        if (!ACloudSysCoreS::isArray($aids)) {
            return $this->buildResponse(ATTACH_INVALID_PARAMS);
        }
        $_attaches = $this->_getThreadAttach()->fetchAttach($aids);
        $attaches = array();
        foreach ($_attaches as $v) {
            if ($v['type'] != 'img') {
                continue;
            }
            $attaches[$v['aid']] = array('url' => Pw::getPath($v['path'], 0));
        }

        return $this->buildResponse(0, $attaches);
    }

    public function getImgAttachesByTids($tids)
    {
        if (!ACloudSysCoreS::isArray($tids)) {
            return $this->buildResponse(ATTACH_INVALID_PARAMS);
        }
        $_attaches = $this->_getThreadAttach()->fetchAttachByTidsAndPid($tids);
        $attaches = array();
        foreach ($_attaches as $v) {
            if ($v['type'] != 'img') {
                continue;
            }
            $attaches[$v['tid']][] = Pw::getPath($v['path']);
        }

        return $this->buildResponse(0, $attaches);
    }

    public function getAttachsByRange($startId, $endId)
    {
        list($startId, $endId) = array(intval($startId), intval($endId));
        if ($startId < 0 || $startId > $endId || $endId < 1) {
            return array();
        }
        $sql = sprintf('SELECT * FROM %s WHERE aid >= %s AND aid <= %s', ACloudSysCoreS::sqlMetadata('{{attachs_thread}}'), ACloudSysCoreS::sqlEscape($startId), ACloudSysCoreS::sqlEscape($endId));
        $query = Wind::getComponent('db')->query($sql);
        $result = $query->fetchAll(null, PDO::FETCH_ASSOC);
        if (! ACloudSysCoreS::isArray($result)) {
            return array();
        }

        return $this->_buildAttachData($result);
    }

    public function getAttachDirectories()
    {
        $storage = Wind::getComponent('storage');
        if (!$storage instanceof PwStorageLocal) {
            return array();
        }
        $attachDir = Wind::getRealDir('PUBLIC:').PUBLIC_ATTACH;

        return $this->_listDirectories($attachDir);
    }

    /**
     *
     * 云存储api（同步附件）
     *
     * @param string $dir
     */
    public function getAttachesForStorage($dir)
    {
        $attachDir = Wind::getRealDir('PUBLIC:').PUBLIC_ATTACH ;
        $dir = trim($dir);
        if (! $dir) {
            return array();
        }
        $result = array();
        $baseUrl = Pw::substrs(PUBLIC_URL, strrpos(PUBLIC_URL, 'aCloud'), 0, false);
        foreach (glob($attachDir.$dir.'/*') as $fileName) {
            if ($fileName == '.' || $fileName == '..' || preg_match('/\.(htm|html|db)$/i', $fileName)) {
                continue;
            }
            $result [] = array('attachurl' => str_replace(Wind::getRealDir('PUBLIC:'), $baseUrl, $fileName), 'dir' => str_replace(Wind::getRealDir('PUBLIC:'), '', $fileName));
        }

        return $result;
    }

    private function _listDirectories($currentDir)
    {
        $dirs = array();
        foreach (glob($currentDir.'/*', GLOB_ONLYDIR) as $dir) {
            if ($dir == '.' || $dir == '..') {
                continue;
            }
            $tmp = $this->_listDirectories($dir);
            $tmp [] = array('dir' => str_replace(array(Wind::getRealDir('PUBLIC:').PUBLIC_ATTACH, '\\'), array('', '/'), $dir));
            $dirs = array_merge($dirs, $tmp);
        }

        return $dirs;
    }

    private function _buildAttachData($data)
    {
        $result = array();
        foreach ($data as $value) {
            $value ['attachurl'] = Pw::getPath($value ['path'], $value ['ifthumb']);
            $result [$value ['aid']] = $value;
        }

        return $result;
    }

    private function _getThreadAttach()
    {
        return Wekit::load('SRV:attach.PwThreadAttach');
    }
}
