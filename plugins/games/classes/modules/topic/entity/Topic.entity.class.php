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

class PluginGames_ModuleTopic_EntityTopic extends PluginGames_Inherit_ModuleTopic_EntityTopic {

	/**
	 * Добавляем правила валидации
	 */
	public function Init() {
		parent::Init();
        $this->aValidateRules[]=array('topic_title','string','max'=>200,'min'=>2,'allowEmpty'=>false,'label'=>$this->Lang_Get('topic_create_title'),'on'=>array('games'));
        $this->aValidateRules[]=array('topic_text_source','string','max'=>Config::Get('module.topic.max_length'),'min'=>2,'allowEmpty'=>false,'label'=>$this->Lang_Get('topic_create_text'),'on'=>array('games'));
        $this->aValidateRules[]=array('topic_text_source','topic_unique','on'=>array('games'));
        $this->aValidateRules[]=array('topic_tags','tags','count'=>15,'label'=>$this->Lang_Get('topic_create_tags'),'allowEmpty'=>Config::Get('module.topic.allow_empty_tags'),'on'=>array('games'));
        $this->aValidateRules[]=array('blog_id','blog_id','on'=>array('games'));
        $this->aValidateRules[]=array('topic_type','topic_type','on'=>array('games'));
	}

}
?>