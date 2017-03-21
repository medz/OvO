<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>.
 *
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: WindidMessageApi.php 24706 2013-02-16 06:02:32Z jieyin $
 */
class WindidMessageApi
{
    /**
     * 获取私信
     *
     * @param int $messageId 私信ID
     */
    public function getMessageById($messageId)
    {
        $params = [
            'messageId' => $messageId,
        ];

        return WindidApi::open('message/getMessageById', $params);
    }

    /**
     * 获取用户未读消息数.
     *
     * @param int $uid
     *
     * @return int
     */
    public function getUnRead($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        return WindidApi::open('message/getNum', $params);
    }

    /**
     * 统计一个会话的消息数.
     *
     * @param int $dialogId
     *
     * @return int
     */
    public function countMessage($dialogId)
    {
        $params = [
            'dialogId' => $dialogId,
        ];

        return WindidApi::open('message/countMessage', $params);
    }

    /**
     * 获取消息列表.
     *
     * @param int $dialogId
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getMessageList($dialogId, $start = 0, $limit = 10)
    {
        $params = [
            'dialogId' => $dialogId,
            'start'    => $start,
            'limit'    => $limit,
        ];

        return WindidApi::open('message/getMessageList', $params);
    }

    /**
     * 获取一条对话信息.
     *
     * @param int $dialogId
     */
    public function getDialog($dialogId)
    {
        $params = [
            'dialogId' => $dialogId,
        ];

        return WindidApi::open('message/getDialog', $params);
    }

    /**
     * 按会话ids获取对话消息列表.
     *
     * @param array $dialogIds
     *
     * @return array
     */
    public function fetchDialog($dialogIds)
    {
        $params = [
            'dialogIds' => $dialogIds,
        ];

        return WindidApi::open('message/fetchDialog', $params);
    }

    /**
     * 获取消息分组信息.
     *
     * @param int $toUid
     * @param int $fromUid
     */
    public function getDialogByUser($uid, $dialogUid)
    {
        $params = [
            'uid'       => $uid,
            'dialogUid' => $dialogUid,
        ];

        return WindidApi::open('message/getDialogByUser', $params);
    }

    /**
     * 获取多组消息分组信息.
     *
     * @param int $uid
     * @param int $from_uids
     */
    public function getDialogByUsers($uid, $dialogUids)
    {
        $params = [
            'uid'        => $uid,
            'dialogUids' => $dialogUids,
        ];

        return WindidApi::open('message/getDialogByUsers', $params);
    }

    /**
     * 获取对话消息列表.
     *
     * @param int $uid
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getDialogList($uid, $start = 0, $limit = 10)
    {
        $params = [
            'uid'   => $uid,
            'start' => $start,
            'limit' => $limit,
        ];

        return WindidApi::open('message/getDialogList', $params);
    }

    /**
     * 统计分组消息列表数量.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countDialog($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        return WindidApi::open('message/countDialog', $params);
    }

    /**
     * 获取多条未读对话.
     *
     * @param int $uid
     * @param int $limit
     *
     * @return array
     */
    public function getUnreadDialogsByUid($uid, $limit = 10)
    {
        $params = [
            'uid'   => $uid,
            'limit' => $limit,
        ];

        return WindidApi::open('message/getUnreadDialogsByUid', $params);
    }

    /**
     * 搜索消息.
     *
     * @param array $search array('fromuid', 'keyword', 'username', 'starttime', 'endtime')
     * @param int   $start
     * @param int   $limit
     *
     * @return array(count, list)
     */
    public function searchMessage($search, $start = 0, $limit = 10)
    {
        if (! is_array($search)) {
            return [0, []];
        }
        $params = [
            'start' => $start,
            'limit' => $limit,
        ];
        $array = ['fromuid', 'keyword', 'username', 'starttime', 'endtime'];
        foreach ($search as $k => $v) {
            if (! in_array($k, $array)) {
                continue;
            }
            $params[$k] = $v;
        }

        return WindidApi::open('message/searchMessage', $params);
    }

    /**
     * 更新消息数.
     *
     * @param int $uid
     * @param int $num
     */
    public function editMessageNum($uid, $num)
    {
        $params = [
            'uid' => $uid,
            'num' => $num,
        ];

        return WindidApi::open('message/editNum', [], $params);
    }

    /**
     * 发送消息.
     *
     * @param array  $uids    收件人uids
     * @param string $content 消息内容
     * @param int    $fromUid 发件人
     *
     * @return int
     */
    public function send($uids, $content, $fromUid = 0)
    {
        $params = [
            'uids'    => $uids,
            'content' => $content,
            'fromUid' => $fromUid,
        ];

        return WindidApi::open('message/send', [], $params);
    }

    /**
     * 标记已读.
     *
     * @param int   $uid
     * @param int   $dialogId
     * @param array $messageIds
     *
     * @return int 标记成功的条数
     */
    public function read($uid, $dialogId, $messageIds = [])
    {
        $params = [
            'uid'        => $uid,
            'dialogId'   => $dialogId,
            'messageIds' => $messageIds,
        ];

        return WindidApi::open('message/read', [], $params);
    }

    public function readDialog($dialogIds)
    {
        $params = [
            'dialogIds' => $dialogIds,
        ];

        return WindidApi::open('message/readDialog', [], $params);
    }

    public function delete($uid, $dialogId, $messageIds = [])
    {
        $params = [
            'uid'        => $uid,
            'dialogId'   => $dialogId,
            'messageIds' => $messageIds,
        ];

        return WindidApi::open('message/delete', [], $params);
    }

    public function batchDeleteDialog($uid, $dialogIds)
    {
        $params = [
            'uid'       => $uid,
            'dialogIds' => $dialogIds,
        ];

        return WindidApi::open('message/batchDeleteDialog', [], $params);
    }

    public function deleteByMessageIds($messageIds)
    {
        $params = [
            'messageIds' => $messageIds,
        ];

        return WindidApi::open('message/deleteByMessageIds', [], $params);
    }

    public function deleteUserMessages($uid)
    {
        $params = [
            'uid' => $uid,
        ];

        return WindidApi::open('message/deleteUserMessages', [], $params);
    }

    /********************** 传统收件箱，发件箱接口start *********************/

    /**
     * 发件箱.
     *
     * @return array
     */
    public function fromBox($fromUid, $start = 0, $limit = 10)
    {
        $params = [
            'uid'   => $fromUid,
            'start' => $start,
            'limit' => $limit,
        ];

        return WindidApi::open('message/fromBox', $params);
    }

    /**
     * 收件箱.
     *
     * @return array
     */
    public function toBox($toUid, $start = 0, $limit = 10)
    {
        $params = [
            'uid'   => $uid,
            'start' => $start,
            'limit' => $limit,
        ];

        return WindidApi::open('message/toBox', $params);
    }

    public function readMessages($uid, $messageIds)
    {
        $params = [
            'uid'        => $uid,
            'messageIds' => $messageIds,
        ];

        return WindidApi::open('message/readMessages', [], $params);
    }

    public function deleteMessages($uid, $messageIds)
    {
        $params = [
            'uid'        => $uid,
            'messageIds' => $messageIds,
        ];

        return WindidApi::open('message/deleteMessages', [], $params);
    }

    /**********************传统收件箱，发件箱接口end *********************/
}
