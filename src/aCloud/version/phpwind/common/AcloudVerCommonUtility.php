<?php
/**
 * @author jinling.sujl <emily100813@gmail.com> 2010-11-2
 *
 * @link http://www.phpwind.com
 *
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
class AcloudVerCommonUtility extends ACloudVerCommonBase
{
    /**
     * 获取缩略图片.
     *
     * @param string $attachurl
     * @param int    $ifthumb   $ifthumb&1：原图      $ifthumb&2：缩略图     $ifthumb&1：mini图
     *
     * @return string
     */
    public function getThumbAttach($attachurl, $ifthumb = false)
    {
        $attach = Pw::getPath($attachurl, 0);
        $thumbAttach = $ifthumb ? Pw::getPath($attachurl, $ifthumb) : '';

        return array(0, array($attach, $thumbAttach));
    }

    /**
     * 获取mini图地址
     *
     * @param string $path
     * @param int    $ifthumb $ifthumb&1：原图      $ifthumb&2：缩略图     $ifthumb&1：mini图
     *
     * @return string
     */
    public function getMiniUrl($path, $ifthumb, $where = null)
    {
        return array(0, Pw::getPath($path, $ifthumb));
    }

    public function makeThumb(PwImage $image, $thumbInfo, $store)
    {
        $quality = Wekit::C('attachment', 'thumb.quality');
        foreach ($thumbInfo as $key => $value) {
            $thumburl = $store->getAbsolutePath($value[0], $value[1]);
            PwUpload::createFolder(dirname($thumburl));
            $result = $image->makeThumb($thumburl, $value[2], $value[3], $quality, $value[4], $value[5]);
            if ($result === true && $image->filename != $thumburl) {
                $this->ifthumb |= (1 << $key);
                $this->_thumb[] = array($thumburl, $value[1].$value[0]);
            }
        }
    }

    private function setStore()
    {
        $this->store = Wind::getComponent('storage');
    }
}
