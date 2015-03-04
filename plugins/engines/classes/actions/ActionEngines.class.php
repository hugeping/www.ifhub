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

class PluginEngines_ActionEngines extends ActionPlugin
{

    /**
     * Главное меню
     *
     * @var string
     */
    protected $sMenuHeadItemSelect='blog';
    /**
     * Меню
     *
     * @var string
     */
    protected $sMenuItemSelect='topic';
    /**
     * СубМеню
     *
     * @var string
     */
    protected $sMenuSubItemSelect='engines';
    /**
     * Текущий юзер
     *
     * @var ModuleUser_EntityUser|null
     */
    protected $oUserCurrent=null;

    public function Init()
    {
        /**
         * Проверяем авторизован ли юзер
         */
        $this->oUserCurrent=$this->User_GetUserCurrent();
        $this->SetDefaultEvent('engines');
    }
    /**
     * Регистрируем евенты
     *
     */
    protected function RegisterEvent() {
        $this->AddEvent('add','EventAdd');
        $this->AddEvent('edit','EventEdit');
        $this->AddEvent('engines','EventEnginesList');
        $this->AddEventPreg('/^(page([1-9]\d{0,5}))?$/i', 'EventEnginesList');
    }


    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */
    protected function EventEnginesList()
    {
        /**
        * Меню
        */
        $this->sMenuSubItemSelect = 'newall';
        $this->sMenuItemSelect='engines';
        /**
        * Передан ли номер страницы
        */
        $iPage = $this->GetEventMatch(2) ? $this->GetEventMatch(2) : 1;
        /**
        * Устанавливаем основной URL для поисковиков
        */
        if ($iPage == 1) {
            $this->Viewer_SetHtmlCanonical(Config::Get('path.root.web') . '/');
        }
        $aFilter = array(
            'topic_publish' => 1,
            'topic_type' => array('engines')
        );

        /**
        * Получаем список топиков
        */
        $aResult = $this->Topic_GetTopicsByFilter($aFilter, $iPage, Config::Get('module.topic.per_page'));
        $aTopics = $aResult['collection'];
        /**
        * Вызов хуков
        */
        $this->Hook_Run('topics_list_show', array('aTopics' => $aTopics));
        /**
        * Формируем постраничность
        */
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('module.topic.per_page'), Config::Get('pagination.pages.count'), Router::GetPath('engines'));
        /**
        * Загружаем переменные в шаблон
        */
        $this->Viewer_Assign('aTopics', $aTopics);
        $this->Viewer_Assign('aPaging', $aPaging);
        /**
        * Устанавливаем шаблон вывода
        */
        $this->SetTemplateAction('index');
    }
    /**
     * Редактирование топика
     *
     */
    protected function EventEdit() {
        if (!$this->User_IsAuthorization()) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
            return Router::Action('error');
        }
                /**
         * Получаем номер топика из УРЛ и проверяем существует ли он
         */
        $sTopicId=$this->GetParam(0);
        if (!($oTopic=$this->Topic_GetTopicById($sTopicId))) {
            return parent::EventNotFound();
        }
        /**
         * Проверяем тип топика
         */
        if ($oTopic->getType()!='engines') {
            return parent::EventNotFound();
        }
        /**
         * Если права на редактирование
         */
        if (!$this->ACL_IsAllowEditTopic($oTopic,$this->oUserCurrent)) {
            return parent::EventNotFound();
        }
        /**
         * Вызов хуков
         */
        $this->Hook_Run('topic_edit_show',array('oTopic'=>$oTopic));
/**
         * Загружаем переменные в шаблон
         */
        $this->Viewer_Assign('aBlogsAllow',$this->Blog_GetBlogsAllowByUser($this->oUserCurrent));        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('add');
        /**
         * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
         */
        if (isset($_REQUEST['submit_topic_publish']) or isset($_REQUEST['submit_topic_save'])) {
            /**
             * Обрабатываем отправку формы
             */
            return $this->SubmitEdit($oTopic);
        } else {
            /**
             * Заполняем поля формы для редактирования
             * Только перед отправкой формы!
             */
            $_REQUEST['topic_title']=$oTopic->getTitle();
            $_REQUEST['topic_text']=$oTopic->getTextSource();
            $_REQUEST['topic_tags']=$oTopic->getTags();
            $_REQUEST['blog_id']=$oTopic->getBlogId();
            $_REQUEST['topic_id']=$oTopic->getId();
            $_REQUEST['topic_publish_index']=$oTopic->getPublishIndex();
            $_REQUEST['topic_forbid_comment']=$oTopic->getForbidComment();
                $_REQUEST['topic_field_link1']=$oTopic->getFieldLink1();
                    $_REQUEST['topic_field_string1']=$oTopic->getFieldString1();
            }
    }
    /**
     * Добавление топика
     *
     */
    protected function EventAdd() {
        if (!$this->User_IsAuthorization()) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
            return Router::Action('error');
        }
        /**
         * Вызов хуков
         */
        $this->Hook_Run('topic_add_show');
        /**
         * Загружаем переменные в шаблон
         */
        $this->Viewer_Assign('aBlogsAllow',$this->Blog_GetBlogsAllowByUser($this->oUserCurrent));
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitAdd();
    }
    /**
     * Обработка добавлени топика
     *
     * @return mixed
     */
    protected function SubmitAdd() {
        /**
         * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
         */
        if (!isPost('submit_topic_publish') and !isPost('submit_topic_save')) {
            return false;
        }
        $oTopic=Engine::GetEntity('Topic');
        $oTopic->_setValidateScenario('engines');
        /**
         * Заполняем поля для валидации
         */
        $oTopic->setBlogId(getRequest('blog_id'));

        $oTopic->setTitle(strip_tags(getRequest('topic_title')));

        $oTopic->setTextSource(getRequest('topic_text'));
        $oTopic->setTags(getRequest('topic_tags'));

        $oTopic->setUserId($this->oUserCurrent->getId());
        $oTopic->setType('engines');
        $oTopic->setDateAdd(date("Y-m-d H:i:s"));
        $oTopic->setUserIp(func_getIp());
        /**
         * Проверка корректности полей формы
         */
        if (!$this->checkTopicFields($oTopic)) {
            return false;
        }
        /**
         * Определяем в какой блог делаем запись
         */        $iBlogId=$oTopic->getBlogId();
        if ($iBlogId==0) {
            $oBlog=$this->Blog_GetPersonalBlogByUserId($this->oUserCurrent->getId());
        } else {
            $oBlog=$this->Blog_GetBlogById($iBlogId);
        }        /**
         * Если блог не определен выдаем предупреждение
         */
        if (!$oBlog) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_create_blog_error_unknown'),$this->Lang_Get('error'));
            return false;
        }
        /**
         * Проверяем права на постинг в блог
         */
        if (!$this->ACL_IsAllowBlog($oBlog,$this->oUserCurrent)) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_create_blog_error_noallow'),$this->Lang_Get('error'));
            return false;
        }
        /**
         * Проверяем разрешено ли постить топик по времени
         */
        if (isPost('submit_topic_publish') and !$this->ACL_CanPostTopicTime($this->oUserCurrent)) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_time_limit'),$this->Lang_Get('error'));
            return;
        }
        /**
         * Теперь можно смело добавлять топик к блогу
         */
        $oTopic->setBlogId($oBlog->getId());
        $oTopic->setText($this->Text_Parser($oTopic->getTextSource()));
        $oTopic->setTextShort($oTopic->getText());
        $oTopic->setCutText(null);

        /**
         * Публикуем или сохраняем
         */
        if (isset($_REQUEST['submit_topic_publish'])) {
            $oTopic->setPublish(1);
            $oTopic->setPublishDraft(1);
        } else {
            $oTopic->setPublish(0);
            $oTopic->setPublishDraft(0);
        }
        /**
         * Принудительный вывод на главную
         */
        $oTopic->setPublishIndex(0);
        if ($this->ACL_IsAllowPublishIndex($this->oUserCurrent))	{
            if (getRequest('topic_publish_index')) {
                $oTopic->setPublishIndex(1);
            }
        }
        /**
         * Запрет на комментарии к топику
         */
        $oTopic->setForbidComment(0);
        if (getRequest('topic_forbid_comment')) {
            $oTopic->setForbidComment(1);
        }
                                        $oTopic->setFieldLink1(getRequest('topic_field_link1'));
                                                        $oTopic->setFieldString1(getRequest('topic_field_string1'));
                            /**
         * Запускаем выполнение хуков
         */
        $this->Hook_Run('topic_add_before', array('oTopic'=>$oTopic,'oBlog'=>$oBlog));
        /**
         * Добавляем топик
         */
        if ($this->Topic_AddTopic($oTopic)) {
            $this->Hook_Run('topic_add_after', array('oTopic'=>$oTopic,'oBlog'=>$oBlog));
            $this->Topic_UpdateTopicFieldsEngines($oTopic);
            /**
             * Получаем топик, чтоб подцепить связанные данные
             */
            $oTopic=$this->Topic_GetTopicById($oTopic->getId());
            /**
             * Обновляем количество топиков в блоге
             */
            $this->Blog_RecalculateCountTopicByBlogId($oTopic->getBlogId());
            /**
             * Добавляем автора топика в подписчики на новые комментарии к этому топику
             */
            $this->Subscribe_AddSubscribeSimple('topic_new_comment',$oTopic->getId(),$this->oUserCurrent->getMail());
            //Делаем рассылку спама всем, кто состоит в этом блоге
            if ($oTopic->getPublish()==1 and $oBlog->getType()!='personal') {
                $this->Topic_SendNotifyTopicNew($oBlog,$oTopic,$this->oUserCurrent);
            }
            /**
             * Добавляем событие в ленту
             */
            $this->Stream_write($oTopic->getUserId(), 'add_topic', $oTopic->getId(),$oTopic->getPublish() && $oBlog->getType()!='close');
            Router::Location($oTopic->getUrl());
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'));
            return Router::Action('error');
        }
    }
    /**
     * Обработка редактирования топика
     *
     * @param ModuleTopic_EntityTopic $oTopic
     * @return mixed
     */
    protected function SubmitEdit($oTopic) {
        $oTopic->_setValidateScenario('engines');
        /**
         * Сохраняем старое значение идентификатора блога
         */
        $sBlogIdOld = $oTopic->getBlogId();
        /**
         * Заполняем поля для валидации
         */
        $oTopic->setBlogId(getRequest('blog_id'));
        $oTopic->setTitle(strip_tags(getRequest('topic_title')));

        $oTopic->setTextSource(getRequest('topic_text'));
        $oTopic->setTags(getRequest('topic_tags'));
        $oTopic->setUserIp(func_getIp());
        /**
         * Проверка корректности полей формы
         */
        if (!$this->checkTopicFields($oTopic)) {
            return false;
        }
        /**
         * Определяем в какой блог делаем запись
         */        $iBlogId=$oTopic->getBlogId();
        if ($iBlogId==0) {
            $oBlog=$this->Blog_GetPersonalBlogByUserId($this->oUserCurrent->getId());
        } else {
            $oBlog=$this->Blog_GetBlogById($iBlogId);
        }        /**
         * Если блог не определен выдаем предупреждение
         */
        if (!$oBlog) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_create_blog_error_unknown'),$this->Lang_Get('error'));
            return false;
        }
        /**
         * Проверяем права на постинг в блог
         */
        if (!$this->ACL_IsAllowBlog($oBlog,$this->oUserCurrent)) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_create_blog_error_noallow'),$this->Lang_Get('error'));
            return false;
        }
        /**
         * Проверяем разрешено ли постить топик по времени
         */
        if (isPost('submit_topic_publish') and !$oTopic->getPublishDraft() and !$this->ACL_CanPostTopicTime($this->oUserCurrent)) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_time_limit'),$this->Lang_Get('error'));
            return;
        }

        /**
         * Теперь можно смело редактировать топик
         */
        $oTopic->setBlogId($oBlog->getId());
        $oTopic->setText($this->Text_Parser($oTopic->getTextSource()));
        $oTopic->setTextShort($oTopic->getText());
        /**
         * Публикуем или сохраняем в черновиках
         */
        $bSendNotify=false;
        if (isset($_REQUEST['submit_topic_publish'])) {
            $oTopic->setPublish(1);
            if ($oTopic->getPublishDraft()==0) {
                $oTopic->setPublishDraft(1);
                $oTopic->setDateAdd(date("Y-m-d H:i:s"));
                $bSendNotify=true;
            }
        } else {
            $oTopic->setPublish(0);
        }
        /**
         * Принудительный вывод на главную
         */
        if ($this->ACL_IsAllowPublishIndex($this->oUserCurrent))	{
            if (getRequest('topic_publish_index')) {
                $oTopic->setPublishIndex(1);
            } else {
                $oTopic->setPublishIndex(0);
            }
        }
        /**
         * Запрет на комментарии к топику
         */
        $oTopic->setForbidComment(0);
        if (getRequest('topic_forbid_comment')) {
            $oTopic->setForbidComment(1);
        }
                    $oTopic->setFieldLink1(getRequest('topic_field_link1'));
                        $oTopic->setFieldString1(getRequest('topic_field_string1'));
                $this->Hook_Run('topic_edit_before', array('oTopic'=>$oTopic,'oBlog'=>$oBlog));
        /**
         * Сохраняем топик
         */
        if ($this->Topic_UpdateTopic($oTopic)) {
            $this->Topic_UpdateTopicFieldsEngines($oTopic);
            $this->Hook_Run('topic_edit_after', array('oTopic'=>$oTopic,'oBlog'=>$oBlog,'bSendNotify'=>&$bSendNotify));
            /**
             * Обновляем данные в комментариях, если топик был перенесен в новый блог
             */
            if($sBlogIdOld!=$oTopic->getBlogId()) {
                $this->Comment_UpdateTargetParentByTargetId($oTopic->getBlogId(), 'topic', $oTopic->getId());
                $this->Comment_UpdateTargetParentByTargetIdOnline($oTopic->getBlogId(), 'topic', $oTopic->getId());
            }
            /**
             * Обновляем количество топиков в блоге
             */
            if ($sBlogIdOld!=$oTopic->getBlogId()) {
                $this->Blog_RecalculateCountTopicByBlogId($sBlogIdOld);
            }
            $this->Blog_RecalculateCountTopicByBlogId($oTopic->getBlogId());
            /**
             * Добавляем событие в ленту
             */
            $this->Stream_write($oTopic->getUserId(), 'add_topic', $oTopic->getId(),$oTopic->getPublish() && $oBlog->getType()!='close');
            /**
             * Рассылаем о новом топике подписчикам блога
             */
            if ($bSendNotify)	 {
                $this->Topic_SendNotifyTopicNew($oBlog,$oTopic,$this->oUserCurrent);
            }
            if (!$oTopic->getPublish() and !$this->oUserCurrent->isAdministrator() and $this->oUserCurrent->getId()!=$oTopic->getUserId()) {
                Router::Location($oBlog->getUrlFull());
            }
            Router::Location($oTopic->getUrl());
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'));
            return Router::Action('error');
        }
    }
    /**
     * Проверка полей формы
     *
     * @param ModuleTopic_EntityTopic $oTopic
     * @return bool
     */
    protected function checkTopicFields($oTopic) {
        $this->Security_ValidateSendForm();

        $bOk=true;
        if (!$oTopic->_Validate()) {
            $this->Message_AddError($oTopic->_getValidateError(),$this->Lang_Get('error'));
            $bOk=false;
        }








        /**
         * Выполнение хуков
         */
        $this->Hook_Run('check_engines_fields', array('bOk'=>&$bOk));

        return $bOk;
    }
    /**
     * При завершении экшена загружаем необходимые переменные
     *
     */
    public function EventShutdown() {
        $this->Viewer_Assign('sMenuHeadItemSelect',$this->sMenuHeadItemSelect);
        $this->Viewer_Assign('sMenuItemSelect',$this->sMenuItemSelect);
        $this->Viewer_Assign('sMenuSubItemSelect',$this->sMenuSubItemSelect);
    }
}

?>