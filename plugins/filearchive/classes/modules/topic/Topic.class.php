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

class PluginFilearchive_ModuleTopic extends PluginFilearchive_Inherit_ModuleTopic {

    public function Init() {
        parent::Init();
        array_push($this->aTopicTypes, 'file');
    }
}
?>