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

$config = array();
    
/*
Config::Set('block.rule_engines', array(
    'action' => array(
        'index'
    ),
    'blocks' => array(
        'right' => array(
            'engines' => array('params' => array('plugin' => 'engines'), 'priority' => 999),
        )
    ),
    'clear' => false,
));
*/
Config::Set('block.rule_engines_page', array(
    'action' => array(
        'engines' => array('engines', 'views', 'discussed', 'top', 'newall','/^(page([1-9]\d{0,5}))?$/i' ),
    ),
    'blocks' => array(
        'right' => array('stream'=>array('priority'=>100),'tags'=>array('priority'=>50),'blogs'=>array('params'=>array(),'priority'=>1))
    ),
    'clear' => false,
));

$aTypes = Config::Get('block.rule_topic_type');
$aTypes['action']['engines'] = array('add','edit');
Config::Set('block.rule_topic_type', $aTypes);

Config::Set('router.page.engines', 'PluginEngines_ActionEngines');return $config;
?>