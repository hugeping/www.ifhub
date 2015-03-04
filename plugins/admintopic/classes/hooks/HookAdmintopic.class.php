<?php

class PluginAdmintopic_HookAdmintopic extends Hook {

    /*
     * Регистрация событий на хуки
	*/

    public function RegisterHook() {
        $this->AddHook('template_admin_menu_item', 'manageTopicsInject',__CLASS__);
    }

    public function manageTopicsInject(){
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('admintopic').'menu.admin_item.tpl');
    }
	
}
?>
