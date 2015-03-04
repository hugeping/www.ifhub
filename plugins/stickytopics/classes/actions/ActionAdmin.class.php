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

class PluginStickytopics_ActionAdmin extends PluginStickytopics_Inherit_ActionAdmin
{

    protected function RegisterEvent()
    {
        $this->AddEvent('sticky', 'EventStickyTopics');
        parent::RegisterEvent();
    }

    protected function EventStickyTopics()
    {
        if (!$this->ACL_CanStickTopic($this->oUserCurrent, 'index'))
        {
            return parent::EventNotFound();
        }
        
        $aStickyTopics=$this->PluginStickytopics_Stickytopics_GetStickyTopicItemsByTargetTypeAndTargetId('index', 0, array('#order'=>array('topic_order'=>'asc')));
        
        $aA=array();
        foreach ($aStickyTopics as $oStickyTopic)
        {
            $aA[]=$oStickyTopic->getTopicId();
        }
        
        $this->Viewer_Assign('sTargetType', 'index');
        $this->Viewer_Assign('iTargetId', 0);
        
        $this->Viewer_Assign('aTopic', $this->Topic_GetTopicsAdditionalData($aA));
        
        $this->Viewer_AppendStyle($this->PluginStickytopics_Stickytopics_GetTemplateFileWebPath(__CLASS__, 'css/style.css'));
        $this->Viewer_AppendScript($this->PluginStickytopics_Stickytopics_GetTemplateFileWebPath(__CLASS__, 'js/stickytopics.js'));
        
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.stickytopics.edit_index_sticky'));
        
        $this->SetTemplate($this->PluginStickytopics_Stickytopics_GetTemplateFilePath(__CLASS__, 'actions/ActionAdmin/sticky.tpl'));
    }
}
?>