<?php
/*
  Cutplacetext plugin
  (P) PSNet, 2008 - 2013
  http://psnet.lookformp3.net/
  http://livestreet.ru/profile/PSNet/
  http://livestreetcms.com/profile/PSNet/
  http://livestreetguide.com/developer/PSNet/
*/

class PluginCutplacetext_ActionCutplacetext extends ActionPlugin {

  protected $oUserCurrent = null;

  // ---

  public function Init () {
    if (!$this -> oUserCurrent = $this -> User_GetUserCurrent () or !$this -> oUserCurrent -> isAdministrator ()) {
      return Router::Action ('error');
    }
    $this -> SetDefaultEvent ('index');
  }

  // ---

  protected function RegisterEvent () {
    $this -> AddEvent ('index', 'EventShowOrEdit');
  }

  // ---

  public function EventShowOrEdit () {
    if (isPost ('submit_edit_text_content') and is_string (getRequest ('cutplacetext'))) {
      $this -> Security_ValidateSendForm ();
      Config::Set ('plugin.cutplacetext.Text_Source', (string) getRequest ('cutplacetext'));
      CE::SaveMyConfig ($this);
      $this -> Message_AddNoticeSingle ('Ok');
    }
  }
	
}

?>