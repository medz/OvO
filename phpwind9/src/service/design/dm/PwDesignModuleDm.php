<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignModuleDm.php 22339 2012-12-21 09:37:22Z gao.wanggao $
 */
class PwDesignModuleDm extends PwBaseDm
{
    public $moduleid;

    public function __construct($moduleid = null)
    {
        if (isset($moduleid)) {
            $this->moduleid = (int) $moduleid;
        }
    }

    public function setPageId($pageid)
    {
        $this->_data['page_id'] = (int) $pageid;

        return $this;
    }

    public function setSegment($segment)
    {
        $this->_data['segment'] = $segment;

        return $this;
    }

    public function setStruct($truct)
    {
        $this->_data['module_struct'] = $truct;

        return $this;
    }

    public function setFlag($flag)
    {
        $this->_data['model_flag'] = $flag;

        return $this;
    }

    public function setName($name)
    {
        $this->_data['module_name'] = Pw::substrs($name, 20);

        return $this;
    }

    public function setProperty($array)
    {
        $array['titlenum'] = (int) $array['titlenum'];
        $array['desnum'] = (int) $array['desnum'];
        $array['limit'] = (int) $array['limit'];
        $this->_data['module_property'] = serialize($array);

        return $this;
    }

    public function setCompid($compid)
    {
        $this->_data['module_compid'] = intval($compid);

        return $this;
    }

    public function setTitle($array)
    {
        $this->_data['module_title'] = serialize($array);

        return $this;
    }

    public function setStyle($font, $link, $border, $margin, $padding, $background, $styleclass)
    {
        $font['color'] = $this->_verifyColor($font['color']);
        $link['color'] = $this->_verifyColor($link['color']);
        $border['color'] = $this->_verifyColor($border['color']);
        $border['top']['color'] = $this->_verifyColor($border['top']['color']);
        $border['left']['color'] = $this->_verifyColor($border['left']['color']);
        $border['right']['color'] = $this->_verifyColor($border['right']['color']);
        $border['bottom']['color'] = $this->_verifyColor($border['bottom']['color']);
        $background['color'] = $this->_verifyColor($background['color']);
        $array = ['top', 'right', 'bottom', 'left'];
        if ($border['linewidth']) {
            $border['top']['linewidth'] = (int) $border['linewidth'];
            $border['right']['linewidth'] = (int) $border['linewidth'];
            $border['bottom']['linewidth'] = (int) $border['linewidth'];
            $border['left']['linewidth'] = (int) $border['linewidth'];
            unset($border['linewidth']);
            $border['isdiffer'] = 1;
        }
        foreach ($array as $v) {
            if (isset($border[$v]['linewidth']) && $border[$v]['linewidth'] !== '') {
                $border[$v]['linewidth'] = (int) $border[$v]['linewidth'];
            }
        }

        if ($border['style']) {
            $border['top']['style'] = $border['style'];
            $border['right']['style'] = $border['style'];
            $border['bottom']['style'] = $border['style'];
            $border['left']['style'] = $border['style'];
            unset($border['style']);
        }

        if ($border['color']) {
            $border['top']['color'] = $border['color'];
            $border['right']['color'] = $border['color'];
            $border['bottom']['color'] = $border['color'];
            $border['left']['color'] = $border['color'];
            unset($border['color']);
        }

        if ($margin['both']) {
            $margin['top'] = (int) $margin['both'];
            $margin['right'] = (int) $margin['both'];
            $margin['bottom'] = (int) $margin['both'];
            $margin['left'] = (int) $margin['both'];
            unset($margin['both']);
            $margin['isdiffer'] = 1;
        }

        foreach ($array as $v) {
            if (isset($margin[$v]) && $margin[$v] !== '') {
                $margin[$v] = (int) $margin[$v];
            }
        }

        if ($padding['both']) {
            $padding['top'] = (int) $padding['both'];
            $padding['right'] = (int) $padding['both'];
            $padding['bottom'] = (int) $padding['both'];
            $padding['left'] = (int) $padding['both'];
            unset($padding['both']);
            $padding['isdiffer'] = 1;
        }

        foreach ($array as $v) {
            if (isset($padding[$v]) && $padding[$v] !== '') {
                $padding[$v] = (int) $padding[$v];
            }
        }

        $style = [
            'font'       => $font,
            'link'       => $link,
            'border'     => $border,
            'margin'     => $margin,
            'padding'    => $padding,
            'background' => $background,
            'styleclass' => $styleclass,
        ];

        $this->_data['module_style'] = serialize($style);

        return $this;
    }

    public function getStyle()
    {
        return unserialize($this->_data['module_style']);
    }

    public function setModuleTpl($tpl)
    {
        $this->_data['module_tpl'] = $tpl;

        return $this;
    }

    public function setCache($array)
    {
        $array['expired'] = (int) $array['expired'];
        $array['start_hour'] = (int) $array['start_hour'];
        $array['start_minute'] = (int) $array['start_minute'];
        $array['end_hour'] = (int) $array['end_hour'];
        $array['end_minute'] = (int) $array['end_minute'];
        if ($array['start_hour'] > 23 || $array['start_hour'] < 0) {
            $array['start_hour'] = 0;
        }
        if ($array['end_hour'] > 23 || $array['end_hour'] < 0) {
            $array['end_hour'] = 23;
        }
        if ($array['start_minute'] > 59 || $array['start_minute'] < 0) {
            $array['start_minute'] = 0;
        }
        if ($array['end_minute'] > 59 || $array['end_minute'] < 0) {
            $array['end_minute'] = 59;
        }
        $this->_data['module_cache'] = serialize($array);

        return $this;
    }

    public function setIsused($isused)
    {
        $this->_data['isused'] = (int) $isused;

        return $this;
    }

    public function setModuleType($type)
    {
        $this->_data['module_type'] = (int) $type;

        return $this;
    }

    protected function _beforeAdd()
    {
        return true;
    }

    protected function _beforeUpdate()
    {
        if ($this->moduleid < 1) {
            return new PwError('operate.fail');
        }

        return true;
    }

    private function _verifyColor($v = null)
    {
        if (! $v) {
            return $v;
        }

        return (preg_match('/^#[a-z0-9]+$/', $v)) ? $v : '';
    }
}
