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

class PluginEngines_HookEngines extends Hook
{
    public function RegisterHook()
    {
        $this->AddHook('template_menu_create_topic_item', 'AddMenuCreateEngines');
        $this->AddHook('template_menu_blog', 'AddMenuBlogEngines');
    }
    public function AddMenuCreateEngines()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('engines') . 'menu.create_topic_item.tpl');
    }

    public function AddMenuBlogEngines()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('engines') . 'menu.blog.tpl');
    }
}

?>