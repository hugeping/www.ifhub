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

class PluginGames_ModuleTopic extends PluginGames_Inherit_ModuleTopic {

    public function Init() {
        parent::Init();
        array_push($this->aTopicTypes, 'games');
        array_push($this->aTopicTypes, 'engines');
    }
    public function GetTopicsByFilter($aFilter,$iPage=1,$iPerPage=10,$aAllowData=null) {
        if (array_key_exists('topic_publish', $aFilter) && $aFilter['topic_publish']!=0){
            if (empty($aFilter['topic_type'])){
                $aFilter['topic_type'] = array('games', 'engines', 'topic','link','question','photoset');
            }
        }
        return parent::GetTopicsByFilter($aFilter,$iPage,$iPerPage,$aAllowData);
    }

    public function GetCountTopicsByFilter($aFilter) {
        if ($aFilter['topic_publish']!=0){
            if (empty($aFilter['topic_type'])){
                $aFilter['topic_type'] = array('games', 'engines', 'topic','link','question','photoset');
            }
        }
        return parent::GetCountTopicsByFilter($aFilter);
    }
    public function GetTopicsGamesLast($iCount) {
        $aFilter=array(
            'blog_type' => array(
                'personal',
                'open',
            ),
            'topic_publish' => 1,
            'topic_type' => 'games',
        );
        /**
        * Если пользователь авторизирован, то добавляем в выдачу
        * закрытые блоги в которых он состоит
        */
        if($this->oUserCurrent) {
            $aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
            if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
        }
        $aReturn=$this->GetTopicsByFilter($aFilter,1,$iCount);
            if (isset($aReturn['collection'])) {
            return $aReturn['collection'];
        }
        return false;
    }
    public function UpdateTopicFieldsGames(ModuleTopic_EntityTopic $oTopic) {
        $oTopic->setDateEdit(date("Y-m-d H:i:s"), time()+1);
        if ($this->oMapperTopic->UpdateTopicFieldsGames($oTopic)) {

            //чистим зависимые кеши
            $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('topic_update',"topic_update_user_{$oTopic->getUserId()}"));
            $this->Cache_Delete("topic_{$oTopic->getId()}");
            return true;
        }
        return false;
    }
}
?>