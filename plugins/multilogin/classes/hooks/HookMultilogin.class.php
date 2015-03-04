<?php
/**
 * MultiLogin - авторизация без сброса cookie
 *
 * Версия:	1.0.1
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_multilogin
 *
 **/

class PluginMultilogin_HookMultilogin extends Hook
{
    public function RegisterHook()
    {
        if ($this->User_IsAuthorization()) {
            $this->AddHook('settings_account_save_before','ChangePassword');
        }
    }

    public function ChangePassword($aParam)
    {
        $oUser = (isset($aParam['oUser']) ? $aParam['oUser'] : null);
        if ($oUser) {
            $oUserCurrent = $this->User_GetUserById($oUser->getId());
            if ($oUserCurrent && $oUserCurrent->getPassword() !== $oUser->getPassword()) {
                $this->User_UpdateSessionKey(true);
            }
        }
    }
}
?>