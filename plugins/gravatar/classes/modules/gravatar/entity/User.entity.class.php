<?php
class PluginGravatar_ModuleGravatar_EntityUser extends PluginGravatar_Inherit_ModuleUser_EntityUser {
  
	//*********************************************************************************	  
    public function getProfileAvatarPath($iSize=100) {
        if($sPath=$this->getProfileAvatar()){ 	
        	return str_replace('_100x100',(($iSize==0)?"":"_{$iSize}x{$iSize}"),$sPath."?".date('His',strtotime($this->getProfileDate())));
    	}else{
    		return "http://www.gravatar.com/avatar/".md5(strtolower($this->getMail())).".png?size=".$iSize;
    	}
    }
}
?>