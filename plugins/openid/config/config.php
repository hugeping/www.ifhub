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
 * Используемые таблицы БД
 */
$config['table']['openid'] = '___db.table.prefix___openid';
$config['table']['openid_tmp'] = '___db.table.prefix___openid_tmp';

/**
 * Настраиваем роутинг
 */
Config::Set('router.page.openid_login', 'PluginOpenid_ActionLogin');
Config::Set('router.page.openid_settings', 'PluginOpenid_ActionSettings');

/**
 * Общие настройки
 */
$config['file_store']   = '___sys.cache.dir___php_consumer_livestreet'; // каталог для хранения данных OpenID
$config['time_key_limit']   = 60*60*1; // in seconds, время актуальности временных данных при авторизации
$config['mail_required']   = false; // обязательный ввод e-mail
$config['buggy_gmp']   = false; // для обхода проблемы с шибкой "Bad signature" на некоторых серверах

/**
 * Настройки авторизации ВКонтакте
 */
$config['vk']['id']   = '1111'; // ID приложения
$config['vk']['secure_key']   = '1111'; // Защищенный ключ приложения
$config['vk']['transport_path']   = '/plugins/openid/include/xd_receiver.html'; // Путь от корня сайта до файла транспорта

/**
 * Настройки Facebook Application
 */
$config['fb']['id']   = '1111'; // Application ID
$config['fb']['secret']   = '1111'; // Application Secret

/**
 * Настройки Twitter Application
 */
$config['twitter']['token']   = '1111'; // Consumer key
$config['twitter']['token_secret']   = '1111'; // Consumer secret



$config['$root$']['block']['rule_settings_openid'] = array(
	'action'  => array( 'openid_settings' ),
	'blocks'  => array( 'right' => array('actions/ActionProfile/sidebar.tpl') ),
);


return $config;
?>