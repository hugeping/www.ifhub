<?php
class PluginRetroavatar_ModuleUser_EntityUser extends PluginRetroavatar_Inherit_ModuleUser_EntityUser {
    public function getProfileAvatarPath($iSize=100) {
        if(!$sPath=$this->getProfileAvatar()){
            $sRetroPath = "http://retroavatar.appspot.com/api?name=".md5($this->getLogin())."&fact=".(int)($iSize/14);
            if(Config::Get('plugin.retroavatar.alpha')) $sRetroPath .= "&alpha=true";
            if ($sPath = $this->User_UploadRetroAvatar($sRetroPath, $this)){
                return str_replace('_100x100',(($iSize==0)?"":"_{$iSize}x{$iSize}"),$sPath."?".date('His',strtotime($this->getProfileDate())));
            }
            return $sRetroPath;
        }else{
            return str_replace('_100x100',(($iSize==0)?"":"_{$iSize}x{$iSize}"),$sPath."?".date('His',strtotime($this->getProfileDate())));
    	}
    }
}
?>