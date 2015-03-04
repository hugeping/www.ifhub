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
class PluginStickytopics_ModuleStickytopics extends ModuleORM
{
    public function GetTemplateFilePath($sPluginClass,$sFileName)
    {
        $sPP=Plugin::GetTemplatePath($sPluginClass);
        $fName=$sPP . $sFileName;
        if (file_exists($fName))
            return $fName;
        
        $aa=explode("/", $sPP);
        array_pop($aa);
        array_pop($aa);
        $aa[]='default';
        $aa[]='';
        return join("/", $aa) . $sFileName;
    }

    public function GetTemplateFileWebPath($sPluginClass,$sFileName)
    {
        return str_replace(Config::Get('path.root.server'), Config::Get('path.root.web'), $this->GetTemplateFilePath($sPluginClass, $sFileName));
    }

}
?>