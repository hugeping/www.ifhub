var ls = ls || {};

/**
 * Обработка комментариев для незарегистрированных пользователей
 */
ls.comments.add = function (formObj, targetId, targetType) {

    if (this.options.wysiwyg) {
        $('#' + formObj + ' textarea').val(tinyMCE.activeEditor.getContent());
    }
    formObj = $('#' + formObj);

    ls.ajax(aRouter['openidcmt']+'ajaxcheckcomment', formObj.serializeJSON(), function (result) {
        $('#comment-button-submit').removeAttr('disabled');
        if (!result) {
            this.enableFormComment();
            ls.msg.error('Error', 'Please try again later');
            return;
        }
        if (result.bStateError) {
            this.enableFormComment();
            ls.msg.error(null, result.sMsg);
        } else {
            // Если пользователь не залогинен, показываем форму авторизации
            if (result.bShowLoginForm) {
                ls.msg.notice(null, 'Comment is added');
                $('#reply').hide();
                $('#window_login_form').jqmShow();
            }
        }
    }.bind(this));
}