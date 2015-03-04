<?php
/* -------------------------------------------------------
*
*   LiveStreet (v1.x)
*   Plugin Engines (v.0.1)
*   Copyright © 2015 Bishovec Nikolay, service http://pluginator.ru
*
* --------------------------------------------------------
*
*   Page's service author: http://netlanc.net
*   Plugin Page: http://pluginator.ru
*   CMS Page http://livestreetcms.com
*   Contact e-mail: netlanc@yandex.ru
*
---------------------------------------------------------
*/

if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginEngines extends Plugin
{
    protected $aInherits = array(
            'module' => array('ModuleTopic'),
	'entity' => array('ModuleTopic_EntityTopic'),
	'mapper' => array('ModuleTopic_MapperTopic'),
	);

    protected $aDelegates = array(
        'template' => array(
            'topic_engines.tpl',
	)
    );

    public function Activate()
    {
        $this->addEnumType('prefix_topic','topic_type','engines');
		
        if (!$this->isFieldExists('prefix_topic', 'topic_field_link1')) {
            $this->ExportSQL(dirname(__FILE__) . '/dump.sql');
        }

        return true;
    }


    public function Deactivate()
    {
        return true;
    }

    public function Init()
    {
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('engines') . 'css/style.css'); 
    }
}

?>