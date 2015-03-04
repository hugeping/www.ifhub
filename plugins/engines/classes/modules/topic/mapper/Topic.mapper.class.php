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

class PluginEngines_ModuleTopic_MapperTopic extends PluginEngines_Inherit_ModuleTopic_MapperTopic
{
    public function UpdateTopicFieldsEngines(ModuleTopic_EntityTopic $oTopic)
    {
        $sql = "UPDATE " . Config::Get('db.table.topic') . "
                SET
                    topic_field_link1 = ?,
                    topic_field_string1 = ?,
                    topic_date_edit = ?
                WHERE
                    topic_id = ?d
            ";
        if ($this->oDb->query($sql,$oTopic->getFieldLink1(), $oTopic->getFieldString1(), $oTopic->getDateEdit(), $oTopic->getId())) {
            return true;
        }
    return false;
    }
}

?>
