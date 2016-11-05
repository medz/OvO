<?php
Wind::import('APPS:space.controller.SpaceBaseController');
/**
 * the last known user to change this file in the repository <$LastChangedBy$>
 * 
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package
 *
 */
class ProfileController extends SpaceBaseController {

	/**
	 * 用户资料
	 */
	public function run() {
		$lang = Wind::getComponent('i18n');
		$gid = ($this->space->spaceUser['groupid'] == 0) ? $this->space->spaceUser['memberid'] : $this->space->spaceUser['groupid'];
		$group = Wekit::load('usergroup.PwUserGroups')->getGroupByGid($gid);
		$constellation = Wekit::load('space.srv.PwSpaceService')->getConstellation(
			$this->space->spaceUser['byear'], $this->space->spaceUser['bmonth'], 
			$this->space->spaceUser['bday']);
		$this->setOutput($group['name'], 'groupName');
		$this->setOutput($lang->getMessage('USER:profile.constellation.' . $constellation), 'constellation');
		$this->setOutput('profile', 'src');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo(
			$lang->getMessage('SEO:space.profile.run.title', 
				array($this->space->spaceUser['username'], $this->space->space['space_name'])), '', 
			$lang->getMessage('SEO:space.profile.run.description', 
				array($this->space->spaceUser['username'])));
		Wekit::setV('seo', $seoBo);
	}
}

?>