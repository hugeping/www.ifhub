<?php

class PluginGravatar extends Plugin {

	//*********************************************************************************	
    protected $aInherits = array(
		'entity'  => array('ModuleUser_EntityUser'=>'_ModuleGravatar_EntityUser')
    );
	
	//*********************************************************************************	
	public function Activate(){
		return true;
	}
	
	//*********************************************************************************	
	public function Deactivate(){
		return true;
	}
	
	//*********************************************************************************	
	public function Init(){
	}
	
}

?>