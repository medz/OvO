<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户投票基础服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com> 2012-01-21
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwPollVoterService.php 2552 2012-01-12 11:28:21Z mingxing.sun $
 * @package poll
 * @subpackage service.srv
 */

class PwPollVoterService
{
    /**
   * 获取我关注的人参与的投票批量ID
   *
   * @param int $uids 多个用户ID
   * @param int $limit
   * @param int $offset
   * @return array
   */
  public function fetchVoteByUid($uids, $limit, $offset)
  {
      if (!$uids || !is_array($uids)) {
          return array();
      }
      $pollids = array();
      $voterInfos = $this->_getPwPollVoterDs()->fetchVoteByUid($uids, $limit, $offset);
      foreach ($voterInfos as $value) {
          $pollids[] = $value['poll_id'];
      }

      return $pollids;
  }

  /**
   * get PwPollVoter
   *
   * @return PwPollVoter
   */
  protected function _getPollVoterDs()
  {
      return Wekit::load('poll.PwPollVoter');
  }
}
