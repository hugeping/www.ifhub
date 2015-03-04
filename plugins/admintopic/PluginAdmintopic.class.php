<?php

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginAdmintopic extends Plugin {

    protected $aInherits = array(
        'entity' => array('ModuleTopic_EntityTopic' => '_ModuleTopic_EntityTopic'),
        'mapper' => array('ModuleTopic_MapperTopic' => '_ModuleTopic_MapperTopic'),
    );
    // Активация плагина
    public function Activate() {
        return true;
    }

    // Деактивация плагина
    public function Deactivate(){       
    	return true;
    }
    // Инициализация плагина
    public function Init() {
        $this->Viewer_AppendStyle(Plugin::GetTemplatePath(__CLASS__)."/css/styles.css"); // Добавление своего CSS
    }
}
?>
