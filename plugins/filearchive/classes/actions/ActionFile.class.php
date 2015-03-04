<?php
/**
 * File Archive - тип топика "файл"
 *
 * Версия:	1.0.3
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_filearchive
 *
 **/

class PluginFilearchive_ActionFile extends ActionPlugin {
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
    protected $sMenuSubItemSelect='file';
    /**
     * Текущий юзер
     *
     * @var ModuleUser_EntityUser|null
     */
    protected $oUserCurrent=null;

    /**
     * Инициализация
     *
     */
    public function Init() {
        /**
         * Получаем текущего пользователя
         */
        $this->oUserCurrent=$this->User_GetUserCurrent();
        /**
         * Устанавливаем дефолтный евент
         */
        $this->SetDefaultEvent('add');
        /**
         * Устанавливаем title страницы
         */
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.filearchive.topic_file_title'));
    }

    /**
     * Регистрируем евенты
     *
     */
    protected function RegisterEvent() {
        $this->AddEvent('add','EventAdd'); // Добавление топика
        $this->AddEvent('edit','EventEdit'); // Редактирование топика
        $this->AddEvent('go','EventGo');
    }

    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    /**
     * Скачивание файла с подсчетом количества переходов
     *
     */
    protected function EventGo() {
        $bOnlyUsers = Config::Get('plugin.filearchive.only_users');
        $bUseLimit = Config::Get('plugin.filearchive.use_limit');
        $iLimitRating = Config::Get('plugin.filearchive.limit_rating');
        if ($bOnlyUsers && !$this->User_IsAuthorization()) {
            return parent::EventNotFound();
        }
        /**
         * Получаем номер топика из УРЛ и проверяем существует ли он
         */
        $sTopicId=$this->GetParam(0);
        if (!($oTopic=$this->Topic_GetTopicById($sTopicId)) || (!$oTopic->getPublish()) && !($this->User_IsAuthorization() && $this->oUserCurrent->isAdministrator())) {
            return parent::EventNotFound();
        }

        /**
         * Проверяем является ли топик файлом
         */
        if (!$oTopic->isFile()) {
            return parent::EventNotFound();
        }
        if (!$bOnlyUsers || ($bOnlyUsers && $this->oUserCurrent && (!$bUseLimit || ($bUseLimit && ($this->oUserCurrent->getRating()>=$iLimitRating || $this->oUserCurrent->isAdministrator()))))) {
            /**
             * Увеличиваем число скачиваний файла
             */
            $oTopic->setFileDownloads($oTopic->getFileDownloads()+1);
            $this->Topic_UpdateTopic($oTopic);
            /**
             * Скачивание файла
             */
            Router::Location($oTopic->getFileUrl());
        } else {
            return parent::EventNotFound();
        }
    }

    /**
     * Редактирование топика-файла
     *
     */
    protected function EventEdit() {
        /**
         * Проверяем авторизован ли юзер
         */
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
        if (!$oTopic->isFile()) {
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
        $this->Viewer_Assign('bEdit',true);
        $this->Viewer_Assign('aBlogsAllow',$this->Blog_GetBlogsAllowByUser($this->oUserCurrent));
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.filearchive.topic_file_title_edit'));
        /**
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
            $_REQUEST['topic_file']=$oTopic->getFileUrl();
            $_REQUEST['topic_text']=$oTopic->getTextSource();
            $_REQUEST['topic_tags']=$oTopic->getTags();
            $_REQUEST['blog_id']=$oTopic->getBlogId();
            $_REQUEST['topic_id']=$oTopic->getId();
            $_REQUEST['topic_publish_index']=$oTopic->getPublishIndex();
            $_REQUEST['topic_forbid_comment']=$oTopic->getForbidComment();
            $_REQUEST['topic_file_url']=$oTopic->getFileUrl();
            $_REQUEST['topic_file_name']=$oTopic->getFileName();
            $_REQUEST['topic_file_size']=$oTopic->getFileSize();
        }
    }
    /**
     * Добавление топика-файла
     *
     */
    protected function EventAdd() {
        /**
         * Проверяем авторизован ли юзер
         */
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
        $this->Viewer_Assign('bEdit',false);
        $this->Viewer_Assign('aBlogsAllow',$this->Blog_GetBlogsAllowByUser($this->oUserCurrent));
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.filearchive.topic_file_title_create'));
        /**
         * Обрабатываем отправку формы
         */
        return $this->SubmitAdd();
    }

    /**
     * Загрузка файла
     *
     */
    private function UploadTopicFile($aFile) {
        if ($aFile['size'] > Config::Get('plugin.filearchive.max_size')) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.filearchive.topic_file_size_error', array('MAX' => Config::Get('plugin.filearchive.max_size') / 1024)), $this->Lang_Get('error'));
            return false;
        }
        $sExt = pathinfo($aFile['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($sExt), Config::Get('plugin.filearchive.allow_ext'))) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.filearchive.topic_file_type_error', array('TYPES' => implode(', ', Config::Get('plugin.filearchive.allow_ext')))), $this->Lang_Get('error'));
            return false;
        }
        $sDirUpload = Config::Get('plugin.filearchive.uploads_files') . '/' . preg_replace('~(.{2})~U', "\\1/", str_pad($this->oUserCurrent->getId(), 6, "0", STR_PAD_LEFT)) . date('Y/m/d');
        @func_mkdir(Config::Get('path.root.server'), $sDirUpload);
        $sFileShort = $sDirUpload . '/' . func_generator() . ($sExt ? '.' . $sExt : '');
        $sFile = Config::Get('path.root.server') . DIRECTORY_SEPARATOR . $sFileShort;

        if (!move_uploaded_file($aFile['tmp_name'], $sFile)) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
            return false;
        }
        return $sFileShort;
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

        /**
         * Был выбран файл с компьютера и он успешно зугрузился?
         */
        if (!isset($_FILES['topic_file']) || !is_uploaded_file($_FILES['topic_file']['tmp_name'])) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.filearchive.topic_file_create_error'),$this->Lang_Get('error'));
            return false;
        }

        $oTopic=Engine::GetEntity('Topic');
        $oTopic->_setValidateScenario('file');
        /**
         * Заполняем поля для валидации
         */
        $oTopic->setBlogId(getRequestStr('blog_id'));
        $oTopic->setTitle(strip_tags(getRequestStr('topic_title')));
        $oTopic->setTextSource(getRequestStr('topic_text'));
        $oTopic->setTags(getRequestStr('topic_tags'));
        $oTopic->setUserId($this->oUserCurrent->getId());
        $oTopic->setType('file');
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
         */
        $iBlogId=$oTopic->getBlogId();
        if ($iBlogId==0) {
            $oBlog=$this->Blog_GetPersonalBlogByUserId($this->oUserCurrent->getId());
        } else {
            $oBlog=$this->Blog_GetBlogById($iBlogId);
        }
        /**
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
        $sFileShort = $this->UploadTopicFile($_FILES['topic_file']);
        if (!$sFileShort) {
            return false;
        }
        $oTopic->setFilePath($sFileShort);
        $oTopic->setFileName($_FILES['topic_file']['name']);
        $oTopic->setFileDownloads(0);
        $oTopic->setFileSize(@filesize($oTopic->getFilePathFull()));
        /**
         * Теперь можно смело добавлять топик к блогу
         */
        $oTopic->setBlogId($oBlog->getId());
        /**
         * Получаемый и устанавливаем разрезанный текст по тегу <cut>
         */
        list($sTextShort,$sTextNew,$sTextCut) = $this->Text_Cut($oTopic->getTextSource());
        $oTopic->setCutText($sTextCut);
        $oTopic->setText($this->Text_Parser($sTextNew));
        $oTopic->setTextShort($this->Text_Parser($sTextShort));
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
        /**
         * Запускаем выполнение хуков
         */
        $this->Hook_Run('topic_add_before', array('oTopic'=>$oTopic,'oBlog'=>$oBlog));
        /**
         * Добавляем топик
         */
        if ($this->Topic_AddTopic($oTopic)) {
            $this->Hook_Run('topic_add_after', array('oTopic'=>$oTopic,'oBlog'=>$oBlog));
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
        $oTopic->_setValidateScenario('file');
        /**
         * Сохраняем старое значение идентификатора блога
         */
        $sBlogIdOld = $oTopic->getBlogId();
        /**
         * Заполняем поля для валидации
         */
        $oTopic->setBlogId(getRequestStr('blog_id'));
        $oTopic->setTitle(strip_tags(getRequestStr('topic_title')));
        $oTopic->setTextSource(getRequestStr('topic_text'));
        $oTopic->setTags(getRequestStr('topic_tags'));
        $oTopic->setUserIp(func_getIp());
        /**
         * Проверка корректности полей формы
         */
        if (!$this->checkTopicFields($oTopic)) {
            return false;
        }
        /**
         * Определяем в какой блог делаем запись
         */
        $iBlogId=$oTopic->getBlogId();
        if ($iBlogId==0) {
            $oBlog=$this->Blog_GetPersonalBlogByUserId($oTopic->getUserId());
        } else {
            $oBlog=$this->Blog_GetBlogById($iBlogId);
        }
        /**
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
        /**
         * Получаемый и устанавливаем разрезанный текст по тегу <cut>
         */
        list($sTextShort,$sTextNew,$sTextCut) = $this->Text_Cut($oTopic->getTextSource());
        $oTopic->setCutText($sTextCut);
        $oTopic->setText($this->Text_Parser($sTextNew));
        $oTopic->setTextShort($this->Text_Parser($sTextShort));
        /**
         * Был выбран файл с компьютера и он успешно зугрузился?
         */
        if (isset($_FILES['topic_file']) && is_uploaded_file($_FILES['topic_file']['tmp_name'])) {
            $sFileShort = $this->UploadTopicFile($_FILES['topic_file']);
            if ($sFileShort) {
                $sOldFile = $oTopic->getFilePathFull();
                $oTopic->setFilePath($sFileShort);
                $oTopic->setFileName($_FILES['topic_file']['name']);
                $oTopic->setFileDownloads(0);
                $oTopic->setFileSize(@filesize($oTopic->getFilePathFull()));
                @unlink($sOldFile);
            } else {
                $_REQUEST['topic_file_url']=$oTopic->getFileUrl();
                $_REQUEST['topic_file_name']=$oTopic->getFileName();
                $_REQUEST['topic_file_size']=$oTopic->getFileSize();
                return false;
            }
        }

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
        $this->Hook_Run('topic_edit_before', array('oTopic'=>$oTopic,'oBlog'=>$oBlog));
        /**
         * Сохраняем топик
         */
        if ($this->Topic_UpdateTopic($oTopic)) {
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
        $this->Hook_Run('check_file_fields', array('bOk'=>&$bOk));
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