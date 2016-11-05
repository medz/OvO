<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignStyle.php 22041 2012-12-18 10:31:33Z gao.wanggao $
 * @package
 */
class PwDesignStyle
{
    private $_domId = '';
    private $_style = array();

    /**
     * 设置HTML DOM ID
     *
     * @param string $domId
     */
    public function setDom($domId)
    {
        $this->_domId = $domId;
    }

    /**
     * 根据样式数组组装style
     *
     * @param array $style
     */
    public function setStyle($style)
    {
        $this->_style = array();
        foreach ((array) $style as $k => $v) {
            switch ($k) {
                case 'font':
                    $this->_setFont((int) $v['size'], $v['color'], $v['bold'], $v['underline'], $v['italic']);
                    break;
                case 'border':
                    if (!$v['isdiffer']) {
                        $this->_setBorder($v['linewidth'], $v['style'], $v['color']);
                    } else {
                        foreach ($v as $_k => $_v) {
                            if (!in_array($_k, array('top', 'left', 'right', 'bottom'))) {
                                continue;
                            }
                            $this->_setBorder($_v['linewidth'], $_v['style'], $_v['color'], $_k);
                        }
                    }
                    break;
                case 'margin':
                    if (!$v['isdiffer']) {
                        $this->_setMargin((int) $v['both'], (int) $v['both'], (int) $v['both'], (int) $v['both']);
                    } else {
                        $this->_setMargin((int) $v['top'], (int) $v['right'], (int) $v['bottom'], (int) $v['left']);
                    }
                    break;
                case 'padding':
                    if (!$v['isdiffer']) {
                        $this->_setPadding((int) $v['both'], (int) $v['both'], (int) $v['both'], (int) $v['both']);
                    } else {
                        $this->_setPadding((int) $v['top'], (int) $v['right'], (int) $v['bottom'], (int) $v['left']);
                    }
                    break;
                case 'background':
                    $this->_setBackground($v['color'], $v['image'], $v['position']);
                    break;
                case 'float':
                    $this->_setFloat($v['type'], (int) $v['margin']);
                    break;
            }
        }
    }

    /**
     * 获取一个DOM的CSS样式
     *
     * @return array
     */
    public function getCss()
    {
        return array($this->_domId, implode('', $this->_style));
    }

    /**
     * 获取一个DOM 的链接样式
     *
     * @param array $style
     */
    public function getLink($style = array())
    {
        $this->_style = array();
        if ($style['link']) {
            $this->_setFont($style['link']['size'], $style['link']['color']);
        }

        return array($this->_domId.' A', implode('', $this->_style));
    }

    /**
     * 根据样式数组格式化一个标题的样式
     *
     * @param  array $style
     * @return array
     */
    public function buildTitleStyle($style)
    {
        return array(
                'float' => array('type' => $style['float'], 'margin' => $style['margin']),
                'font' => array('size' => $style['fontsize'], 'color' => $style['fontcolor'], 'bold' => $style['fontbold'], 'underline' => $style['fontunderline'], 'italic' => $style['fontitalic']),
                'background' => array('color' => $style['bgcolor'], 'image' => $style['bgimage'], 'position' => $style['bgposition']),
        );
    }

    private function _setFont($size = 0, $color = '', $bold = 0, $underline = 0, $italic = 0)
    {
        $style = '';
        if ($size) {
            $style .= 'font-size: '.$size.'px;';
        }
        if ($color) {
            $style .= 'color: '.$color.';';
        }
        if ($bold) {
            $style .= 'font-weight:bold;';
        }
        if ($underline) {
            $style .= 'text-decoration:underline;';
        }
        if ($italic) {
            $style .= 'font-style:italic;';
        }
        $this->_appendStyle($style);
    }

    private function _setBorder($width = null, $line = null, $color = null, $tblr = null)
    {
        $style = '';
        if ($tblr) {
            $tblr .= '-';
        }
        if (isset($width) && $width != '') {
            $style .= 'border-'.$tblr.'width: '.$width.'px;';
        }
        if ($line) {
            $style .= 'border-'.$tblr.'style: '.$line.';';
        }
        if ($color) {
            $style .= 'border-'.$tblr.'color: '.$color.';';
        }
        $this->_appendStyle($style);
    }

    private function _setMargin($top = 0, $right = 0, $bottom = 0, $left = 0)
    {
        $style = '';
        if ($top || $right || $bottom || $left) {
            $style .= 'margin: '.$top.'px '.$right.'px '.$bottom.'px '.$left.'px;';
        }
        $this->_appendStyle($style);
    }

    private function _setPadding($top = 0, $right = 0, $bottom = 0, $left = 0)
    {
        $style = '';
        if ($top || $right || $bottom || $left) {
            $style .= 'padding: '.$top.'px '.$right.'px '.$bottom.'px '.$left.'px;';
        }
        $this->_appendStyle($style);
    }

    private function _setBackground($color = null, $backimage = null, $position = null)
    {
        $style = '';

        if ($backimage) {
            $style .= 'background: url(\''.$backimage.'\');';
        } /*elseif (isset($backimage)) {
            $style .= 'background-image:none;';
        }*/
        if ($color) {
            $style .= 'background-color: '.$color.';';
        }
        if (in_array($position, array('left', 'right', 'center'))) {
            $style .= 'background-position: '.$position.' top;background-repeat:no-repeat;';
        }
        if ($position == 'repeat') {
            $style .= 'background-repeat:repeat-x;background-position: left top;';
        }
        $this->_appendStyle($style);
    }

    private function _setFloat($type = 'left', $pixels = 0)
    {
        $style = '';
        $pixels = (int) $pixels;
        if ($type == 'left') {
            $style .= 'float:left;margin-left:'.$pixels.'px;';
        }
        if ($type == 'right') {
            $style .= 'float:right;margin-right:'.$pixels.'px;';
        }
        $this->_appendStyle($style);
    }

    private function _appendStyle($style)
    {
        if ($style) {
            $this->_style[] = $style;
        }
    }
}
