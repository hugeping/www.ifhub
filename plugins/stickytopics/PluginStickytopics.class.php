<?php
/*-------------------------------------------------------
*
*   StickyTopics v2
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail:kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin'))
{
    die('Hacking attempt!');
}

class PluginStickytopics extends Plugin
{

    protected $aInherits=array('action'=>array('ActionBlog', 'ActionAjax', 'ActionProfile', 'ActionAdmin', 'ActionIndex'), 'module'=>array('ModuleACL'=>'PluginStickytopics_ModuleACL'), 'mapper'=>array('ModuleTopic_MapperTopic'=>'PluginStickytopics_ModuleTopic_MapperTopic'));

    public function Activate()
    {
        if (!$this->isTableExists('prefix_stickytopics_sticky_topic'))
        {
        /**
	 * При активации выполняем SQL дамп
	 */
            $this->ExportSQL(dirname(__FILE__) . '/install.sql');
        }
        return true;
    }

}
?>