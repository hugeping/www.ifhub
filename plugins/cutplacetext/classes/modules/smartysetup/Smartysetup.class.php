<?php
/*
  Cutplacetext plugin
  (P) PSNet, 2008 - 2013
  http://psnet.lookformp3.net/
  http://livestreet.ru/profile/PSNet/
  http://livestreetcms.com/profile/PSNet/
  http://livestreetguide.com/developer/PSNet/
*/

class PluginCutplacetext_ModuleSmartysetup extends Module {

  public function Init () {}
  
  // ---

  public function SetDefaultFiltersForSmartyObject ($oSmarty) {
    // lets setup smarty for filtering all output data and parse it - its good way
    $oSmarty -> registerFilter ('output', array ($this, 'ParseSmartyOutputContent'));
  }
  
  // ---
  
  public function ParseSmartyOutputContent ($tpl_output, Smarty_Internal_Template $template) {
    // all that will be displayed will pass through this function
    return preg_replace ('#<a\s++[^>]*name="cut"[^>]*+></a>#', '\\0' . Config::Get ('plugin.cutplacetext.Text_Source'), $tpl_output);
  }
  
}

?>