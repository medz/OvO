<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子附件附件展示 / ubb解析.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwAttachDisplay.php 28804 2013-05-24 08:00:00Z hao.lin $
 */
class PwAttachDisplay
{
    public $attachs;
    public $showlist = [];
    public $user;
    public $imgWidth;
    public $imgHeight;
    public $imgLazy = false;

    protected $isAdmin;
    protected $downloadUrl = 'bbs/attach/download?';
    protected $recordUrl = 'bbs/attach/record?';

    public function __construct($tid, $pids, PwUserBo $user, $isLazy = false)
    {
        $this->user = $user;
        $this->attachs = $this->_getData($tid, $pids);
        $this->isAdmin = $user->getPermission('operate_thread.deleteatt');
        $this->imgWidth = Wekit::C('bbs', 'ubb.img.width');
        $this->imgHeight = Wekit::C('bbs', 'ubb.img.height');
        $this->imgLazy = $isLazy;
        $this->_init();
    }

    public function has($id)
    {
        return isset($this->attachs[$id]);
    }

    public function getList($pid)
    {
        return $this->showlist[$pid];
    }

    public function deleteList($pid)
    {
        unset($this->showlist[$pid]);
    }

    public function getHtml($pid, $aid)
    {
        if (! isset($this->attachs[$pid]) || ! isset($this->attachs[$pid][$aid])) {
            return '';
        }
        $att = $this->attachs[$pid][$aid];
        $atype = $att['atype'];
        unset($this->showlist[$pid][$atype][$aid]);

        return $this->bulidHtml($atype, $att);
    }

    public function bulidHtml($atype, $att)
    {
        $html = '';
        switch ($atype) {
            case 'pic':
                $html = $this->parsePicHtml($att);
                break;
            case 'downattach':
                $html = $this->parseAttachHtml($att);
                break;
            case 'picurl':
                $html = '';
                break;
        }

        return "<span id=\"J_att_{$att['aid']}\">".$html.'</span>';
    }

    public function parsePicHtml($att)
    {
        $att['name'] = WindSecurity::escapeHTML($att['name']);
        $html = "<span id=\"td_att_{$att['aid']}\" class=\"J_attach_img_wrap single_img\">";
        $html .= '<div class="img_info J_img_info">';
        if ($att['descrip']) {
            $html .= '<p>描述：'.Pw::stripWindCode($att['descrip'], true).'</p>';
        }
        $html .= '<p>图片：'.$att['name'];
        if ($this->isAdmin) {
            $html .= '<a class="J_read_img_del w" data-pdata="{\'aid\':\''.$att['aid'].'\'}" href="'.WindUrlHelper::createUrl('bbs/attach/delete').'">[删除]</a>';
        }
        $html .= '</p></div>';
        $html .= $att['img'];
        $html .= '</span>';

        return $html;
    }

    public function parseAttachHtml($att)
    {
        $lang = [0 => '', 1 => ''];
        $att['descrip'] = WindSecurity::escapeHTML($att['descrip']);
        $att['name'] = WindSecurity::escapeHTML($att['name']);
        $attachBuyClass = 'J_attach_post_buy';
        if ($att['cost'] > 0) {
            $creditBo = PwCreditBo::getInstance();
            $lang[0] = $att['special'] == 1 ? '加密' : '出售';
            $lang[1] = '('.$lang[0].'<span class="org">'.$att['cost'].'</span>&nbsp;'.$creditBo->cType[$att['ctype']].', <span class="org">'.$att['size'].'</span>KB, 已下载<span class="org J_attach_count_'.$att['aid'].'">'.$att['hits'].'</span>次)&nbsp;';
            $lang[2] = $att['special'] == 2 ? ' J_qlogin_trigger' : '';

            if ($att['special'] == 1) {
                $lang[1] .= '<span class="tips_icon_'.($this->user->getCredit($att['ctype']) > $att['cost'] ? 'success' : 'error').'"><span>&nbsp;</span>';
            }
            $attachBuyClass = $this->user->isExists() ? $attachBuyClass : 'J_qlogin_trigger';
        }
        $html = '<span class="file_insert"><a href="'.WindUrlHelper::createUrl($this->downloadUrl.'aid='.$att['aid']).'" class="mr5 J_post_attachs'.$lang[2].'" data-id="'.$att['aid'].'" data-price="'.$att['cost'].'" data-type="'.$att['special'].'" data-typelang="'.$lang[0].'" data-util="'.$creditBo->cType[$att['ctype']].'" data-credit="'.$this->user->getCredit($att['ctype']).'" data-role="attach" data-size="'.$att['size'].'" data-hits="'.$att['hits'].'" data-descrip="'.$att['descrip'].'"><span class="file_list_wrap"><span class="file_icon_'.$att['ext'].'"></span></span>'.$att['name'].'</a><div class="img_info" id="J_attach_post_info_'.$att['aid'].'" style="display: none;"><p>类型：'.$lang[0].'</p><p>售价：'.$att['cost'].''.$creditBo->cType[$att['ctype']].'</p><p>大小：'.$att['size'].'KB</p><p>下载：<span class="J_attach_count_'.$att['aid'].'">'.$att['hits'].'</span>次</p><p>描述：'.$att['descrip'].'</p><p><a href="'.WindUrlHelper::createUrl($this->downloadUrl.'aid='.$att['aid']).'" data-insertid="'.$att['aid'].'" class="'.$attachBuyClass.' mr10" data-countrel=".J_attach_count_'.$att['aid'].'">[下载]</a>';
        $att['cost'] && $html .= '<a href="'.WindUrlHelper::createUrl($this->recordUrl.'aid='.$att['aid']).'" class="J_buy_record mr10" data-aid="'.$att['aid'].'">[记录]</a>';
        if ($this->isAdmin) {
            $html .= '<a class="J_attach_post_del" data-pdata="{\'aid\':\''.$att['aid'].'\'}" data-rel="#J_att_'.$att['aid'].'" href="'.WindUrlHelper::createUrl('bbs/attach/delete').'">[删除]</a>';
        }
        $html .= '</p></div>'.$lang[1].'</span>';

        return $html;
    }

    public function analyse($attach)
    {
        if ($attach['type'] == 'img' && $attach['cost'] == 0) {
            $atype = 'pic';
            $url = Pw::getPath($attach['path']);
            $img = PwUbbCode::createImg(Pw::getPath($attach['path'], $attach['ifthumb'] & 1), $this->imgWidth, $this->imgHeight, $url, $this->imgLazy);

            $attr = '';
            if ($this->imgLazy && ($tmp = $this->_compare($attach['width'], $attach['height'], $this->imgWidth, $this->imgHeight))) {
                $attr .= ' width="'.$tmp[0].'"';
                $attr .= ' height="'.$tmp[1].'"';
            }
            $attach['descrip'] && $attr .= ' alt="'.WindSecurity::escapeHTML($attach['descrip']).'"';
            $attr && $img = substr($img, 0, -3).$attr.' />';

            $attach += [
                'url'     => $url,
                'img'     => $img,
                'miniUrl' => Pw::getPath($attach['path'], $attach['ifthumb']),
            ];
        } else {
            $atype = 'downattach';
            $attach += [
                'cname' => '',
                'ext'   => strtolower(substr(strrchr($attach['name'], '.'), 1)),
            ];
        }

        return [$atype, $attach];
    }

    protected function _getData($tid, $pids)
    {
        $tmp = [];
        $attachs = $this->_getAttachService()->getAttachByTid($tid, $pids);
        foreach ($attachs as $key => $value) {
            $tmp[$value['pid']][$key] = $value;
        }
        foreach ($tmp as $key => $value) {
            ksort($tmp[$key]);
        }

        return $tmp;
    }

    protected function _init()
    {
        foreach ($this->attachs as $pid => $values) {
            foreach ($values as $key => $value) {
                list($atype, $value) = $this->analyse($value);
                $this->showlist[$pid][$atype][$key] = $value;
                $this->attachs[$pid][$key] = $value;
                $this->attachs[$pid][$key]['atype'] = $atype;
            }
        }
    }

    protected function _compare($width, $height, $maxW, $maxH)
    {
        if (! $width || ! $height) {
            return [];
        }
        if ($maxW > 0 && $width > $maxW) {
            $height = round($height * $maxW / $width);
            $width = $maxW;
        }
        if ($maxH > 0 && $height > $maxH) {
            $width = round($width * $maxH / $height);
            $height = $maxH;
        }

        return [$width, $height];
    }

    protected function _getAttachService()
    {
        return Wekit::load('attach.PwThreadAttach');
    }
}
