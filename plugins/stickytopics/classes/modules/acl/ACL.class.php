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
class PluginStickytopics_ModuleACL extends PluginStickytopics_Inherit_ModuleACL
{

    /**
	 * Проверяет может ли пользователь прикреплять топики указанного типа
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @param string $sTargetType Тип "контейнера" топика - главная страница, блог, персональный блог
	 * @param $iTargetId Id контейнера или сам контейнер для проверки
	 * @return bool
	 */
    public function CanStickTopic(ModuleUser_EntityUser $oUser,$sTargetType=null,$iTargetId=null)
    {
        if (!$oUser)
            return false;
        
        switch ($sTargetType)
        {
            case 'personal':
                if (!Config::Get('plugin.stickytopics.allow_personal_sticky_topics'))
                    return false;
                
                $oPersBlog=$this->Blog_GetPersonalBlogByUserId($oUser->getId());
                if (!$oPersBlog)
                    return false;
                
                return true;
                break;
            case 'blog':
                if (is_numeric($iTargetId))
                {
                    $oBlog=$this->Blog_GetBlogById($iTargetId);
                    if (!$oBlog)
                        return false;
                }
                else
                    $oBlog=$iTargetId;
                
                if ($oUser->isAdministrator())
                    return true;
                
                return $this->ACL_IsAllowAdminBlog($oBlog, $oUser);
                
                break;
            case 'index':
                if ($oUser->isAdministrator())
                    return true;
                
                break;
            default:
                return false;
        }
    }

    /**
     * Проверяет может ли пользователь $oUser видеть топик $oTopic
     * @param ModuleUser_EntityUser $oUser
     * @param ModuleTopic_EntityTopic $oTopic
     */
    public function CanViewTopic($oUser,ModuleTopic_EntityTopic $oTopic)
    {
        if (!$oTopic->getPublish() and (!$oUser or ($oUser->getId() != $oTopic->getUserId() and !$oUser->isAdministrator())))
        {
            return false;
        }
        
        if ($oTopic->getBlog()->getType() == 'close' and (!$oUser || !in_array($oTopic->getBlog()->getId(), $this->Blog_GetAccessibleBlogsByUser($oUser))))
        {
            return false;
        }
        
        return true;
    }
}
?>