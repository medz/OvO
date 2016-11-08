<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子发布扩展接口定义
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPostDoBase.php 20996 2012-11-23 08:33:21Z jieyin $
 * @package forum
 */

abstract class PwPostDoBase
{
    /**
     * 帖子发布前，数据检查
     *
     * @param  PwPostDm     $postDm
     * @return true|PwError
     */
    public function check($postDm)
    {
        return true;
    }

    /**
     * 帖子发布页，正文上方内容显示(模板扩展) <output内容输出>
     */
    public function createHtmlBeforeContent()
    {
    }

    /**
     * 帖子发布页，正文右侧内容显示(模板扩展) <output内容输出>
     */
    public function createHtmlRightContent()
    {
    }

    /**
     * 数据处理
     *
     * @param  PwPostDm $postDm
     * @return PwPostDm
     */
    public function dataProcessing($postDm)
    {
        return $postDm;
    }

    /**
     * 帖子发布成功后调用
     *
     * @param int $tid
     */
    public function addThread($tid)
    {
    }

    /**
     * 帖子更新成功后调用
     *
     * @param int $tid
     */
    public function updateThread($tid)
    {
    }

    /**
     * 回复发布成功后调用
     *
     * @param int $pid
     * @param int $tid
     */
    public function addPost($pid, $tid)
    {
    }

    /**
     * 回复更新成功后调用
     *
     * @param int $pid
     * @param int $tid
     */
    public function updatePost($pid, $tid)
    {
    }
}
