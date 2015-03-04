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
        'engines' => array('engines'),
    ),
    'blocks' => array(
        'right' => array('stream'=>array('priority'=>100),'tags'=>array('priority'=>50),'blogs'=>array('params'=>array(),'priority'=>1))
    ),
    'clear' => false,
));
Config::Set('router.page.engines', 'PluginEngines_ActionEngines');return $config;
?>