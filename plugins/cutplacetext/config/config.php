<?php
/*
  Cutplacetext plugin
  (P) PSNet, 2008 - 2013
  http://psnet.lookformp3.net/
  http://livestreet.ru/profile/PSNet/
  http://livestreetcms.com/profile/PSNet/
  http://livestreetguide.com/developer/PSNet/
*/

$config = array ();

// --- редактировать здесь ничего не нужно - все через веб-интерфейс ---

$config ['Text_Source'] = 'Сюда внести нужный текст. Редактировать через админку плагина.';

// ---

$config ['url'] = 'cutplacetext';
$config ['$root$']['router']['page'][$config ['url']] = 'PluginCutplacetext_ActionCutplacetext';

return $config;

?>