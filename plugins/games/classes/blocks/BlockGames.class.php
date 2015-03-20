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

class PluginGames_BlockGames extends Block
{

    public function Exec()
    {
        $aGames = $this->Topic_GetTopicsGamesLast(Config::Get('block.blogs.row'));
        $this->Viewer_Assign('aGames',$aGames);
    }

}

?>
