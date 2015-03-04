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

class PluginViews_ActionPersonalBlog extends PluginViews_Inherit_ActionPersonalBlog
{
    /**
     * Регистрируем необходимые евенты
     *
     */
    protected function RegisterEvent() {
        if (Config::Get('plugin.views.use_sort')) {
            $this->AddEventPreg('/^views$/i','/^(page([1-9]\d{0,5}))?$/i','EventTopics');
        }
        parent::RegisterEvent();
    }

    /**
     * Показ топиков
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
            $this->Viewer_SetHtmlCanonical(Router::GetPath('personal_blog').$sShowType.'/');
        }
        /**
         * Получаем список топиков
         */
        $aResult=$this->Topic_GetTopicsPersonal($iPage,Config::Get('module.topic.per_page'),$sShowType,$sPeriod=='all' ? null : $sPeriod*60*60*24);
        /**
         * Если нет топиков за 1 день, то показываем за неделю (7)
         */
        if (!$aResult['count'] and $iPage==1 and !getRequest('period')) {
            $sPeriod=7;
            $aResult=$this->Topic_GetTopicsPersonal($iPage,Config::Get('module.topic.per_page'),$sShowType,$sPeriod=='all' ? null : $sPeriod*60*60*24);
        }
        $aTopics=$aResult['collection'];
        /**
         * Вызов хуков
         */
        $this->Hook_Run('topics_list_show',array('aTopics'=>$aTopics));
        /**
         * Формируем постраничность
         */
        $aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.topic.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('personal_blog').$sShowType,in_array($sShowType,array('discussed','top')) ? array('period'=>$sPeriod) : array());
        /**
         * Вызов хуков
         */
        $this->Hook_Run('personal_show',array('sShowType'=>$sShowType));
        /**
         * Загружаем переменные в шаблон
         */
        $this->Viewer_Assign('aTopics',$aTopics);
        $this->Viewer_Assign('aPaging',$aPaging);
        $this->Viewer_Assign('sPeriodSelectCurrent',$sPeriod);
        $this->Viewer_Assign('sPeriodSelectRoot',Router::GetPath('personal_blog').$sShowType.'/');
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('index');
    }
}
?>
