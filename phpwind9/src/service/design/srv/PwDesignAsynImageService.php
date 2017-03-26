<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignAsynImageService.php 23371 2013-01-09 06:18:14Z gao.wanggao $
 */
class PwDesignAsynImageService
{
    public function get($id)
    {
        $image = $this->_getImageDs()->getImage($id);
        if (! $image['status']) {
            return '';
        }
        if (! $image['thumb']) {
            return $this->asynThumb($image);
        }

        return $image['thumb'];
    }

    public function asynThumb($image)
    {
        $srv = Wekit::load('design.srv.PwDesignImage');
        $srv->setInfo($image['moduleid'], $image['path'], $image['width'], $image['height']);
        $thumb = $srv->cut();

        $dm = new PwDesignAsynImageDm($image['id']);
        list($dir, $filename, $url) = $thumb;
        if (! $dir) {
            $dm->setStatus(1)
                ->setThumb($url);
            $this->_getImageDs()->updateImage($dm);
            $thumbUrl = $url;
            $filename = '';
        } else {
            $thumbUrl = $url.$dir.$filename;
            $dm->setStatus(1)
                ->setThumb($url.$dir.$filename);
            $this->_getImageDs()->updateImage($dm);
        }
        $this->updateDesignData($image['data_id'], $image['sign'], $thumbUrl, $filename);

        return $thumbUrl;
    }

    public function updateDesignData($dataid, $sign, $thumbUrl, $thumb)
    {
        $ds = $this->_getDataDs();
        $data = $ds->getData($dataid);
        $extend = unserialize($data['extend_info']);
        if ($thumbUrl) {
            $extend['standard_image'] .= $thumb.'|||';
            $extend[$sign] = $thumbUrl;
        }
        $dm = new PwDesignDataDm($dataid);
        $dm->setExtend($extend);
        $ds->updateData($dm);

        return '';
    }

    private function _getImageDs()
    {
        return Wekit::load('design.PwDesignAsynImage');
    }

    private function _getDataDs()
    {
        return Wekit::load('design.PwDesignData');
    }
}
