<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwEmotionCategoryDm extends PwBaseDm
{
    public $categoryId;

    public function __construct($categoryId = null)
    {
        isset($categoryId) && $this->categoryId = (int) $categoryId;
    }

    public function setCategoryMame($categoryname)
    {
        $this->_data['category_name'] = Pw::substrs($categoryname, 4);

        return $this;
    }

    public function setEmotionFolder($emotionFolder)
    {
        $this->_data['emotion_folder'] = $emotionFolder;

        return $this;
    }

    public function setEmotionApps($apps)
    {
        !is_array($apps) && $apps = [];
        $_apps = implode('|', $apps);
        $this->_data['emotion_apps'] = $_apps;

        return $this;
    }

    public function setOrderId($orderid)
    {
        $this->_data['orderid'] = (int) $orderid;

        return $this;
    }

    public function setIsopen($isopen)
    {
        $this->_data['isopen'] = (int) $isopen;

        return $this;
    }

    protected function _beforeAdd()
    {
        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
