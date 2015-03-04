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

class PluginStickytopics_ActionProfile extends PluginStickytopics_Inherit_ActionProfile
{

    protected function RegisterEvent()
    {
        $this->AddEventPreg('/^.+$/i', '/^created/i', '/^sticky/i', '/^$/i', 'EventStickyTopics');
        parent::RegisterEvent();
    }

    protected function EventStickyTopics()
    {
        if (!$this->CheckUserProfile())
        {
            return parent::EventNotFound();
        }
        /**
		 * Меню
		 */
        $this->sMenuSubItemSelect='sticky';
        /**
		 * Проверяем передан ли в УРЛе номер блога
		 */
        /**
		 * Проверям авторизован ли пользователь
		 */
        if (!$this->User_IsAuthorization())
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return Router::Action('error');
        }
        if (!$oBlog=$this->Blog_GetPersonalBlogByUserId($this->oUserCurrent->getId()))
        {
            return parent::EventNotFound();
        }
        /**
		 * Проверка на право редактировать блог
		 */
        if (!$this->ACL_IsAllowEditBlog($oBlog, $this->oUserCurrent))
        {
            return parent::EventNotFound();
        }
        
        if (!$this->ACL_CanStickTopic($this->oUserCurrent, 'personal', $oBlog))
        {
            return parent::EventNotFound();
        }
        
        $this->Viewer_Assign('oBlogEdit', $oBlog);
        
        $aStickyTopics=$this->PluginStickytopics_Stickytopics_GetStickyTopicItemsByTargetTypeAndTargetId('personal', $oBlog->getId(), array('#order'=>array('topic_order'=>'asc')));
        
        $aA=array();
        foreach ($aStickyTopics as $oStickyTopic)
        {
            $aA[]=$oStickyTopic->getTopicId();
        }
        
        $this->Viewer_Assign('sTargetType', 'personal');
        $this->Viewer_Assign('iTargetId', $oBlog->getId());
        
        $this->Viewer_Assign('aTopic', $this->Topic_GetTopicsAdditionalData($aA));
        
        $this->Viewer_AppendStyle($this->PluginStickytopics_Stickytopics_GetTemplateFileWebPath(__CLASS__, 'css/style.css'));
        $this->Viewer_AppendScript($this->PluginStickytopics_Stickytopics_GetTemplateFileWebPath(__CLASS__, 'js/stickytopics.js'));
        
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.stickytopics.edit_personal_sticky'));
        
        $this->SetTemplate($this->PluginStickytopics_Stickytopics_GetTemplateFilePath(__CLASS__, 'actions/ActionProfile/sticky.tpl'));
    }

    protected function EventCreatedTopics()
    {
        if ($res=parent::EventCreatedTopics())
            return $res;
        
        if (!$this->oUserProfile)
            return;
        
        if (!$this->oUserCurrent)
            return;
        
        if (!$this->ACL_CanStickTopic($this->oUserProfile, 'personal'))
        {
            return;
        }
        
        if ($this->GetParamEventMatch(1, 0) == 'topics')
        {
            $iPage=$this->GetParamEventMatch(2, 2)?$this->GetParamEventMatch(2, 2):1;
        }
        else
        {
            $iPage=$this->GetParamEventMatch(1, 2)?$this->GetParamEventMatch(1, 2):1;
        }
        
        if ($iPage != 1)
            return;
        
        $oPersBlog=$this->Blog_GetPersonalBlogByUserId($this->oUserCurrent->getId());
        if (!$oPersBlog)
            return;
        
        $aStickyTopics=$this->PluginStickytopics_Stickytopics_GetStickyTopicItemsByTargetTypeAndTargetId('personal', $oPersBlog->getId(), array('#order'=>array('topic_order'=>'asc')));
        
        $aA=array();
        foreach ($aStickyTopics as $oStickyTopic)
        {
            $aA[]=$oStickyTopic->getTopicId();
        }
        
        $aStickyTopics=$this->Topic_GetTopicsAdditionalData($aA);
        $oUser=$this->User_GetUserCurrent();
        foreach ($aStickyTopics as $key=>$oTopic)
        {
            if (!$this->ACL_CanViewTopic($oUser,$oTopic))
                unset($aStickyTopics[$key]);
            else
                $aStickyTopics[$key]->bStickyTopic=true;
        }
        $this->Viewer_Assign('aStickyTopics', $aStickyTopics);
        
                
        if (!Config::Get('plugin.stickytopics.sticky_topics_in_feed'))
            return;
        
        $oSmarty=$this->Viewer_GetSmartyObject();
        
        $aTopics=$oSmarty->GetVariable('aTopics');
        
        if (!$aTopics)
            return;
        
        $aTopics=$aTopics->value;

        if (!is_array($aTopics))
            $aTopics=array();

        foreach ($aTopics as $key => $oTopic)
        {
            if (in_array($oTopic->getId(), $aA))
                unset($aTopics[$key]);
        }

        $aTopics=array_merge($aStickyTopics, $aTopics);
        
        $this->Viewer_Assign('aTopics', $aTopics);
    }
}
?>