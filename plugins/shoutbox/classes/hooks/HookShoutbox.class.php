<?php
class PluginShoutbox_HookShoutbox extends Hook {

	public function RegisterHook() {
		$this->AddHook('init_action','CheckUpdateUnterval');
		$this->AddHook('template_content_begin','ViewShoutbox');
	}
	public function ViewShoutbox() {

		if (Config::Get('plugin.shoutbox.only_authorized') AND !$this->User_IsAuthorization()) {
			return;
		}

		if (Config::Get ('plugin.shoutbox.view_module') != 'HomePage') {
			return;
		}

		$rout = Router::GetAction();

		if ($rout == 'index'){
					

			return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'shoutbox.tpl');
		}
	}

	public function CheckUpdateUnterval() {
		
		$updateint = @$_COOKIE['shoutbox_update_interval'];
		switch ($updateint) {
			case 0:
				$this->Viewer_Assign('iUPInterval',0);
				break;
			case 10000:
				$this->Viewer_Assign('iUPInterval',10);
				break;
			case 30000:
				$this->Viewer_Assign('iUPInterval',30);
				break;
			case 60000:
				$this->Viewer_Assign('iUPInterval',60);
				break;
			case 300000:
				$this->Viewer_Assign('iUPInterval',300);
				break;
			default:
				$this->Viewer_Assign('iUPInterval',10);
				setcookie('shoutbox_update_interval','10000',time()+Config::Get('sys.cookie.time'),Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
				break;
		}
		
	}
}

?>