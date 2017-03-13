<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 *
 * @link http://www.phpwind.com
 *
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */

return [
    'image' => [
        'name'        => '图片验证码',
        'alias'       => 'image',
        'description' => '图片验证码：需要GD库2.01以上版本',
        'components'  => ['path' => 'WINDID:service.verify.codetype.PwVerifyCode', 'display' => 'image'], ],
    'flash' => [
        'name'        => 'flash验证码',
        'alias'       => 'flash',
        'description' => 'Flash验证码：需要主机支持Ming库',
        'components'  => ['path' => 'LIB:utility.PwVerifyCode', 'display' => 'flash'], ], ];
