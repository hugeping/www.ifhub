<?php
/**
 * File Archive - тип топика "файл"
 *
 * Версия:	1.0.3
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_filearchive
 *
 **/

$config = array();

// Максимальное количество символов в одном топике-файле
$config['text_max_length'] = Config::Get('module.topic.max_length');

// Путь к каталогу с файлами
$config['uploads_files'] = '___path.uploads.root___/files';

// Максимальный размер файла, байт
$config['max_size'] = 1048576; // 1 МБ

// Доступ к скачиванию только пользователям
$config['only_users'] = true;

// Использовать ограничение рейтинга для доступа к скачиванию (используется при $config['only_users'] = true).
$config['use_limit'] = false;

// Порог рейтинга при котором юзер может скачивать топики (используется при $config['only_users'] = true и $config['use_limit'] = true).
$config['limit_rating'] = 0;

// Разрешенные расширения для файла
$config['allow_ext'] = array('pdf', 'rar', 'zip');

// Показывать число скачиваний в панели информации топика
$config['show_info'] = true;

// Добавить иконку в меню "Создать"
$config['show_write_item'] = false;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// НЕ ИЗМЕНЯТЬ
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Config::Set('router.page.file', 'PluginFilearchive_ActionFile');

$aTypes = Config::Get('block.rule_topic_type');
$aTypes['action']['file'] = array('add','edit');
Config::Set('block.rule_topic_type', $aTypes);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

return $config;
?>