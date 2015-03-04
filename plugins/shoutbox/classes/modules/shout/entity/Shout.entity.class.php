<?php
/*-------------------------------------------------------
*			Модуль "ShoutBox"
*			by Hellcore
---------------------------------------------------------
*/

class PluginShoutbox_ModuleShout_EntityShout extends Entity {

	public function getId() {
		return $this->_getDataOne('id');
	}
	public function getUserId() {
		return $this->_getDataOne('user_id');
	}
	// 0 - обынчый, 1-софт удалено
	public function getStatus() {
		return $this->_getDataOne('status');
	}
	public function getText() {
		return $this->_getDataOne('text');
	}
	// mod? Если 0 то обычный, если один то модераторский спец. пост. рисуется специальным цветом (т.е команда)
	public function getMod() {
		return $this->_getDataOne('mod');
	}
	public function getDate() {
		return $this->_getDataOne('datetime');
	}


	public function setId($data) {
		$this->_aData['id']=$data;
	}
	public function setUserId($data) {
		$this->_aData['user_id']=$data;
	}
	public function setStatus($data) {
		$this->_aData['status']=$data;
	}
	public function setText($data) {
		$this->_aData['text']=$data;
	}
	public function setMod($data) {
		$this->_aData['mod']=$data;
	}
	public function setDate($data) {
		$this->_aData['datetime']=$data;
	}
}
?>