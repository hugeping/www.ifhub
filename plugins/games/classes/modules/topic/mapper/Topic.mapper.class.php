<?php
/* -------------------------------------------------------
*
*   LiveStreet (v1.x)
*   Plugin Games (v.0.1)
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

class PluginGames_ModuleTopic_MapperTopic extends PluginGames_Inherit_ModuleTopic_MapperTopic
{
    public function UpdateTopicFieldsGames(ModuleTopic_EntityTopic $oTopic)
    {
        $sql = "UPDATE " . Config::Get('db.table.topic') . "
                SET
                    topic_field_link1 = ?,
                    topic_date_edit = ?
                WHERE
                    topic_id = ?d
            ";
        if ($this->oDb->query($sql,$oTopic->getFieldLink1(), $oTopic->getDateEdit(), $oTopic->getId())) {
            return true;
        }
    return false;
    }
}

?>
