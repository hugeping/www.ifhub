<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/
/**
 * Настройки для локального сервера.
 * Для использования - переименовать файл в config.local.php
 */

/**
 * Настройка базы данных
 */
$config['db']['params']['host'] = 'localhost';
$config['db']['params']['port'] = '3306';
$config['db']['params']['user'] = 'ifhub';
$config['db']['params']['pass'] = 'XXXXXX';
$config['db']['params']['type']   = 'mysql';
$config['db']['params']['dbname'] = 'ifhub';
$config['db']['table']['prefix'] = '';

$config['path']['root']['web'] = 'http://ifhub.ru';
$config['path']['root']['server'] = '/home/peter/www/ifhub.ru';
$config['path']['offset_request_url'] = '0';
$config['db']['tables']['engine'] = 'InnoDB';

/**
* Настройки почтовых уведомлений
*/
$config['sys']['mail']['from_email']       = 'admin@ifhub.ru';      // Мыло с которого отправляются все уведомления
$config['sys']['mail']['from_name']        = 'Служба уведомлений ifHub.ru';  // Имя с которого отправляются все уведомления
$config['sys']['mail']['charset']          = 'UTF-8';                // Какую кодировку использовать в письмах
$config['sys']['mail']['smtp']['host']     = 'smtp.yandex.ru';            // Настройки SMTP - хост
$config['sys']['mail']['smtp']['port']     = 465;
$config['sys']['mail']['smtp']['user']     = 'admin@ifhub.ru';                     // Настройки SMTP - пользователь
$config['sys']['mail']['smtp']['password'] = 'XXXXXX';                     // Настройки SMTP - пароль
$config['sys']['mail']['smtp']['secure']   = 'ssl';                     // Настройки SMTP - протокол шифрования: tls, ssl
$config['sys']['mail']['smtp']['auth']     = true;                   // Использовать авторизацию при отправке
$config['sys']['mail']['include_comment']  = true;                   // Включает в уведомление о новых комментах текст коммента
$config['sys']['mail']['include_talk']     = true;                   // Включает в уведомление о новых личных сообщениях текст сообщения

$config['module']['blog']['index_good']      =  5;   // Рейтинг топика выше которого(включительно) он попадает на главную

return $config;
?>