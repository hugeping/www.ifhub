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

class PluginViews_ModuleTopic_EntityTopic extends PluginViews_Inherit_ModuleTopic_EntityTopic {
    public function AddView() {
        return $this->PluginViews_Topic_AddView($this->getId());
    }

	/**
	 * Возвращает число прочтений топика
	 *
	 * @return int|null
	 */
	public function getCountRead() {
        return $this->PluginViews_Topic_GetCountRead($this->getId());
	}
}
?>