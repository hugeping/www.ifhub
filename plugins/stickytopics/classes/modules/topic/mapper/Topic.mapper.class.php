<?php
class PluginStickytopics_ModuleTopic_MapperTopic extends PluginStickytopics_Inherit_ModuleTopic_MapperTopic
{

    protected function buildFilter($aFilter)
    {
        $sWhere=parent::buildFilter($aFilter);
        
        if (isset($aFilter['title_like']))
        {
            $sWhere.=" AND t.topic_title like concat('%','" . mysql_real_escape_string($aFilter['title_like']) . "','%')";
        }
        
        if (isset($aFilter['exclude_topics']))
        {
            if (!is_array($aFilter['exclude_topics']))
                $aFilter['exclude_topics']=array($aFilter['exclude_topics']);
            
            if (count($aFilter['exclude_topics']) > 0)
                $sWhere.=" AND t.topic_id not in (".join(",",array_map('mysql_real_escape_string',$aFilter['exclude_topics'])).")";
        }
        
        return $sWhere;
    }
}
?>