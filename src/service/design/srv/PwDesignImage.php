<?php


/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignImage.php 24487 2013-01-31 02:50:53Z gao.wanggao $
 * @package
 */
class PwDesignImage
{
    protected $store = '';
    protected $moduleid = 0;
    protected $thumbW = 0;
    protected $thumbH = 0;
    protected $ext = '';
    protected $image = '';

    public function setInfo($moduleid, $image, $thumbW, $thumbH)
    {
        $this->moduleid = (int) $moduleid;
        $this->thumbW = (int) $thumbW;
        $this->thumbH = (int) $thumbH;
        $this->ext = strtolower(substr(strrchr($image, '.'), 1));
        $this->image = $image;
        $this->store = Wind::getComponent('storage');
    }

    /*public function setStore() {
        $this->store = Wind::getComponent('storage');
    }*/

    public function cut()
    {
        static $isDel = false;
        $outFile = $this->getFileName();
        $outDir = $this->getSaveDir($this->moduleid);
        $cut = new PwCutImage();
        $image = $this->getRealPath($outFile);
        if (!$image) {
            return array('', '', $this->store->get($this->image, 0));
        } //返回原图片
        $cut->image = $image;
        $cut->outImage = Wind::getRealDir('PUBLIC:').PUBLIC_ATTACH.'/'.$outDir.$outFile;
        $cut->cutWidth = $this->thumbW;
        $cut->cutHeight = $this->thumbH;
        $cut->quality = 90;
        $cut->forceThumb = true;
        $cut->forceScale = true;
        if ($cut->cut() !== false) {
            if (!$this->store instanceof PwStorageLocal) {
                $localFile = Wind::getRealDir('PUBLIC:').PUBLIC_ATTACH.'/'.$outDir.$outFile;
                $this->store->save($localFile, $outDir.$outFile);
                $attachUrl = $this->store->get('', 0);

                WindFile::del(Wind::getRealDir('PUBLIC:').PUBLIC_ATTACH.'/_tmp/'.$outFile);
                WindFile::del($localFile);
            } else {
                $attachUrl = Wekit::url()->attach.'/';
            }

            return array($outDir, $outFile, $attachUrl);
        }

        return array('', '', $this->store->get($this->image, 0)); //返回原图片
    }

    public function clearFolder($moduleid)
    {
        if (!$moduleid) {
            return false;
        }
        $dir = $this->getSaveDir($moduleid);
        $store = Wind::getComponent('storage'); //单独使用
        if (!$store instanceof PwStorageLocal) {
            $store->delete($dir, 0);
        } else {
            $dir = Wind::getRealDir('PUBLIC:').PUBLIC_ATTACH.'/'.$dir;
            WindFolder::clearRecur($dir, true);
        }

        return true;
    }

    public function clearFiles($moduleid, $images)
    {
        if (!$images || !is_array($images)) {
            return false;
        }
        $dir = $this->getSaveDir($moduleid);
        $store = Wind::getComponent('storage'); //单独使用
        if (!$store instanceof PwStorageLocal) {
            foreach ($images as $image) {
                $store->delete($dir.$image, 0);
            }
        } else {
            $dir = Wind::getRealDir('PUBLIC:').PUBLIC_ATTACH.'/'.$dir;
            foreach ($images as $image) {
                is_file($dir.$image) && WindFile::del($dir.$image);
            }
        }
    }

    protected function getRealPath($outFile)
    {
        if (!$this->store instanceof PwStorageLocal) {
            $localDir = Wind::getRealDir('PUBLIC:').PUBLIC_ATTACH.'/_tmp/';
            $path = $this->getImage($this->store->get($this->image, 0), $localDir, $outFile);
        } else {
            $path = Wind::getRealDir('PUBLIC:').PUBLIC_ATTACH.'/'.$this->image;
        }

        return $path;
    }

    protected function getFileName()
    {
        $num = 6;
        $filename = $this->thumbW.'_'.$this->thumbH.'_';
        $str = '0123456789abcdefghjkmnopqrstuvwxyABCDEFGHJKLMNOPQRSTUVWXY';
        $len = Pw::strlen($str) - 1;
        for ($i = 0; $i < $num; $i++) {
            $_num = mt_rand(0, $len);
            $filename .= substr($str, $_num, 1);
        }
        $filename .= '.'.$this->ext;

        return $filename;
    }

    protected function getSaveDir($moduleid)
    {
        return 'module/'.$moduleid.'/';
    }

    protected function getImage($url, $path, $filename = '')
    {
        if ($url == '' || $path == '') {
            return false;
        }
        if (!$this->createFolder($path)) {
            return false;
        }
        $ext = strrchr($url, '.');
        if ($ext != '.gif' && $ext != '.jpg' && $ext != '.png') {
            return false;
        }
        $filename = $filename ? $filename : mt_rand(1, 999999).'.'.$ext;
        $filename = $path.$filename;
        if ($this->store instanceof PwStorageFtp) {
            $ftp = Wekit::load('design.srv.ftp.PwDesignFtp');
            $ftp->download($this->image, $filename);
        } else {
            ob_start();
            echo $this->getContents($url);
            $img = ob_get_contents();
            ob_end_clean();
            WindFile::write($filename, $img);
        }

        return $filename;
    }



    protected function getContents($url)
    {
        $timeout = 30;
        $contents = false;
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $contents = curl_exec($ch);
            curl_close($ch);

            return $contents;
        }
        if (function_exists('file_get_contents')) {
            if (function_exists('stream_context_create')) {
                $opts = array(
                    'http' => array(
                        'method' => 'GET',
                        'timeout' => $timeout,
                    ),
                );
                $contents = @file_get_contents($url, false, stream_context_create($opts));
            } else {
                $contents = @file_get_contents($url);
            }
        }

        return $contents;
    }



    private function createFolder($path = '')
    {
        if (!$path) {
            return false;
        }
        if (!is_dir($path)) {
            $this->createFolder(dirname($path));
            if (!@mkdir($path, 0777)) {
                return false;
            }
        }

        return true;
    }
}
