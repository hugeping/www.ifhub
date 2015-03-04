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

class PluginViews_ModuleTopic extends PluginViews_Inherit_ModuleTopic {
    /**
     * список топиков из персональных блогов
     *
     * @param int $iPage	Номер страницы
     * @param int $iPerPage	Количество элементов на страницу
     * @param string $sShowType	Тип выборки топиков
     * @param string|int $sPeriod	Период в виде секунд или конкретной даты
     * @return array
     */
    public function GetTopicsPersonal($iPage,$iPerPage,$sShowType='good',$sPeriod=null) {
        if ($sShowType != 'views') {
            return parent::GetTopicsPersonal($iPage,$iPerPage,$sShowType,$sPeriod);
        } else {
            if (is_numeric($sPeriod)) {
                // количество последних секунд
                $sPeriod=date("Y-m-d H:00:00",time()-$sPeriod);
            }
            $bViewstat = (class_exists('PluginViewstat') && in_array('viewstat', $this->Plugin_GetActivePlugins()));
            $aFilter=array(
                'blog_type' => array('personal'),
                'topic_publish' => 1,
                'viewstat' => $bViewstat,
            );
            if ($sPeriod) {
                $aFilter[($bViewstat && Config::Get('plugin.views.stat_date_filter')) ? 'stat_date_more' : 'topic_date_more'] = $sPeriod;
            }
            $aFilter['order']=array('t.topic_count_read desc','t.topic_id desc');
            return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
        }
    }
    /**
     * Список топиков из коллективных блогов
     *
     * @param int $iPage	Номер страницы
     * @param int $iPerPage	Количество элементов на страницу
     * @param string $sShowType	Тип выборки топиков
     * @param string $sPeriod	Период в виде секунд или конкретной даты
     * @return array
     */
    public function GetTopicsCollective($iPage,$iPerPage,$sShowType='good',$sPeriod=null) {
        if ($sShowType != 'views') {
            return parent::GetTopicsCollective($iPage,$iPerPage,$sShowType,$sPeriod);
        } else {
            if (is_numeric($sPeriod)) {
                // количество последних секунд
                $sPeriod=date("Y-m-d H:00:00",time()-$sPeriod);
            }
            $bViewstat = (class_exists('PluginViewstat') && in_array('viewstat', $this->Plugin_GetActivePlugins()));
            $aFilter=array(
                'blog_type' => array('open'),
                'topic_publish' => 1,
                'viewstat' => $bViewstat,
            );
            if ($sPeriod) {
                $aFilter[($bViewstat && Config::Get('plugin.views.stat_date_filter')) ? 'stat_date_more' : 'topic_date_more'] = $sPeriod;
            }
            $aFilter['order']=array('t.topic_count_read desc','t.topic_id desc');
            /**
             * Если пользователь авторизирован, то добавляем в выдачу
             * закрытые блоги в которых он состоит
             */
            if($this->oUserCurrent) {
                $aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
                if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
            }
            return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
        }
    }
    /**
     * Список топиков из блога
     *
     * @param ModuleBlog_EntityBlog $oBlog	Объект блога
     * @param int $iPage	Номер страницы
     * @param int $iPerPage	Количество элементов на страницу
     * @param string $sShowType	Тип выборки топиков
     * @param string $sPeriod	Период в виде секунд или конкретной даты
     * @return array
     */
    public function GetTopicsByBlog($oBlog,$iPage,$iPerPage,$sShowType='good',$sPeriod=null) {
        if ($sShowType != 'views') {
            return parent::GetTopicsByBlog($oBlog,$iPage,$iPerPage,$sShowType,$sPeriod);
        } else {
            if (is_numeric($sPeriod)) {
                // количество последних секунд
                $sPeriod=date("Y-m-d H:00:00",time()-$sPeriod);
            }
            $bViewstat = (class_exists('PluginViewstat') && in_array('viewstat', $this->Plugin_GetActivePlugins()));
            $aFilter=array(
                'topic_publish' => 1,
                'blog_id' => $oBlog->getId(),
                'viewstat' => $bViewstat,
            );
            if ($sPeriod) {
                $aFilter[($bViewstat && Config::Get('plugin.views.stat_date_filter')) ? 'stat_date_more' : 'topic_date_more'] = $sPeriod;
            }
            $aFilter['order']=array('t.topic_count_read desc','t.topic_id desc');
            return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
        }
    }
    /**
     * Список просматриваемых из всех блогов
     *
     * @param  int    $iPage	Номер страницы
     * @param  int    $iPerPage	Количество элементов на страницу
     * @param  int|string   $sPeriod	Период в виде секунд или конкретной даты
     * @param  bool   $bAddAccessible Указывает на необходимость добавить в выдачу топики,
     *                                из блогов доступных пользователю. При указании false,
     *                                в выдачу будут переданы только топики из общедоступных блогов.
     * @return array
     */
    public function GetTopicsViews($iPage,$iPerPage,$sPeriod=null,$bAddAccessible=true) {
        if (is_numeric($sPeriod)) {
            // количество последних секунд
            $sPeriod=date("Y-m-d H:00:00",time()-$sPeriod);
        }
        $bViewstat = (class_exists('PluginViewstat') && in_array('viewstat', $this->Plugin_GetActivePlugins()));
        $aFilter=array(
            'blog_type' => array(
                'personal',
                'open',
            ),
            'topic_publish' => 1,
            'viewstat' => $bViewstat,
        );
        if ($sPeriod) {
            $aFilter[($bViewstat && Config::Get('plugin.views.stat_date_filter')) ? 'stat_date_more' : 'topic_date_more'] = $sPeriod;
        }
        $aFilter['order']=array('t.topic_count_read desc','t.topic_id desc');
        /**
         * Если пользователь авторизирован, то добавляем в выдачу
         * закрытые блоги в которых он состоит
         */
        if($this->oUserCurrent && $bAddAccessible) {
            $aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
            if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
        }
        return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
    }
    
    public function AddView($sTopicId) {
        if (!$this->oUserCurrent && Config::Get('plugin.views.only_users')) {
            return false;
        }
        if (Config::Get('plugin.views.only_once')) {
            $aTopicIds = array();
            $sTopicIds = $this->Session_Get('views_topic_ids');
            if ($sTopicIds) {
                $aTopicIds = explode(".", $sTopicIds);
                if (is_array($aTopicIds) && in_array($sTopicId, $aTopicIds)) {
                    return false;
                }
            }
            $aTopicIds[] = $sTopicId;
            $this->Session_Set('views_topic_ids', implode(".", $aTopicIds));
        }
        return $this->oMapperTopic->AddView($sTopicId);
    }

    public function GetCountRead($sTopicId) {
        return $this->oMapperTopic->GetCountRead($sTopicId);
    }
}
?>