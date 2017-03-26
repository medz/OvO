<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMedalDm.php 8501 2012-04-19 09:32:42Z gao.wanggao $
 */
class PwMedalDm extends PwBaseDm
{
    public $medalId;

    public function __construct($medalId = null)
    {
        if (isset($medalId)) {
            $this->medalId = (int) $medalId;
        }
    }

    public function setMedalName($name)
    {
        $this->_data['name'] = $name;

        return $this;
    }

    public function setPath($path)
    {
        $this->_data['path'] = $path;

        return $this;
    }

    public function setImage($image)
    {
        $this->_data['image'] = $image;

        return $this;
    }

    public function setIcon($icon)
    {
        $this->_data['icon'] = $icon;

        return $this;
    }

    public function setDescrip($descrip)
    {
        $this->_data['descrip'] = $descrip;

        return $this;
    }

    public function setMedalType($type)
    {
        $this->_data['medal_type'] = $type;

        return $this;
    }

    public function setMedalGids($gids)
    {
        $gids = is_array($gids) ? $gids : [];
        $this->_data['medal_gids'] = implode(',', $gids);

        return $this;
    }

    public function setAwardType($type)
    {
        $this->_data['award_type'] = $type;

        return $this;
    }

    public function setAwardCondition($condition)
    {
        $this->_data['award_condition'] = (int) $condition;

        return $this;
    }

    public function setExpiredDays($days)
    {
        $this->_data['expired_days'] = (int) $days;

        return $this;
    }

    public function setIsopen($open)
    {
        $this->_data['isopen'] = (int) $open;

        return $this;
    }

    public function setVieworder($vieworder)
    {
        $this->_data['vieworder'] = (int) $vieworder;

        return $this;
    }

    public function setReceiveType($receive)
    {
        $this->_data['receive_type'] = (int) $receive;

        return $this;
    }

    protected function _beforeAdd()
    {
        if (empty($this->_data['name'])) {
            return new PwError('MEDAL:name.empty');
        }
        if (empty($this->_data['descrip'])) {
            return new PwError('MEDAL:descrip.empty');
        }
        if ($this->_data['receive_type'] == 1 && $this->_data['award_condition'] < 1) {
            return new PwError('MEDAL:award.condition.empty');
        }

        return true;
    }

    protected function _beforeUpdate()
    {
        if (empty($this->medalId)) {
            return new PwError('MEDAL:fail');
        }
        if (empty($this->_data['descrip'])) {
            return new PwError('MEDAL:descrip.empty');
        }
        if (empty($this->_data['name'])) {
            return new PwError('MEDAL:name.empty');
        }
        if ($this->_data['receive_type'] == 1 && $this->_data['award_condition'] < 1) {
            return new PwError('MEDAL:award.condition.empty');
        }

        return true;
    }
}
