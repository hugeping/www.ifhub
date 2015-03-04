<?php
/**
 * Views - подсчет количества просмотров топиков
 *
 * Версия:	1.0.1
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_views
 *
 **/

$config = array();

// Считать просмотры только от авторизованных пользователей
$config['only_users'] = false;

// Считать только первый просмотр топика пользователем (в пределах сессии)
$config['only_once'] = true;

// Использовать сортировку топиков по числу просмотров
$config['use_sort'] = true;

// Отображаются только топики, которые просматривались в выбранный период, независимо от времени их создания.
// Использует данные плагина Viewstat (должен быть установлен). По умолчанию отключено (false).
$config['stat_date_filter'] = false;

// Показывать число просмотров в панели информации топика
$config['show_info'] = true;

Config::Set('db.table.topic_view', '___db.table.prefix___topic_view');

return $config;
?>