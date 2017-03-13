<?php

defined('WEKIT_VERSION') or exit(403);

return [
    'config'            => ['全局', []],
    'config_site'       => ['站点设置', 'config/config/*', '', '', 'config'],
    'config_register'   => ['注册登录', 'config/regist/*', '', '', 'config'],
    'config_attachment' => ['附件相关', 'config/attachment/*', '', '', 'config'],
    'config_watermark'  => ['水印设置', 'config/watermark/*', '', '', 'config'],
    'config_notice'     => ['消息设置', 'config/notice/*', '', '', 'config'],
    'config_message'    => ['提示信息', 'config/message/*', '', '', 'config'],
    'config_email'      => ['电子邮件', 'config/email/*', '', '', 'config'],
    'config_pay'        => ['在线支付', 'config/pay/*', '', '', 'config'],
    'config_webdata'    => ['资料库', [], '', '', 'config'],
    'config_area'       => ['地区库', 'config/areadata/*', '', '', 'config_webdata'],
    'config_school'     => ['学校库', 'config/schooldata/*', '', '', 'config_webdata'],

];
