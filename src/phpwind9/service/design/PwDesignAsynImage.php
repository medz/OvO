<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignAsynImage.php 22292 2012-12-21 05:09:20Z gao.wanggao $
 */
class PwDesignAsynImage
{
    public function getImage($id)
    {
        if (!$id) {
            return [];
        }

        return $this->_getDao()->get($id);
    }

    public function fetchImage($ids)
    {
        if (empty($ids) || !is_array($ids)) {
            return [];
        }

        return $this->_getDao()->fetch($ids);
    }

    public function addImage(PwDesignAsynImageDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->add($dm->getData());
    }

    public function updateImage(PwDesignAsynImageDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->update($dm->id, $dm->getData());
    }

    public function updateDataId($id, $dataid)
    {
        $id = (int) $id;
        $data['data_id'] = (int) $dataid;
        if (!$id || !$data['data_id']) {
            return false;
        }

        return $this->_getDao()->update($id, $data);
    }

    public function deleteImage($id)
    {
        if (!$id) {
            return false;
        }

        return $this->_getDao()->delete($id);
    }

    public function batchDelete($ids)
    {
        if (!is_array($ids)) {
            return false;
        }

        return $this->_getDao()->batchDelete($ids);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignImageDao');
    }
}
