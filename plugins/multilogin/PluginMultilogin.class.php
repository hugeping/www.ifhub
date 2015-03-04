<?php
/**
 * MultiLogin - авторизация без сброса cookie
 *
 * Версия:	1.0.1
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_multilogin
 *
 **/

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginMultilogin extends Plugin {
    protected $aInherits = array(
        'module' => array('ModuleUser'),
    );

    public function Activate() {
        return true;
    }


    public function Deactivate() {
        return true;
    }

    public function Init() {
    }
}
?>