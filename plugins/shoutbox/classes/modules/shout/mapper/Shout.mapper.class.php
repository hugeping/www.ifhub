<?php
/*-------------------------------------------------------
*			Модуль "ShoutBox"
*			by Hellcore
---------------------------------------------------------
*/

class PluginShoutbox_ModuleShout_MapperShout extends Mapper {
	public function Push($oShout) {

		$sql = "INSERT INTO ".Config::Get('plugin.shoutbox.table.shout')." SET ?a ";
		if ($iId=$this->oDb->query($sql,$oShout->_getData())) {
			return $iId;
		}
		return false;
	}

	public function Edit($id,$action) {
		$sql = "UPDATE ".Config::Get('plugin.shoutbox.table.shout')."
			SET 
			 	status = ?d 
			WHERE 
				id = ?d 
		";
		if ($this->oDb->query($sql,$action,$id))
			return true;
		else
			return false;
	}

	public function Delete($id) {
		$sql = "DELETE FROM ".Config::Get('plugin.shoutbox.table.shout')."
			WHERE id = ?d
		";
		if ($this->oDb->query($sql,$id))
			return true;
		else
			return false;
	}

	public function GetLast($count,$mod,$iCurrPage,$iLastId) {

		$sOrder=' id desc ';
		$iPerPage = $count;

		if ($mod==0) {
			$where=" WHERE status='0' { AND id > ?d }";
		} else {
			$where="{ WHERE id > ?d }";
		}

		$sql = "SELECT
					id
				FROM
					".Config::Get('plugin.shoutbox.table.shout')."
				$where
				ORDER by {$sOrder}
				LIMIT ?d, ?d ;
				";
		$aResult=array();
		if ($aRows=$this->oDb->selectPage($iCount,$sql, $iLastId>0 ? $iLastId : DBSIMPLE_SKIP, ($iCurrPage-1)*$iPerPage, $iPerPage)) {
			foreach ($aRows as $aRow) {
				$aResult[]=$aRow['id'];
			}
		}
		return $aResult;
	}
	public function GetCount($mod) {
		if ($mod==0) {
			$where=" WHERE status='0' ";
		} else {
			$where=" ";
		}
		$sql = "SELECT
					count(*) as c
				FROM
					".Config::Get('plugin.shoutbox.table.shout')."
				$where
					";
		if ($aRow=$this->oDb->selectRow($sql)) {
			return $aRow['c'];
		}
		return 0;
	}

	public function GetLastByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}

		$sql = "SELECT
					*
				FROM
					".Config::Get('plugin.shoutbox.table.shout')."
				WHERE
					id IN( ?a )
				ORDER BY FIELD(id, ?a) ";
		$aResult=array();

		if ($aRows=$this->oDb->select($sql,$aArrayId,$aArrayId)) {
			if (Config::Get ('plugin.shoutbox.sort_reverse')) {
				$aRows = array_reverse($aRows);
			}
			foreach ($aRows as $aRow) {
				$aResult[]=Engine::GetEntity('PluginShoutbox_Shout',$aRow);

			}
		}
		return $aResult;
	}

	public function InBlackList($sLogin) {
		$sql = "SELECT
				bl.uname
			FROM
				".Config::Get('plugin.shoutbox.table.shout_bl')." as bl
			WHERE
				bl.uname = ? ";
		if ($this->oDb->selectRow($sql,$sLogin)) {
			return true;
		}
		return false;
	}

	public function AddToBlackList($sLogin) {

		$sql = "INSERT INTO ".Config::Get('plugin.shoutbox.table.shout_bl')." SET uname = ? ";
		if ($this->oDb->query($sql,$sLogin)) {
			return true;
		}
		return false;
	}

	public function RemoveFromBlackList($sLogin) {

		$sql = "DELETE FROM ".Config::Get('plugin.shoutbox.table.shout_bl')." WHERE uname = ? ";
		if ($this->oDb->query($sql,$sLogin)) {
			return true;
		}
		return false;
	}
}
?>