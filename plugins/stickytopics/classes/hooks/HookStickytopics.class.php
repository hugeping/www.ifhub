<?php
/*-------------------------------------------------------
*
*   StickyTopics v2.
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail:kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

class PluginStickytopics_HookStickytopics extends Hook
{

    public function RegisterHook()
    {
        $oUserCurrent=$this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return;
        
        $this->AddHook('template_menu_blog_edit_admin_item', 'AddBlogEditMenu');
        $this->AddHook('template_menu_profile_created_item', 'AddPersonalEditMenu');
        $this->AddHook('template_admin_action_item', 'AddAdminEditMenu');
        $this->AddHook('template_st_assign_filepath', 'AssignFilePath');
        $this->AddHook('template_st_assign_webpath', 'AssignWebPath');
    }

    public function AddBlogEditMenu($aParams)
    {
        $res=$this->Viewer_Fetch($this->PluginStickytopics_Stickytopics_GetTemplateFilePath(__CLASS__, 'blog_edit_menu.tpl'));
        return $res;
    }

    public function AddPersonalEditMenu($aParams)
    {
        if (!$oUserCurrent=$this->User_GetUserCurrent())
            return;
        
        if ($aParams['oUserProfile']->getId() != $oUserCurrent->getId())
            return;
        
        if (!$this->ACL_CanStickTopic($aParams['oUserProfile'], 'personal'))
            return;
        
        $res=$this->Viewer_Fetch($this->PluginStickytopics_Stickytopics_GetTemplateFilePath(__CLASS__, 'personal_edit_menu.tpl'));
        return $res;
    }

    public function AddAdminEditMenu($aParams)
    {
        $res=$this->Viewer_Fetch($this->PluginStickytopics_Stickytopics_GetTemplateFilePath(__CLASS__, 'admin_edit_menu.tpl'));
        return $res;
    }

    public function AssignFilePath($aParams)
    {
        return $this->PluginStickytopics_Stickytopics_GetTemplateFilePath(__CLASS__, $aParams['sFilename']);
    }

    public function AssignWebPath($aParams)
    {
        return $this->PluginStickytopics_Stickytopics_GetTemplateFileWebPath(__CLASS__, $aParams['sFilename']);
    }

}
?>
