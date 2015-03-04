<?php

class PluginShoutbox_ActionShoutbox extends Action {
	protected $oUserCurrent;

	public function Init() {

		$this->oUserCurrent=$this->User_GetUserCurrent();
		$this->SetDefaultEvent('history');
		
	}


	// Sub functions

	public function CheckAccess($mask='admins') {

		if (in_array($this->oUserCurrent->getId(), Config::Get("plugin.shoutbox.$mask"))) {
			return true;
		} else {
			return false;
		}
	}

	public function KeepUserAuthorization() {
		if (!$this->User_IsAuthorization() AND Config::Get('plugin.shoutbox.only_authorized')) {
			return true;
		} else {
			return false;
		}
	}

	public function MuteUser($username) {

		// нет доступа
		if (!($this->oUserCurrent->isAdministrator() OR $this->CheckAccess('super_admins_mute'))) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_access_denied'),$this->Lang_Get('error'));
			return;
		}

		$oUserSB=$this->User_GetUserByLogin($username);
		// пользователь не найден
		if (!($oUserSB)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_notfound'),$this->Lang_Get('error'));
			return;
		}

		// пользователя нельзя забанить
		if (in_array($oUserSB->getId(), Config::Get("plugin.shoutbox.super_users"))) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_wrong_user'),$this->Lang_Get('error'));
			return;
		}

		// пользователя уже забанен
		if ($this->PluginShoutbox_Shout_InBlackList($oUserSB->getLogin())) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_user_already_muted'),$this->Lang_Get('error'));
			return;
		}

		return true;

	}

	public function UnMuteUser($username) {

		// нет доступа
		if (!($this->oUserCurrent->isAdministrator() OR $this->CheckAccess('super_admins_unmute'))) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_access_denied'),$this->Lang_Get('error'));
			return;
		}

		$oUserSB=$this->User_GetUserByLogin($username);
		// пользователь не найден
		if (!($oUserSB)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_notfound'),$this->Lang_Get('error'));
			return;
		}

		// пользователя нельзя разбанить/забанить
		if (in_array($oUserSB->getId(), Config::Get("plugin.shoutbox.super_users"))) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_wrong_user'),$this->Lang_Get('error'));
			return;
		}

		// пользователя не забанен
		if (!($this->PluginShoutbox_Shout_InBlackList($oUserSB->getLogin()))) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_user_already_unmuted'),$this->Lang_Get('error'));
			return;
		}

		return true;

	}

	// Events

	protected function RegisterEvent() {

		$this->AddEventPreg('/^add$/','EventShoutboxAdd');
		$this->AddEventPreg('/^update$/','EventShoutboxUpdate');
		$this->AddEventPreg('/^moderate$/','EventShoutboxModerate');
		$this->AddEventPreg('/^history/i','/^(page([1-9]\d{0,5}))?$/i','EventHistory');
	}

	protected function EventShoutboxUpdate() {

		$this->Viewer_SetResponseAjax('json');

		if ($this->KeepUserAuthorization()) {
			die();
		}

		$crerror = $this->Lang_Get('plugin.shoutbox.copyright_error');
		$perpage = (Config::Get ('plugin.shoutbox.count_in_chat'));
		$iLastIdRE = getRequest('iLastId')>0 ? getRequest('iLastId') : -1;

		$key = md5(getRequest('vkey'))=='cb7b099c596d9b789c4c125f4176538d' ? getRequest('vkey') : false;
		$hash100 = md5($crerror) == '28103e4148479354da85bcf128a4fffa' ? true : false;
		$hash101 = md5($crerror) == 'fa200236bbf7751036ae4405bca96e1c' ? true : false;

		$mod = 0;

		if ($this->User_IsAuthorization()) {
			if ($this->oUserCurrent->isAdministrator() OR $this->CheckAccess()) {
				$mod = 1;
			}
		}

		$aLastShouts = $this->PluginShoutbox_Shout_GetLast($perpage,$mod,1,$iLastIdRE);

		if ($aLastShouts) {

			$shoutdata = $this->BuildHTMLFromShouts($aLastShouts);

			if (!$key) {
				if ($hash100 or $hash101) {
					$html = $crerror.$shoutdata['html'];
				} else {
					$html = 'hacking atempt!<br><br>'.$crerror;
				}
			} else {
				$html = $shoutdata['html'];
			}
			$lastid = $shoutdata['lastid'];

			$this->Viewer_AssignAjax('aDoUpdateResult',$iLastIdRE>0 ? true : false);
			$this->Viewer_AssignAjax('aHtml',$html);
			$this->Viewer_AssignAjax('iLastId',$lastid);
			$this->Viewer_AssignAjax('sKey',$key);
		}

		return;
	}


	protected function EventHistory() {

		if (!Config::Get('plugin.shoutbox.allow_view_history')) {
			return $this->EventNotFound();
		}

		if ($this->KeepUserAuthorization()) {
			die();
		}

		$mod = 0;

		if ($this->User_IsAuthorization()) {
			if ($this->oUserCurrent->isAdministrator() OR $this->CheckAccess()) {
				$mod = 1;
			}
		}

		$iCount =  $this->PluginShoutbox_Shout_GetCount($mod);

		$iPage=$this->GetParamEventMatch(0,2) ? $this->GetParamEventMatch(0,2) : 1;
		$page = $iPage>=1 ? $iPage : 1;

		$perpage = (Config::Get ('plugin.shoutbox.count_in_history'));
		$pages = ceil($iCount/$perpage);

		$page = $page>$pages ? $page=$pages : $page;

		$aPaging=$this->Viewer_MakePaging($iCount,$page,$perpage,4,Router::GetPath('shoutbox').'history');

		$aShouts = $this->PluginShoutbox_Shout_GetLast($perpage,$mod,$page);

		$shoutdata = $this->BuildHTMLFromShouts($aShouts,'history');
		$html = $shoutdata['html'];

		$this->Viewer_Assign('aHTML',$html);
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign('iCount',$iCount);
		return;

	}
	protected function EventShoutboxAdd() {

		$this->Viewer_SetResponseAjax('json');

		if (!$this->User_IsAuthorization()) {
			die();
		}

		// пользователя забанен
		if ($this->PluginShoutbox_Shout_InBlackList($this->oUserCurrent->getLogin())) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_you_has_been_muted'),$this->Lang_Get('error'));
			return;
		}

		$sText = getRequest('sText');

		// меняем #Nick на юзеро-ссылки
		$sText = preg_replace('/#(\w*)/', '<ls user="${1}" />', $sText);

		if (isset($sText) and $sText != '') {
			if (substr($sText,0,1) == '/') {
				if ($this->oUserCurrent->isAdministrator() OR $this->CheckAccess()) {
					$aCommandArray = explode(" ",substr($sText,1));
					$aCmd = $aCommandArray[0];
					switch ($aCmd) {
						case 'admin':
							$aOption1 = substr($sText,7);
							$shout = Engine::GetEntity('PluginShoutbox_Shout');
							$shout->setUserId($this->oUserCurrent->GetId());
							$shout->setText($this->Text_Parser($aOption1));
							$shout->setDate(time());
							$shout->setMod(1);
							$this->PluginShoutbox_Shout_Push($shout);
						break;
						case 'warn':
							$aOption1 = substr($sText,6);
							$shout = Engine::GetEntity('PluginShoutbox_Shout');
							$shout->setUserId($this->oUserCurrent->GetId());
							$shout->setText($this->Text_Parser($aOption1));
							$shout->setDate(time());
							$shout->setMod(2);
							$this->PluginShoutbox_Shout_Push($shout);
						break;
						case 'mute':
							// /mute %Username%
							$aOption1 = $aCommandArray[1]; //user name
							if ($this->MuteUser($aOption1)) {
								$shout = Engine::GetEntity('PluginShoutbox_Shout');
								$shout->setUserId($this->oUserCurrent->GetId());
								$shout->setText($this->Text_Parser($this->Lang_Get('plugin.shoutbox.moderator_mute',array('username'=>$aOption1))));
								$shout->setDate(time());
								$shout->setMod(2);
								$this->PluginShoutbox_Shout_Push($shout);
								$this->PluginShoutbox_Shout_AddToBlackList($aOption1);
							}
						break;
						case 'unmute':
							// /unmute %Username%
							$aOption1 = $aCommandArray[1]; //user name
							if ($this->UnMuteUser($aOption1)) {
								$shout = Engine::GetEntity('PluginShoutbox_Shout');
								$shout->setUserId($this->oUserCurrent->GetId());
								$shout->setText($this->Text_Parser($this->Lang_Get('plugin.shoutbox.moderator_unmute',array('username'=>$aOption1))));
								$shout->setDate(time());
								$shout->setMod(1);
								$this->PluginShoutbox_Shout_Push($shout);
								$this->PluginShoutbox_Shout_RemoveFromBlackList($aOption1);
							}
						break;
						default:
						break;
					}
				} else {
					$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_access_denied'),$this->Lang_Get('error'));
					return;
				}
			} else {

				$shout = Engine::GetEntity('PluginShoutbox_Shout');
				$shout->setUserId($this->oUserCurrent->GetId());

				$sText=$this->Text_Parser($sText);
				$minchars = Config::Get ('plugin.shoutbox.min_chars');
				$maxchars = Config::Get ('plugin.shoutbox.max_chars');
				if (!func_check($sText,'text',$minchars,$maxchars)) {
					$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.chars_limit',array('min'=>$minchars,'max'=>$maxchars)),$this->Lang_Get('error'));
					return;
				}

				$jevix = $this->Text_LoadJevixConfig('shoutbox',true);
				$shout->setText($this->Text_Parser($sText));

				$shout->setDate(time());
				$this->PluginShoutbox_Shout_Push($shout);

				$PlugSkill = (Config::Get ('plugin.shoutbox.SkillPerShout'));
				$PlugRating = (Config::Get ('plugin.shoutbox.RatingPerShout'));

				if ($PlugSkill OR $PlugRating) {

					$oPlugUser=$this->User_GetUserById($this->oUserCurrent->GetId());

					if ($PlugSkill) {
						$iSkillNew=$oPlugUser->getSkill()+$PlugSkill;
						$oPlugUser->setSkill($iSkillNew);
					}

					if ($PlugRating) {
						$iRatingNew=$oPlugUser->getRating()+$PlugRating;
						$oPlugUser->setRating($iRatingNew);
					}

					$this->User_Update($oPlugUser);

				}

			}
		}
		return;
	}
	protected function EventShoutboxModerate() {

		$this->Viewer_SetResponseAjax('json');

		if (!$this->User_IsAuthorization()) {
			die();
		}

		// пользователя забанен
		if ($this->PluginShoutbox_Shout_InBlackList($this->oUserCurrent->getLogin())) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.shoutbox.moderator_access_denied'),$this->Lang_Get('error'));
			return;
		}
		
		if ($this->oUserCurrent->isAdministrator() OR $this->CheckAccess()) {
			$iId = (int)getRequest('iId');
			$iType = (int)getRequest('iType');
			if ((isset($iId)) and ($iType >= 1 and $iType <= 3)){
				$this->Viewer_AssignAjax('bMod',$this->PluginShoutbox_Shout_Moderate($iId,$iType));
			}
		} else {
			return false;
		}
	}

	public function BuildHTMLFromShouts($Shouts,$mode='default') {
		
		static $counter;
		static $html;
		static $lastid;

		$shoutdata = array();
	
		foreach($Shouts as $shout) {

			$shoutview = array();

			$counter++;
			$shoutdata[$counter]['id'] = $shout->GetId();
			$shoutdata[$counter]['status'] = $shout->GetStatus();
			$shoutdata[$counter]['mod'] = $shout->GetMod();
			$shoutdata[$counter]['author'] = $this->User_GetUserById($shout->GetUserId());
			$shoutdata[$counter]['author_name'] = $shoutdata[$counter]['author']->GetLogin();
			$shoutdata[$counter]['message'] = $shout->GetText();
			$shoutdata[$counter]['date_dt'] = date("d.m.Y",  $shout->GetDate());
			$shoutdata[$counter]['date_tm'] = date("H:i",  $shout->GetDate());

			$shoutview['classes'] = 'sbpost';

			//setup classes
			if ($counter%2 == 0){
				$shoutview['classes'] .= ' chess';
			}
			if ($shoutdata[$counter]['status']==1) {
				$shoutview['classes'] .= ' deleted';
			}


			if ($this->User_IsAuthorization()) {

				if (Config::Get ('plugin.shoutbox.colorize_my_messages')) {
					if (preg_match('/ls-user">'.$this->oUserCurrent->GetLogin().'</i',$shoutdata[$counter]['message'])) {
						$shoutview['classes'] .= ' forme';
					}
				}

				if (Config::Get ('plugin.shoutbox.colorize_my_posts')) {
					if ($this->oUserCurrent->GetLogin() == $shoutdata[$counter]['author_name']) {
						$shoutview['classes'] .= ' mypost';
					}
				}

				if ($this->oUserCurrent->isAdministrator() OR $this->CheckAccess()) {
					$shoutview['viewmoderator'] = true;
				}

			}

			if ($shoutdata[$counter]['mod']!=0) {

				$shoutdata[$counter]['modpost'] = true;
				$shoutview['postpref'] = '*** ';
				$shoutview['postsufx'] = ' ***';
			}

			switch($shoutdata[$counter]['mod']) {
				case 1:
					$shoutview['postclasses']='modmessage';
				break;
				case 2:
					$shoutview['postclasses']='modwarning';
				break;
				default: break;
			}

			$viewmode = (Config::Get ('plugin.shoutbox.view_module'));

			if ($mode == 'history') {
				$viewmode = 'HomePage';
			}


			$shoutdata[$counter]['view'] = $shoutview;
			unset($shoutview);

			if ($lastid < $shoutdata[$counter]['id']) {
				$lastid = $shoutdata[$counter]['id'];
			}

		}

		$html .= $this->buildhtml_all_shouts($shoutdata,$viewmode);

		$return_array = array(
			'html' => $html,
			'lastid' => $lastid
		);

		return $return_array;
	}

	public function buildhtml_all_shouts($aShout,$viewmode) {

		$oViewer=$this->Viewer_GetLocalViewer();

		$oViewer -> Assign ('aShouts', $aShout);

		if ($viewmode == 'Block') {
			return $oViewer->Fetch(Plugin::GetTemplatePath (__CLASS__)."shout_block.tpl");
		} else {
			return $oViewer->Fetch(Plugin::GetTemplatePath (__CLASS__)."shout_normal.tpl");
		}
	}

}

?>