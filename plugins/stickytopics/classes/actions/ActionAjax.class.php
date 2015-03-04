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

class PluginStickytopics_ActionAjax extends PluginStickytopics_Inherit_ActionAjax
{

    protected function RegisterEvent()
    {
        $this->AddEventPreg('/^stickytopics$/i', '/^find$/', 'EventStickyTopicsFindTopic');
        $this->AddEventPreg('/^stickytopics$/i', '/^reload$/', 'EventStickyTopicsReload');
        $this->AddEventPreg('/^stickytopics$/i', '/^add$/', 'EventStickyTopicsAdd');
        $this->AddEventPreg('/^stickytopics$/i', '/^delete$/', 'EventStickyTopicsDelete');
        $this->AddEventPreg('/^stickytopics$/i', '/^move$/', 'EventStickyTopicsMove');
        parent::RegisterEvent();
    }

    /**
	 * Получение таблицы прикрепленных топиков
	 *
	 */
    protected function EventStickyTopicsReload()
    {
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        
        $sTargetType=getRequest('targetType');
        $iTargetId=getRequest('targetId');
        
        switch ($sTargetType)
        {
            case 'index':
                if (!$this->oUserCurrent->isAdministrator())
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
                    return;
                }
                break;
            case 'blog':
            case 'personal':
                $oBlog=$this->Blog_GetBlogById($iTargetId);
                if (!$oBlog)
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_blog'), $this->Lang_Get('error'));
                    return;
                }
                
                /**
		         * Проверка на право редактировать блог
		         */
                if (!$this->ACL_IsAllowEditBlog($oBlog, $this->oUserCurrent))
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
                    return;
                }
                
                break;
            default:
                $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_target_type'), $this->Lang_Get('error'));
                return;
        }
        
        $aStickyTopics=$this->PluginStickytopics_Stickytopics_GetStickyTopicItemsByTargetTypeAndTargetId($sTargetType, $iTargetId, array('#order'=>array('topic_order'=>'asc')));
        
        $aA=array();
        foreach ($aStickyTopics as $oStickyTopic)
        {
            $aA[]=$oStickyTopic->getTopicId();
        }
        
        $oViewer=$this->Viewer_GetLocalViewer();
        
        $oViewer->Assign('aTopic', $this->Topic_GetTopicsAdditionalData($aA));
        $oViewer->Assign('bStickyList', true);
        
        $res=$oViewer->Fetch($this->PluginStickytopics_Stickytopics_GetTemplateFilePath(__CLASS__, 'topic_list.tpl'));
        
        $this->Viewer_AssignAjax('topicData', $res);
    }

    /**
	 * Нахождение подходящих топиков
	 *
	 */
    protected function EventStickyTopicsFindTopic()
    {
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        
        $aBlogs=$this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
        
        $oPersBlog=$this->Blog_GetPersonalBlogByUserId($this->oUserCurrent->getId());
        if ($oPersBlog)
            $aBlogs=array_merge($aBlogs, array($oPersBlog->getId()));
        
        $sTargetType=getRequest('targetType');
        $iTargetId=getRequest('targetId');
        
        $aFilter=array('topic_publish'=>1, 'blog_id'=>$aBlogs, 'title_like'=>getRequest('titlePart'), 'exclude_topics'=>getRequest('excludeTopics'));
        
        if ($sTargetType == 'personal' && Config::Get('plugin.stickytopics.personal_sticky_topics_kind') != 'all')
        {
            $aFilter['user_id']=$this->oUserCurrent->getId();
            
            if (Config::Get('plugin.stickytopics.personal_sticky_topics_kind') == 'personal')
            {
                $oPersBlog=$this->Blog_GetPersonalBlogByUserId($this->oUserCurrent->getId());
                if (!$oPersBlog)
                {
                    $aFilter['user_id']=-1;
                }
                else
                    $aBlogs=array($oPersBlog->getId());
            }
        }
        
        if ($sTargetType == 'blog' && Config::Get('plugin.stickytopics.blog_sticky_topics_kind') != 'all')
        {
            $oBlog=$this->Blog_GetBlogById($iTargetId);
            if ($oBlog)
                $aBlogs=array($oBlog->getId());
            else
                $aBlogs=array(-1);
        }
        
        $aFilter['blog_id']=$aBlogs;
        
        $aTopic=$this->Topic_GetTopicsByFilter($aFilter, 1, 20);
        
        $oViewer=$this->Viewer_GetLocalViewer();
        
        $oViewer->Assign('aTopic', $aTopic['collection']);
        
        $this->Viewer_AssignAjax('topicData', $oViewer->Fetch($this->PluginStickytopics_Stickytopics_GetTemplateFilePath(__CLASS__, 'topic_list.tpl')));
    }

    /**
	 * Получение таблицы прикрепленных топиков
	 *
	 */
    protected function EventStickyTopicsAdd()
    {
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        
        $sTargetType=getRequest('targetType');
        $iTargetId=getRequest('targetId');
        
        switch ($sTargetType)
        {
            case 'index':
                if (!$this->oUserCurrent->isAdministrator())
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
                    return;
                }
                break;
            case 'blog':
            
            case 'personal':
                $oBlog=$this->Blog_GetBlogById($iTargetId);
                if (!$oBlog)
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_blog'), $this->Lang_Get('error'));
                    return;
                }
                
                if (!$this->ACL_CanStickTopic($this->oUserCurrent, $sTargetType, $iTargetId))
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
                    return;
                }
                
                break;
            default:
                $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_target_type'), $this->Lang_Get('error'));
                return;
        }
        
        $oTopic=$this->Topic_GetTopicById(getRequest('topicId'));
        
        if (!$oTopic)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_topic'), $this->Lang_Get('error'));
            return;
        }
        
        if (in_array($oTopic->getBlogId(), $this->Blog_GetInaccessibleBlogsByUser($this->oUserCurrent)))
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return;
        }
        
        $oStickyTopic=$this->PluginStickytopics_Stickytopics_GetStickyTopicByTargetTypeAndTargetIdAndTopicId($sTargetType, $iTargetId, $oTopic->getId());
        
        if ($oStickyTopic)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.already_sticky'), $this->Lang_Get('error'));
            return;
        }
        
        if ($sTargetType == 'personal' && Config::Get('plugin.stickytopics.personal_sticky_topics_kind') != 'all')
        {
            if ($oTopic->getUserId() != $this->oUserCurrent->getId())
            {
                $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.only_self_topics'), $this->Lang_Get('error'));
                return;
            }
            
            if (Config::Get('plugin.stickytopics.personal_sticky_topics_kind') == 'personal')
            {
                $oPersBlog=$this->Blog_GetPersonalBlogByUserId($this->oUserCurrent->getId());
                if (!$oPersBlog || ($oPersBlog && $oTopic->getBlogId() != $oPersBlog->getId()))
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.only_personal_topics'), $this->Lang_Get('error'));
                    return;
                }
            }
        }
        
        if ($sTargetType == 'blog' && Config::Get('plugin.stickytopics.blog_sticky_topics_kind') != 'all')
        {
            if (Config::Get('plugin.stickytopics.blog_sticky_topics_kind') == 'blog')
            {
                if ($oTopic->getBlogId() != $oBlog->getId())
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.only_blog_topics'), $this->Lang_Get('error'));
                    return;
                }
            }
        }
        
        $oLast=array_pop($this->PluginStickytopics_Stickytopics_GetStickyTopicItemsByTargetTypeAndTargetId($sTargetType, $iTargetId, array('#order'=>array('topic_order'=>'desc'), '#limit'=>array(0, 1))));
        
        if ($oLast)
            $iOrder=$oLast->getTopicOrder() + 1;
        else
            $iOrder=1;
        
        $oStickyTopic=Engine::GetEntity('PluginStickytopics_ModuleStickytopics_EntityStickyTopic');
        
        $oStickyTopic->setUserId($this->oUserCurrent->getId());
        $oStickyTopic->setTargetType($sTargetType);
        $oStickyTopic->setTargetId($iTargetId);
        $oStickyTopic->setTopicId($oTopic->getId());
        $oStickyTopic->setTopicOrder($iOrder);
        
        if (!$oStickyTopic->save())
        {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }
    }

    /**
	 * Удаление топика из списка прикрепленных
	 *
	 */
    protected function EventStickyTopicsDelete()
    {
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        
        $sTargetType=getRequest('targetType');
        $iTargetId=getRequest('targetId');
        
        switch ($sTargetType)
        {
            case 'index':
                if (!$this->oUserCurrent->isAdministrator())
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
                    return;
                }
                break;
            case 'blog':
            case 'personal':
                $oBlog=$this->Blog_GetBlogById($iTargetId);
                if (!$oBlog)
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_blog'), $this->Lang_Get('error'));
                    return;
                }
                
                /**
		         * Проверка на право редактировать блог
		         */
                if (!$this->ACL_IsAllowEditBlog($oBlog, $this->oUserCurrent))
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
                    return;
                }
                
                break;
            default:
                $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_target_type'), $this->Lang_Get('error'));
                return;
        }
        
        $oTopic=$this->Topic_GetTopicById(getRequest('topicId'));
        
        if (!$oTopic)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_topic'), $this->Lang_Get('error'));
            return;
        }
        
        $oStickyTopic=$this->PluginStickytopics_Stickytopics_GetStickyTopicByTargetTypeAndTargetIdAndTopicId($sTargetType, $iTargetId, $oTopic->getId());
        
        if (!$oStickyTopic)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.no_sticky'), $this->Lang_Get('error'));
            return;
        }
        
        if (!$oStickyTopic->delete())
        {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }
    }

    /**
	 * Смена порядка
	 *
	 */
    protected function EventStickyTopicsMove()
    {
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        
        $sTargetType=getRequest('targetType');
        $iTargetId=getRequest('targetId');
        
        switch ($sTargetType)
        {
            case 'index':
                if (!$this->oUserCurrent->isAdministrator())
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
                    return;
                }
                break;
            case 'blog':
            case 'personal':
                $oBlog=$this->Blog_GetBlogById($iTargetId);
                if (!$oBlog)
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_blog'), $this->Lang_Get('error'));
                    return;
                }
                
                /**
		         * Проверка на право редактировать блог
		         */
                if (!$this->ACL_IsAllowEditBlog($oBlog, $this->oUserCurrent))
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
                    return;
                }
                
                break;
            default:
                $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_target_type'), $this->Lang_Get('error'));
                return;
        }
        
        $oTopic=$this->Topic_GetTopicById(getRequest('topicId'));
        
        if (!$oTopic)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.unknown_topic'), $this->Lang_Get('error'));
            return;
        }
        
        $oStickyTopic=$this->PluginStickytopics_Stickytopics_GetStickyTopicByTargetTypeAndTargetIdAndTopicId($sTargetType, $iTargetId, $oTopic->getId());
        
        if (!$oStickyTopic)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.stickytopics.no_sticky'), $this->Lang_Get('error'));
            return;
        }
        
        $aStickyTopics=$this->PluginStickytopics_Stickytopics_GetStickyTopicItemsByTargetTypeAndTargetId($sTargetType, $iTargetId, array('#order'=>array('topic_order'=>'asc')));
        
        $oPrev=null;
        $oNext=null;
        
        reset($aStickyTopics);
        $oC=current($aStickyTopics);
        while ($oC)
        {
            if ($oC->getTopicId() != $oTopic->getId())
                $oPrev=$oC;
            else
            {
                $oNext=next($aStickyTopics);
                break;
            }
            $oC=next($aStickyTopics);
        }
        
        if (getRequest('direction') > 0)
        {
            $oEx=$oNext;
        }
        else
        {
            $oEx=$oPrev;
        }
        
        if ($oEx)
        {
            $os=$oEx->getTopicOrder();
            $oEx->setTopicOrder($oStickyTopic->getTopicOrder());
            $oStickyTopic->setTopicOrder($os);
            if (!$oStickyTopic->save() || !$oEx->save())
            {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return;
            }
        }
    }

}
?>