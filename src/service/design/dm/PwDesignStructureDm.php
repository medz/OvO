<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignStructureDm.php 22339 2012-12-21 09:37:22Z gao.wanggao $
 * @package
 */
class PwDesignStructureDm extends PwBaseDm
{
    public function setStructName($name)
    {
        $this->_data['struct_name'] = $name;

        return $this;
    }

    public function setSegment($segment)
    {
        $this->_data['segment'] = $segment;

        return $this;
    }

    public function setStructTitle($array)
    {
        $this->_data['struct_title'] = serialize($array);

        return $this;
    }

    public function setStructStyle($font, $link, $border, $margin, $padding, $background, $styleclass)
    {
        $font['color'] = $this->_verifyColor($font['color']);
        $link['color'] = $this->_verifyColor($link['color']);
        $border['color'] = $this->_verifyColor($border['color']);
        $border['top']['color'] = $this->_verifyColor($border['top']['color']);
        $border['left']['color'] = $this->_verifyColor($border['left']['color']);
        $border['right']['color'] = $this->_verifyColor($border['right']['color']);
        $border['bottom']['color'] = $this->_verifyColor($border['bottom']['color']);
        $background['color'] = $this->_verifyColor($background['color']);
        $array = array('top', 'right', 'bottom', 'left');
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

        $style = array(
            'font' => $font,
            'link' => $link,
            'border' => $border,
            'margin' => $margin,
            'padding' => $padding,
            'background' => $background,
            'styleclass' => $styleclass,
        );
        $this->_data['struct_style'] = serialize($style);

        return $this;
    }

    public function getStyle()
    {
        return unserialize($this->_data['struct_style']);
    }

    private function _verifyColor($v)
    {
        return (preg_match('/^#[a-z0-9]+$/', $v)) ? $v : '';
    }

    protected function _beforeAdd()
    {
        if (!$this->_data['struct_name']) {
            return new PwError('fail');
        }

        return true;
    }

    protected function _beforeUpdate()
    {
        if (!$this->_data['struct_name']) {
            return new PwError('fail');
        }

        return true;
    }
}
