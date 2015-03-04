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

class PluginStickytopics_ActionBlog extends PluginStickytopics_Inherit_ActionBlog
{

    protected function RegisterEvent()
    {
        $this->AddEvent('sticky', 'EventStickyTopics');
        parent::RegisterEvent();
    }

    protected function EventStickyTopics()
    {
        /**
		 * Меню
		 */
        $this->sMenuSubItemSelect='';
        $this->sMenuItemSelect='sticky';
        /**
		 * Проверяем передан ли в УРЛе номер блога
		 */
        $sBlogId=$this->GetParam(0);
        if (!$oBlog=$this->Blog_GetBlogById($sBlogId))
        {
            return parent::EventNotFound();
        }
        /**
		 * Проверяем тип блога
		 */
        if ($oBlog->getType() == 'personal')
        {
            return parent::EventNotFound();
        }
        /**
		 * Проверям авторизован ли пользователь
		 */
        if (!$this->User_IsAuthorization())
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return Router::Action('error');
        }
        
        /**
		 * Проверка на право редактировать блог
		 */
        if (!$this->ACL_IsAllowEditBlog($oBlog, $this->oUserCurrent))
        {
            return parent::EventNotFound();
        }
        
        /**
		 * Проверка на право редактировать блог
		 */
        if (!$this->ACL_CanStickTopic($this->oUserCurrent, 'blog', $oBlog))
        {
            return parent::EventNotFound();
        }
        
        $this->Viewer_Assign('oBlogEdit', $oBlog);
        
        $aStickyTopics=$this->PluginStickytopics_Stickytopics_GetStickyTopicItemsByTargetTypeAndTargetId('blog', $oBlog->getId(), array('#order'=>array('topic_order'=>'asc')));
        
        $aA=array();
        foreach ($aStickyTopics as $oStickyTopic)
        {
            $aA[]=$oStickyTopic->getTopicId();
        }
        
        $this->Viewer_Assign('sTargetType', 'blog');
        $this->Viewer_Assign('iTargetId', $oBlog->getId());
        
        $this->Viewer_Assign('aTopic', $this->Topic_GetTopicsAdditionalData($aA));
        
        $this->Viewer_AppendStyle($this->PluginStickytopics_Stickytopics_GetTemplateFileWebPath(__CLASS__, 'css/style.css'));
        $this->Viewer_AppendScript($this->PluginStickytopics_Stickytopics_GetTemplateFileWebPath(__CLASS__, 'js/stickytopics.js'));
        
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.stickytopics.edit_blog_sticky'));
        
        $this->SetTemplate($this->PluginStickytopics_Stickytopics_GetTemplateFilePath(__CLASS__, 'actions/ActionBlog/sticky.tpl'));
    }

    protected function EventShowBlog()
    {
        if ($res=parent::EventShowBlog())
            return $res;
        
        $sBlogUrl=$this->sCurrentEvent;
        $sShowType=in_array($this->GetParamEventMatch(0, 0), array('bad', 'new', 'newall', 'discussed', 'top'))?$this->GetParamEventMatch(0, 0):'good';
        
        $iPage=$this->GetParamEventMatch(($sShowType == 'good')?0:1, 2)?$this->GetParamEventMatch(($sShowType == 'good')?0:1, 2):1;
        
        if ($iPage != 1 || $sShowType != 'good')
            return $res;
        
        $oBlog=$this->Blog_GetBlogByUrl($sBlogUrl);
        
        $aStickyTopics=$this->PluginStickytopics_Stickytopics_GetStickyTopicItemsByTargetTypeAndTargetId('blog', $oBlog->getId(), array('#order'=>array('topic_order'=>'asc')));
        
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