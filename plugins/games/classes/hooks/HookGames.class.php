<?php
/* -------------------------------------------------------
*
*   LiveStreet (v1.x)
*   Plugin Games (v.0.1)
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

class PluginGames_HookGames extends Hook
{
    public function RegisterHook()
    {
        $this->AddHook('template_menu_create_topic_item', 'AddMenuCreateGames');
        $this->AddHook('template_menu_blog', 'AddMenuBlogGames');
    }
    public function AddMenuCreateGames()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('games') . 'menu.create_topic_item.tpl');
    }

    public function AddMenuBlogGames()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('games') . 'menu.blog.tpl');
    }
}

?>