<?php

/* ---------------------------------------------------------------------------
 * @Plugin Name: OpenIdCmt
 * @Author: Web-studio stfalcon.com
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * ----------------------------------------------------------------------------
 */

class PluginOpenidcmt_ActionOpenidcmt extends ActionPlugin
{

    /**
     * Текущий юзер
     *
     * @var object|null
     */
    protected $oUserCurrent = null;

    /**
     * Инициализация
     */
    public function Init()
    {
        $this->oUserCurrent = $this->User_GetUserCurrent();
        $this->Security_ValidateSendForm();
    }

    /**
     * Регистрируем евенты
     *
     */
    protected function RegisterEvent()
    {
        if (!$this->oUserCurrent) {
            $this->AddEvent('ajaxcheckcomment', 'EventAjaxCheckComment');
        }
    }

    /**
     * Предварительная проверка комментария и разрешения на его отправку
     *
     * @return string
     */
    protected function EventAjaxCheckComment()
    {
        $this->Viewer_SetResponseAjax('json');
        /**
         * Проверям авторизован ли пользователь
         */
        if ($this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('already_registered'), $this->Lang_Get('error'));
            return;
        }
        /**
         * Проверяем текст комментария
         */
        $sText = $this->Text_Parser(getRequest('comment_text'));
        if (!func_check($sText, 'text', 2, 10000)) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_add_text_error'), $this->Lang_Get('error'));
            return;
        }

        /**
         * Проверям на какой коммент отвечаем
         */
        $sParentId = (int) getRequest('reply');
        if (!func_check($sParentId, 'id')) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }

        $aCommentData = array(
            'sText'     => $sText,
            'sParentId' => $sParentId,
            'sTargetId' => getRequest('cmt_target_id'),
        );

        $this->Session_Set('openidcmt_draft_data', serialize($aCommentData));

        $this->Viewer_AssignAjax('bShowLoginForm', true);

        $this->Viewer_AssignAjax('bState', true);
    }
}

?>
