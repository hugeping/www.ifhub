<?php
/* -------------------------------------------------------
*
*   LiveStreet (v1.x)
*   Plugin Games (v.0.1)
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
    

Config::Set('block.rule_games', array(
    'action' => array(
        'index'
    ),
    'blocks' => array(
        'right' => array(
            'games' => array('params' => array('plugin' => 'games'), 'priority' => 999),
        )
    ),
    'clear' => false,
));

Config::Set('block.rule_games_page', array(
    'action' => array(
        'games' => array('games', 'views', 'discussed', 'top', 'newall', '/^(page([1-9]\d{0,5}))?$/i' ),
    ),
    'blocks' => array(
        'right' => array('stream'=>array('priority'=>100),'tags'=>array('priority'=>50),'blogs'=>array('params'=>array(),'priority'=>1))
    ),
    'clear' => false,
));

$aTypes = Config::Get('block.rule_topic_type');
$aTypes['action']['games'] = array('add','edit');
Config::Set('block.rule_topic_type', $aTypes);

Config::Set('router.page.games', 'PluginGames_ActionGames');return $config;
?>