<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:ubb.PwSimpleUbbCode');
Wind::import('LIB:ubb.config.PwUbbCodeConvertThread');

/**
 * 帖子公共服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwThreadService.php 22524 2012-12-25 07:09:15Z jinlong.panjl $
 * @package forum
 */

class PwThreadService
{
    public function displayReplylist($replies, $contentLength = 140)
    {
        $users = Wekit::load('user.PwUser')->fetchUserByUid(array_unique(Pw::collectByKey($replies, 'created_userid')));
        foreach ($replies as $key => $value) {
            $value['content'] = WindSecurity::escapeHTML($value['content']);
            if (!empty($value['ifshield'])) {
                $value['content'] = '<div class="shield">此帖已被屏蔽</div>';
            } elseif ($users[$value['created_userid']]['groupid'] == '6') {
                $value['content'] = '用户被禁言,该主题自动屏蔽!';
            } elseif ($value['useubb']) {
                $ubb = new PwUbbCodeConvertThread();
                $value['reminds'] && $ubb->setRemindUser($value['reminds']);
                $value['content'] = PwSimpleUbbCode::convert($value['content'], $contentLength, $ubb);
            } else {
                $value['content'] = Pw::substrs($value['content'], $contentLength);
            }
            !$value['word_version'] && $value['content'] = Wekit::load('SRV:word.srv.PwWordFilter')->replaceWord($value['content'], $value['word_version']);
            $replies[$key] = $value;
        }

        return $replies;
    }

    public function displayContent($content, $useubb, $remindUser = array(), $contentLength = 140)
    {
        $content = WindSecurity::escapeHTML($content);
        if ($useubb) {
            $ubb = new PwUbbCodeConvertThread();
            $ubb->setRemindUser($remindUser);
            $content = PwSimpleUbbCode::convert($content, $contentLength, $ubb);
        } else {
            $content = Pw::substrs($content, $contentLength);
        }

        return $content;
    }
}
