<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignDataDm.php 18755 2012-09-27 05:37:22Z gao.wanggao $
 */
class PwDesignDataDm extends PwBaseDm
{
    public $dataid;

    public function __construct($dataid = null)
    {
        if (isset($dataid)) {
            $this->dataid = (int) $dataid;
        }
    }

    public function setFromType($type)
    {
        $this->_data['from_type'] = (int) $type;

        return $this;
    }

    public function setFromApp($app)
    {
        $this->_data['from_app'] = Pw::substrs($app, 10);

        return $this;
    }

    public function setFromid($id)
    {
        $this->_data['from_id'] = (int) $id;

        return $this;
    }

    public function setModuleid($id)
    {
        $this->_data['module_id'] = (int) $id;

        return $this;
    }

    public function setStandard($array)
    {
        $this->_data['standard'] = serialize($array);

        return $this;
    }

    public function setDatatype($type)
    {
        if (!$type) {
            $type = 1;
        }
        $this->_data['data_type'] = (int) $type;

        return $this;
    }

    public function setReservation($reservation)
    {
        $this->_data['is_reservation'] = (int) $reservation;

        return $this;
    }

    public function setEdited($isedit = 0)
    {
        $this->_data['is_edited'] = (int) $isedit;

        return $this;
    }

    public function setExtend($array = null)
    {
        $array && $this->_data['extend_info'] = serialize($array);

        return $this;
    }

    public function setStyle($bold, $underline, $italic, $color)
    {
        $this->_data['style'] = $bold.'|'.$underline.'|'.$italic.'|'.$color;

        return $this;
    }

    public function setVieworder($orderid)
    {
        $this->_data['vieworder'] = intval($orderid);

        return $this;
    }

    public function setStarttime($time)
    {
        $this->_data['start_time'] = intval($time);

        return $this;
    }

    public function setEndtime($time)
    {
        $this->_data['end_time'] = intval($time);

        return $this;
    }

    protected function _beforeAdd()
    {
        if (!$this->_data['standard']) {
            return new PwError('operate.fail');
        }

        return true;
    }

    protected function _beforeUpdate()
    {
        if ($this->dataid < 1) {
            return new PwError('operate.fail');
        }

        return true;
    }
}
