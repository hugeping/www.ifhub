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

class PluginStickytopics_ActionIndex extends PluginStickytopics_Inherit_ActionIndex
{

    protected function EventIndex()
    {
        parent::EventIndex();
        
        $iPage=$this->GetEventMatch(2)?$this->GetEventMatch(2):1;
        
        if ($iPage != 1)
            return;
        
        $aStickyTopics=$this->PluginStickytopics_Stickytopics_GetStickyTopicItemsByTargetTypeAndTargetId('index', 0, array('#order'=>array('topic_order'=>'asc')));
        
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