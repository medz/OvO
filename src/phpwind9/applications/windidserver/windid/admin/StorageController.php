<?php

Wind::import('APPS:windid.admin.WindidBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: StorageController.php 24648 2013-02-04 02:31:11Z jieyin $
 */
class StorageController extends WindidBaseController
{
    /**
     * 附件存储方式设置列表页.
     */
    public function run()
    {
        $attService = Wekit::load('LIB:storage.PwStorage');
        $storages = $attService->getStorages();
        $config = Wekit::C()->getValues('attachment');
        $storageType = 'local';
        if (isset($config['storage.type']) && isset($storages[$config['storage.type']])) {
            $storageType = $config['storage.type'];
        }
        $c = Wekit::C()->getValues('site');
        $config['avatarUrl'] = $c['avatarUrl'];

        $this->setOutput($config, 'config');
        $this->setOutput($storages, 'storages');
        $this->setOutput($storageType, 'storageType');
    }

    /**
     * 附件存储方式设置列表页.
     */
    public function dostroageAction()
    {
        $att_storage = $this->getInput('att_storage', 'post');
        $avatarurl = $this->getInput('avatarurl', 'post');

        $attService = Wekit::load('LIB:storage.PwStorage');
        $_r = $attService->setStoragesComponents($att_storage);
        if ($_r !== true) {
            $this->showError($_r->getError());
        }
        $config = new PwConfigSet('attachment');
        $config->set('storage.type', $att_storage)->flush();

        $components = Wekit::C()->get('components')->toArray();
        Wind::getApp()->getFactory()->loadClassDefinitions($components);
        Wekit::C()->setConfig('site', 'avatarUrl', substr(Pw::getPath('1.gpg'), 0, -6));

        Wekit::load('WSRV:notify.srv.WindidNotifyService')->send('alterAvatarUrl', array());

        $this->showMessage('WINDID:success');
    }
}
