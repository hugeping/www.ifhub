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

class PluginViews_ActionBlog extends PluginViews_Inherit_ActionBlog
{
    /**
     * Инизиализация экшена
     *
     */
    public function Init() {
        parent::Init();
        $this->aBadBlogUrl[] = 'views';
    }

    /**
     * Регистрируем евенты, по сути определяем УРЛы вида /blog/.../
     *
     */
    protected function RegisterEvent() {
        if (Config::Get('plugin.views.use_sort')) {
            $this->AddEventPreg('/^views$/i','/^(page([1-9]\d{0,5}))?$/i',array('EventTopics','topics'));
            $this->AddEventPreg('/^[\w\-\_]+$/i','/^views$/i','/^(page([1-9]\d{0,5}))?$/i',array('EventShowBlog','blog'));
        }
        parent::RegisterEvent();
    }

    /**
     * Показ всех топиков
     *
     */
    protected function EventTopics() {
        $sShowType=$this->sCurrentEvent;
        if (!Config::Get('plugin.views.use_sort') || $sShowType != 'views') {
            return parent::EventTopics();
        }

        $sPeriod=1; // по дефолту 1 день
        if (in_array(getRequestStr('period'),array(1,7,30,'all'))) {
            $sPeriod=getRequestStr('period');
        }
        /**
         * Меню
         */
        $this->sMenuSubItemSelect=$sShowType;
        /**
         * Передан ли номер страницы
         */
        $iPage=$this->GetParamEventMatch(0,2) ? $this->GetParamEventMatch(0,2) : 1;
        if ($iPage==1 and !getRequest('period')) {
            $this->Viewer_SetHtmlCanonical(Router::GetPath('blog').$sShowType.'/');
        }
        /**
         * Получаем список топиков
         */
        $aResult=$this->Topic_GetTopicsCollective($iPage,Config::Get('module.topic.per_page'),$sShowType,$sPeriod=='all' ? null : $sPeriod*60*60*24);
        /**
         * Если нет топиков за 1 день, то показываем за неделю (7)
         */
        if (!$aResult['count'] and $iPage==1 and !getRequest('period')) {
            $sPeriod=7;
            $aResult=$this->Topic_GetTopicsCollective($iPage,Config::Get('module.topic.per_page'),$sShowType,$sPeriod=='all' ? null : $sPeriod*60*60*24);
        }
        $aTopics=$aResult['collection'];
        /**
         * Вызов хуков
         */
        $this->Hook_Run('topics_list_show',array('aTopics'=>$aTopics));
        /**
         * Формируем постраничность
         */
        $aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.topic.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('blog').$sShowType,array('period'=>$sPeriod));
        /**
         * Вызов хуков
         */
        $this->Hook_Run('blog_show',array('sShowType'=>$sShowType));
        /**
         * Загружаем переменные в шаблон
         */
        $this->Viewer_Assign('aTopics',$aTopics);
        $this->Viewer_Assign('aPaging',$aPaging);
        $this->Viewer_Assign('sPeriodSelectCurrent',$sPeriod);
        $this->Viewer_Assign('sPeriodSelectRoot',Router::GetPath('blog').$sShowType.'/');
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('index');
    }

    /**
     * Вывод топиков из определенного блога
     *
     */
    protected function EventShowBlog() {
        $sShowType=$this->GetParamEventMatch(0,0);
        if (!Config::Get('plugin.views.use_sort') || $sShowType != 'views') {
            return parent::EventShowBlog();
        }

        $sPeriod=1; // по дефолту 1 день
        if (in_array(getRequestStr('period'),array(1,7,30,'all'))) {
            $sPeriod=getRequestStr('period');
        }
        $sBlogUrl=$this->sCurrentEvent;
        /**
         * Проверяем есть ли блог с таким УРЛ
         */
        if (!($oBlog=$this->Blog_GetBlogByUrl($sBlogUrl))) {
            return parent::EventNotFound();
        }
        /**
         * Определяем права на отображение закрытого блога
         */
        if($oBlog->getType()=='close'
            and (!$this->oUserCurrent
                or !in_array(
                    $oBlog->getId(),
                    $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent)
                )
            )
        ) {
            $bCloseBlog=true;
        } else {
            $bCloseBlog=false;
        }
        /**
         * Меню
         */
        $this->sMenuSubItemSelect=$sShowType;
        $this->sMenuSubBlogUrl=$oBlog->getUrlFull();
        /**
         * Передан ли номер страницы
         */
        $iPage= $this->GetParamEventMatch(1,2) ? $this->GetParamEventMatch(1,2) : 1;
        if ($iPage==1 and !getRequest('period')) {
            $this->Viewer_SetHtmlCanonical($oBlog->getUrlFull().$sShowType.'/');
        }

        if (!$bCloseBlog) {
            /**
             * Получаем список топиков
             */
            $aResult=$this->Topic_GetTopicsByBlog($oBlog,$iPage,Config::Get('module.topic.per_page'),$sShowType,$sPeriod=='all' ? null : $sPeriod*60*60*24);
            /**
             * Если нет топиков за 1 день, то показываем за неделю (7)
             */
            if (!$aResult['count'] and $iPage==1 and !getRequest('period')) {
                $sPeriod=7;
                $aResult=$this->Topic_GetTopicsByBlog($oBlog,$iPage,Config::Get('module.topic.per_page'),$sShowType,$sPeriod=='all' ? null : $sPeriod*60*60*24);
            }
            $aTopics=$aResult['collection'];
            /**
             * Формируем постраничность
             */
            $aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.topic.per_page'),Config::Get('pagination.pages.count'),$oBlog->getUrlFull().$sShowType,array('period'=>$sPeriod));
            /**
             * Получаем число новых топиков в текущем блоге
             */
            $this->iCountTopicsBlogNew=$this->Topic_GetCountTopicsByBlogNew($oBlog);

            $this->Viewer_Assign('aPaging',$aPaging);
            $this->Viewer_Assign('aTopics',$aTopics);
            $this->Viewer_Assign('sPeriodSelectCurrent',$sPeriod);
            $this->Viewer_Assign('sPeriodSelectRoot',$oBlog->getUrlFull().$sShowType.'/');
        }
        /**
         * Выставляем SEO данные
         */
        $sTextSeo=strip_tags($oBlog->getDescription());
        $this->Viewer_SetHtmlDescription(func_text_words($sTextSeo, Config::Get('seo.description_words_count')));
        /**
         * Получаем список юзеров блога
         */
        $aBlogUsersResult=$this->Blog_GetBlogUsersByBlogId($oBlog->getId(),ModuleBlog::BLOG_USER_ROLE_USER,1,Config::Get('module.blog.users_per_page'));
        $aBlogUsers=$aBlogUsersResult['collection'];
        $aBlogModeratorsResult=$this->Blog_GetBlogUsersByBlogId($oBlog->getId(),ModuleBlog::BLOG_USER_ROLE_MODERATOR);
        $aBlogModerators=$aBlogModeratorsResult['collection'];
        $aBlogAdministratorsResult=$this->Blog_GetBlogUsersByBlogId($oBlog->getId(),ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR);
        $aBlogAdministrators=$aBlogAdministratorsResult['collection'];
        /**
         * Для админов проекта получаем список блогов и передаем их во вьювер
         */
        if($this->oUserCurrent and $this->oUserCurrent->isAdministrator()) {
            $aBlogs = $this->Blog_GetBlogs();
            unset($aBlogs[$oBlog->getId()]);

            $this->Viewer_Assign('aBlogs',$aBlogs);
        }
        /**
         * Вызов хуков
         */
        $this->Hook_Run('blog_collective_show',array('oBlog'=>$oBlog,'sShowType'=>$sShowType));
        /**
         * Загружаем переменные в шаблон
         */
        $this->Viewer_Assign('aBlogUsers',$aBlogUsers);
        $this->Viewer_Assign('aBlogModerators',$aBlogModerators);
        $this->Viewer_Assign('aBlogAdministrators',$aBlogAdministrators);
        $this->Viewer_Assign('iCountBlogUsers',$aBlogUsersResult['count']);
        $this->Viewer_Assign('iCountBlogModerators',$aBlogModeratorsResult['count']);
        $this->Viewer_Assign('iCountBlogAdministrators',$aBlogAdministratorsResult['count']+1);
        $this->Viewer_Assign('oBlog',$oBlog);
        $this->Viewer_Assign('bCloseBlog',$bCloseBlog);
        /**
         * Устанавливаем title страницы
         */
        $this->Viewer_AddHtmlTitle($oBlog->getTitle());
        $this->Viewer_SetHtmlRssAlternate(Router::GetPath('rss').'blog/'.$oBlog->getUrl().'/',$oBlog->getTitle());
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('blog');
    }
}
?>