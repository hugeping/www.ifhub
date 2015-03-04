<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * Регистрация хуков
 *
 */
class PluginOpenid_HookLogin extends Hook {    

    public function RegisterHook() {
    	/**
    	 * Хук на инициализацию экшенов
    	 */
        $this->AddHook('init_action', 'InitAction', __CLASS__);
        /**
         * Хук на разлогинивание пользователя
         */
        $this->AddHook('module_user_logout_after', 'Logout', __CLASS__);
        /**
         * Хук на страницу авторизации
         */
        $this->AddHook('template_form_login_begin', 'LoginTpl', __CLASS__);
        /**
         * Хук на всплывающее окно авторизации
         */
        $this->AddHook('template_form_login_popup_begin', 'LoginTpl', __CLASS__);
        /**
         * Хук на страницу регистрации
         */
        $this->AddHook('template_form_registration_begin', 'LoginTpl', __CLASS__);
        /**
         * Хук на меню настроек пользователя
         */
        $this->AddHook('template_menu_settings_settings_item', 'MenuSettingsTpl', __CLASS__);
    }

    /**
     * Отлавливаем нужные экшены и перенаправляем на экшены плагина
     *
     */
    public function InitAction() {    	
		/**
		 * Подхватываем обработку URL вида /login/openid/
		 */
    	if (Router::GetAction()=='login' and Router::GetActionEvent()=='openid') {
    		Router::Action('openid_login','login');
    	}
    	/**
		 * Подхватываем обработку URL вида /settings/openid/
		 */
    	if (Router::GetAction()=='settings' and Router::GetActionEvent()=='openid') {
    		Router::Action('openid_settings','settings');
    	}
    }
    /**
     * Затираем куку ВКонтакте
     *
     */
    public function Logout() {
    	setcookie('vk_app_'.Config::Get('plugin.openid.vk.id'),'',1,Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
    }
    /**
     * Вставляем кнопку OpenID на форму авторизации
     *
     * @return unknown
     */
    public function LoginTpl() {    	
		return $this->Viewer_Fetch(Plugin::GetTemplatePath('openid').'inject_login.tpl');
    }
    /**
     * Добавляем в меню настроек новый пункт
     *
     * @return unknown
     */
    public function MenuSettingsTpl() {    	
    	return $this->Viewer_Fetch(Plugin::GetTemplatePath('openid').'menu.setting.item.tpl');
    }
}
?>