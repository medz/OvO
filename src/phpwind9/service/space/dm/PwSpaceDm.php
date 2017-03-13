<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwSpaceDm.php 20893 2012-11-16 07:00:39Z jieyin $
 */
class PwSpaceDm extends PwBaseDm
{
    public $uid;

    public function __construct($uid = null)
    {
        if (isset($uid)) {
            $this->uid = (int) $uid;
        }
    }

    public function setSpaceName($name)
    {
        $this->_data['space_name'] = Pw::substrs($name, 20, 0, false);

        return $this;
    }

    public function setSpaceDescrip($descrip)
    {
        $this->_data['space_descrip'] = Pw::substrs($descrip, 250, 0, false);

        return $this;
    }

    public function setSpaceDomain($domain)
    {
        $this->_data['space_domain'] = Pw::substrs($domain, 20, 0, false);

        return $this;
    }

    public function setSpaceStyle($style)
    {
        $this->_data['space_style'] = $style;

        return $this;
    }

    public function setBackImage($image, $repeat, $fixed, $align)
    {
        //$array = array('image'=>$image, 'repeat'=>$repeat, 'fixed'=>$fixed, 'align'=>$align);
        $image = htmlentities($image);
        $repeat = htmlentities($repeat);
        $align = htmlentities($align);
        $array = [$image, $repeat, $fixed, $align];
        $this->_data['back_image'] = serialize($array);

        return $this;
    }

    public function setVisitCount($number)
    {
        $this->_data['visit_count'] = (int) $number;

        return $this;
    }

    public function setVisitors($visitors)
    {
        $visitors = is_array($visitors) ? $visitors : [];
        $this->_data['visitors'] = serialize($visitors);

        return $this;
    }

    public function setTovisitors($visitors)
    {
        $visitors = is_array($visitors) ? $visitors : [];
        $this->_data['tovisitors'] = serialize($visitors);

        return $this;
    }

    public function setSpacePrivacy($privacy)
    {
        $this->_data['space_privacy'] = intval($privacy);

        return $this;
    }

    protected function _beforeAdd()
    {
        if ($this->uid < 1) {
            return new PwError('SPACE:uid.empty');
        }
        //if (empty($this->_data['space_name'])) return new PwError('SPACE:spacename.empty');
        return true;
    }

    protected function _beforeUpdate()
    {
        if ($this->uid < 1) {
            return new PwError('SPACE:uid.empty');
        }

        return true;
    }
}
