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

class PluginEngines_BlockEngines extends Block
{

    public function Exec()
    {
        $aEngines = $this->Topic_GetTopicsEnginesLast(Config::Get('block.stream.row'));
        $this->Viewer_Assign('aEngines',$aEngines);
    }

}

?>
