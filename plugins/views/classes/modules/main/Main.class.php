<?php
/**
 * Views - подсчет количества просмотров топиков
 *
 * Версия:	1.0.1
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_views
 *
 **/

class PluginViews_ModuleMain extends PluginViews_ModuleMain_Inherit_PluginMobiletpl_ModuleMain {
	public function IncTopicCountRead($oTopic) {
        return $this->PluginViews_Topic_AddView($oTopic->getId());
	}
}
?>