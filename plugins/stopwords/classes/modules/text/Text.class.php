<?php
/*
  Stopwords plugin
  (P) PSNet, 2008 - 2012
  http://psnet.lookformp3.net/
  http://livestreet.ru/profile/PSNet/
  http://livestreetcms.com/profile/PSNet/
*/

class PluginStopwords_ModuleText extends PluginStopwords_Inherit_ModuleText {

  public function Parser ($sText) {
    $NewResult = parent::Parser ($sText);
    $NewResult = $this -> CheckForStopWords ($NewResult);
    return $NewResult;
  }
  
  // ---
  
  public function CheckForStopWords ($sText) {
    $aWords = Config::Get ('plugin.stopwords.Stop_Words');
    $sReplaceWith = Config::Get ('plugin.stopwords.Replace_With');
    if (Config::Get ('plugin.stopwords.Strict_Replace')) {
      for ($i = 0; $i < count ($aWords); $i ++) {
        $aWords [$i] = '#(?<=[^\wа-яА-ЯіїєІЇЄ]|^)' . quotemeta ($aWords [$i]) . '(?=[^\wа-яА-ЯіїєІЇЄ]|$)#iuUS';
      }
      $NewText = preg_replace ($aWords, $sReplaceWith, $sText);
    } else {
      $NewText = str_replace ($aWords, $sReplaceWith, $sText);
    }
    return $NewText;
  }

}

?>