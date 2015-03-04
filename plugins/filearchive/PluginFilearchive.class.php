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

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginFilearchive extends Plugin {
    protected $aInherits = array(
        'module' => array('ModuleTopic'),
        'entity' => array('ModuleTopic_EntityTopic'),
    );

    protected $aDelegates = array(
        'template' => array(
            'topic_file.tpl',
        )
    );

    public function Activate() {
        $this->addEnumType(Config::Get('db.table.topic'),'topic_type','file');
        return true;
    }


    public function Deactivate() {
        return true;
    }

    public function Init() {
		$this->Viewer_AppendStyle(Plugin::GetTemplateWebPath(__CLASS__) . 'css/filearchive.css');
    }
}
?>