<?php
/*
  Stopwords plugin
  (P) PSNet, 2008 - 2012
  http://psnet.lookformp3.net/
  http://livestreet.ru/profile/PSNet/
  http://livestreetcms.com/profile/PSNet/
*/

$config = array ();

// Массив запрещенных слов, заменяться будут или части слов или точные совпадения в зависимости от параметра Strict_Replace
$config ['Stop_Words'] = array (
                                'кака',
                               );

// html строка замены. Может быть также массивом с точным соответствием для Stop_Words массива,
// т.е. каждому слову из Stop_Words будет соответсвовать вариант из Replace_With
$config ['Replace_With'] = '[<i>Вырезано цензурой</i>]';

// Искать только точные совпадения слов (регистр-независимые), не заменять части других слов
// т.е. заменять "кака", но не "скакалка"
$config ['Strict_Replace'] = true;

return $config;

?>