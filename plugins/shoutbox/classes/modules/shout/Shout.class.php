<?php
/*-------------------------------------------------------
*			Модуль "ShoutBox"
*			by Hellcore
---------------------------------------------------------
*/

class PluginShoutbox_ModuleShout extends Module {

	protected $oMapper;

	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
	}
	
	public function Push(PluginShoutbox_ModuleShout_EntityShout $oShout) {
		if ($sId=$this->oMapper->Push($oShout)) {
			$oShout->setId($sId);
			return $oShout;
		}
		return false;
	}

	public function Edit($id, $action=1) {
		return $this->oMapper->Edit($id, $action);
	}

	public function GetLast($count,$mod=0,$iPage=1,$iLastId=-1) {
		$aShoutsIds = $this->oMapper->GetLast($count,$mod,$iPage,$iLastId);
		return $this->oMapper->GetLastByArrayId($aShoutsIds);
	}
	public function GetCount($mod=0) {
		return $this->oMapper->GetCount($mod);
	}

	public function Moderate($iId,$iType) {
		if ($iType==2) {
			return $this->oMapper->Delete($iId);
		} else {
			return $this->oMapper->Edit($iId, $iType==3 ? 0 : 1);
		}
	}

	public function InBlackList($username) {
		return $this->oMapper->InBlackList($username);
	}

	public function AddToBlackList($username) {
		return $this->oMapper->AddToBlackList($username);
	}

	public function RemoveFromBlackList($username) {
		return $this->oMapper->RemoveFromBlackList($username);
	}
}
?>