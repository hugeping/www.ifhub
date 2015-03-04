<?php
class PluginOpenidcmt_ModuleUser extends PluginOpenidcmt_Inherit_ModuleUser
{
    /**
     * Переопределенный метод авторизации, для проверки, писал ли пользователь коментарии перед авторизацией ести да,
     * то запустить функцию публикации коментария
     *
     * @param ModuleUser_EntityUser $oUser
     * @param bool $bRemember
     * @param null $sKey
     * @return bool
     */
    public function Authorization(ModuleUser_EntityUser $oUser,$bRemember=true,$sKey=null)
    {
        if (!parent::Authorization($oUser, $bRemember, $sKey)){
            return false;
        }

        $this->PostDraftCommentAfter($oUser);

        return true;
    }

    /**
     * Публикация комментария
     *
     * @param ModuleUser_EntityUser $oCurrentUser
     */
    private function PostDraftCommentAfter($oCurrentUser)
    {
        // Get previous comment data
        $aCommentData = (array) unserialize($this->Session_Get('openidcmt_draft_data'));

        if (isset($aCommentData['sTargetId'])) {
            /**
             * Проверяем топик
             */
            if (!($oTopic = $this->Topic_GetTopicById($aCommentData['sTargetId']))) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return;
            }

            /**
             * Проверяем запрет на добавления коммента автором топика
             */
            if ($oTopic->getForbidComment()) {
                $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_notallow'), $this->Lang_Get('error'));
                return;
            }

            $oCommentParent = null;

            $sText = isset($aCommentData['sText']) ? $aCommentData['sText'] : '';

            $sParentId = isset($aCommentData['sParentId']) ? (int) $aCommentData['sParentId'] : 0;

            if ($sParentId != 0) {
                /**
                 * Проверяем существует ли комментарий на который отвечаем
                 */
                if (!($oCommentParent = $this->Comment_GetCommentById($sParentId))) {
                    $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                    return;
                }
                /**
                 * Проверяем из одного топика ли новый коммент и тот на который отвечаем
                 */
                if ($oCommentParent->getTargetId() != $oTopic->getId()) {
                    $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                    return;
                }
            } else {
                /**
                 * Корневой комментарий
                 */
                $sParentId = null;
            }

            $oCommentNew = Engine::GetEntity('Comment');
            $oCommentNew->setTargetId($oTopic->getId());
            $oCommentNew->setTargetType('topic');
            $oCommentNew->setTargetParentId($oTopic->getBlog()->getId());
            $oCommentNew->setUserId($oCurrentUser->getId());
            $oCommentNew->setText($sText);
            $oCommentNew->setDate(date("Y-m-d H:i:s"));
            $oCommentNew->setUserIp(func_getIp());
            $oCommentNew->setPid($sParentId);
            $oCommentNew->setTextHash(md5($sText));
            $oCommentNew->setPublish($oTopic->getPublish());

            $sReturnUrl = $oTopic->getUrl();
            $this->Hook_Run('comment_add_before', array(
                'oCommentNew'    => $oCommentNew,
                'oCommentParent' => $oCommentParent,
                'oTopic'         => $oTopic
            ));
            /**
             * Добавляем коммент
             */
            if ($this->Comment_AddComment($oCommentNew)) {
                // Удаляем из сессии данные о черновике комментария
                $this->Session_Drop('openidcmt_draft_data');

                $this->Hook_Run('comment_add_after', array(
                    'oCommentNew'    => $oCommentNew,
                    'oCommentParent' => $oCommentParent,
                    'oTopic'         => $oTopic
                ));

                $this->Viewer_AssignAjax('sCommentId', $oCommentNew->getId());
                if ($oTopic->getPublish()) {
                    /**
                     * Добавляем коммент в прямой эфир если топик не в черновиках
                     */
                    $oCommentOnline = Engine::GetEntity('Comment_CommentOnline');
                    $oCommentOnline->setTargetId($oCommentNew->getTargetId());
                    $oCommentOnline->setTargetType($oCommentNew->getTargetType());
                    $oCommentOnline->setTargetParentId($oCommentNew->getTargetParentId());
                    $oCommentOnline->setCommentId($oCommentNew->getId());

                    $this->Comment_AddCommentOnline($oCommentOnline);
                }
                /**
                 * Сохраняем дату последнего коммента для юзера
                 */
                $oCurrentUser->setDateCommentLast(date("Y-m-d H:i:s"));
                $this->User_Update($oCurrentUser);
                /**
                 * Отправка уведомления автору топика
                 */
                $oUserTopic = $oTopic->getUser();
                if ($oCommentNew->getUserId() != $oUserTopic->getId()) {
                    $this->Notify_SendCommentNewToAuthorTopic($oUserTopic, $oTopic, $oCommentNew, $oCurrentUser);
                }
                /**
                 * Отправляем уведомление тому на чей коммент ответили
                 */
                if ($oCommentParent and $oCommentParent->getUserId() != $oTopic->getUserId() and $oCommentNew->getUserId() != $oCommentParent->getUserId()) {
                    $oUserAuthorComment = $oCommentParent->getUser();
                    $this->Notify_SendCommentReplyToAuthorParentComment($oUserAuthorComment, $oTopic, $oCommentNew, $oCurrentUser);
                }
                $this->Message_AddNoticeSingle($this->Lang_Get('plugin.openidcmt.opencmtid_comment_send'), $this->Lang_Get('attention'), true);

                /**
                 * Добавляем событие в ленту
                 */
                $this->Stream_write($oCommentNew->getUserId(), 'add_comment', $oCommentNew->getId(),
                    $oTopic->getPublish() && $oTopic->getBlog()->getType() != 'close');
            }
            else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            }
        }
    }
}