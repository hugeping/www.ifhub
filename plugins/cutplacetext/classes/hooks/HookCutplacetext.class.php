<?php
/*
  Cutplacetext plugin
  (P) PSNet, 2008 - 2013
  http://psnet.lookformp3.net/
  http://livestreet.ru/profile/PSNet/
  http://livestreetcms.com/profile/PSNet/
  http://livestreetguide.com/developer/PSNet/
*/

class PluginCutplacetext_HookCutplacetext extends Hook {

  public function RegisterHook () {
    $this -> AddHook ('engine_init_complete', 'EngineInitComplete');
    $this -> AddHook ('template_footer_end', 'FooterEnd');
  }

  // ---

  public function EngineInitComplete () {
    $this -> PluginCutplacetext_Smartysetup_SetDefaultFiltersForSmartyObject ($this -> Viewer_GetSmartyObject ());
  }

  // ---

  public function FooterEnd () {
    return $this -> Viewer_Fetch (Plugin::GetTemplatePath (__CLASS__) . 'footer_end.tpl');
  }

}

?>