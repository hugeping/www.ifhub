<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * Маппер, обрабатывает запросы к БД
 *
 */
class PluginOpenid_ModuleOpenid_MapperOpenid extends Mapper {	
	
	/**
	 * Создает связь OpenID
	 *
	 * @param PluginOpenid_ModuleOpenid_EntityOpenid $oOpenId
	 * @return unknown
	 */
	public function AddOpenId(PluginOpenid_ModuleOpenid_EntityOpenid $oOpenId) {
		$sql = "INSERT INTO ".Config::Get('plugin.openid.table.openid')." SET ?a ";			
		if ($this->oDb->query($sql,$oOpenId->_getData())===0) {
			return true;
		}		
		return false;
	}
	/**
	 * Получает связь OpenID по идентификатору
	 *
	 * @param unknown_type $sOpenId
	 * @return unknown
	 */
	public function GetOpenId($sOpenId) {
		$sql = "SELECT * FROM ".Config::Get('plugin.openid.table.openid')." WHERE openid = ? ";
		if ($aRow=$this->oDb->selectRow($sql,$sOpenId)) {
			return Engine::GetEntity('PluginOpenid_Openid',$aRow);
		}
		return null;
	}
	/**
	 * Получает список связей OpenID пользователя
	 *
	 * @param unknown_type $sUserId
	 * @return unknown
	 */
	public function GetOpenIdByUser($sUserId) {
		$sql = "SELECT * FROM ".Config::Get('plugin.openid.table.openid')." WHERE user_id = ? ";
		$aCollection=array();
		if ($aRows=$this->oDb->select($sql,$sUserId)) {
			foreach ($aRows as $aRow) {
				$aCollection[]=Engine::GetEntity('PluginOpenid_Openid',$aRow);
			}
		}
		return $aCollection;
	}
	/**
	 * Получает пользователя по идентификатору OpenID
	 *
	 * @param unknown_type $sOpenId
	 * @return unknown
	 */
	public function GetUserByOpenId($sOpenId) {
		$sql = "SELECT u.* FROM ".Config::Get('plugin.openid.table.openid')." as o, ".Config::Get('db.table.user')." as u WHERE openid = ? and u.user_id=o.user_id ";
		if ($aRow=$this->oDb->selectRow($sql,$sOpenId)) {
			return Engine::GetEntity('User',$aRow);
		}
		return null;
	}
	/**
	 * Удаляет связь OpenID у пользователя
	 *
	 * @param unknown_type $sOpenId
	 * @return unknown
	 */
	public function DeleteOpenId($sOpenId) {
		$sql = "DELETE FROM ".Config::Get('plugin.openid.table.openid')." WHERE `openid` = ? ";			
		return $this->oDb->query($sql,$sOpenId);
	}
	/**
	 * Создает временные данные
	 *
	 * @param PluginOpenid_ModuleOpenid_EntityTmp $oTmp
	 * @return unknown
	 */
	public function AddTmp(PluginOpenid_ModuleOpenid_EntityTmp $oTmp) {
		$sql = "INSERT INTO ".Config::Get('plugin.openid.table.openid_tmp')." SET ?a ";			
		if ($this->oDb->query($sql,$oTmp->_getData())===0) {
			return true;
		}		
		return false;
	}
	/**
	 * Обновляет временные данные
	 *
	 * @param PluginOpenid_ModuleOpenid_EntityTmp $oTmp
	 * @return unknown
	 */
	public function UpdateTmp(PluginOpenid_ModuleOpenid_EntityTmp $oTmp) {
		$sql = "UPDATE ".Config::Get('plugin.openid.table.openid_tmp')." SET ?a WHERE `key` = ? ";			
		return $this->oDb->query($sql,$oTmp->_getData(array('confirm_mail_key','confirm_mail')),$oTmp->getKey());
	}
	/**
	 * Получает временные данные по ключу
	 *
	 * @param unknown_type $sKey
	 * @return unknown
	 */
	public function GetTmp($sKey) {
		$sql = "SELECT * FROM ".Config::Get('plugin.openid.table.openid_tmp')." WHERE `key` = ? ";
		if ($aRow=$this->oDb->selectRow($sql,$sKey)) {
			return Engine::GetEntity('PluginOpenid_Openid_Tmp',$aRow);
		}
		return null;
	}
	/**
	 * Получает временные данные по ключу подтверждения почты
	 *
	 * @param unknown_type $sKey
	 * @return unknown
	 */
	public function GetTmpByConfirmMailKey($sKey) {
		$sql = "SELECT * FROM ".Config::Get('plugin.openid.table.openid_tmp')." WHERE `confirm_mail_key` = ? ";
		if ($aRow=$this->oDb->selectRow($sql,$sKey)) {
			return Engine::GetEntity('PluginOpenid_Openid_Tmp',$aRow);
		}
		return null;
	}
	/**
	 * Удаляет временные данные
	 *
	 * @param unknown_type $sKey
	 * @return unknown
	 */
	public function DeleteTmp($sKey) {
		$sql = "DELETE FROM ".Config::Get('plugin.openid.table.openid_tmp')." WHERE `key` = ? ";			
		return $this->oDb->query($sql,$sKey);
	}
}
?>