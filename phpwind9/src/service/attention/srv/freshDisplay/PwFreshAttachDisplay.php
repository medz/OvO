<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 新鲜事附件展示.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwFreshAttachDisplay.php 20202 2012-10-24 09:10:34Z jieyin $
 */
class PwFreshAttachDisplay
{
    public $pics;
    public $tmp = [];

    public function __construct(&$pic)
    {
        $this->pics = &$pic;
    }

    public function getHtml($pid, $aid)
    {
        if (isset($this->tmp[$aid])) {
            $this->pics[$aid] = $this->tmp[$aid];

            return '';
        }

        return '[附件]';
    }

    public function removeAttach($aids)
    {
        foreach ($aids as $aid) {
            if (!isset($this->pics[$aid])) {
                continue;
            }
            $this->tmp[$aid] = $this->pics[$aid];
            unset($this->pics[$aid]);
        }
    }
}
