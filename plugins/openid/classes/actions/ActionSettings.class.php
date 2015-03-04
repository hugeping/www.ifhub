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
 * Обрабатывает настройки OpenId для пользователя
 *
 */
class PluginOpenid_ActionSettings extends ActionPlugin {
	/**
	 * Какое меню активно
	 *
	 * @var unknown_type
	 */
	/**
	 * Какое меню активно
	 *
	 * @var string
	 */
	protected $sMenuItemSelect='settings';
	/**
	 * Какое подменю активно
	 *
	 * @var string
	 */
	protected $sMenuSubItemSelect='openid';
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 *
	 * @return null
	 */
	public function Init() {
		/**
		 * Проверяем авторизован ли юзер
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return Router::Action('error');
		}
		$this->oUserCurrent=$this->User_GetUserCurrent();
	}

	protected function RegisterEvent() {
		$this->AddEventPreg('/^settings$/i','/^$/i','EventSettings');
		$this->AddEventPreg('/^settings$/i','/^ajaxdeleteopenid$/i','EventAjaxDeleteOpenId');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	/**
	 * Страница настроект OpenID
	 *
	 */
	protected function EventSettings() {
		/**
		 * Получаем список используемых OpenId
		 */
		$aOpenId=$this->PluginOpenid_Openid_GetOpenIdByUser($this->oUserCurrent->getId());
		$this->Viewer_Assign('aOpenId',$aOpenId);
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('settings');
	}

	/**
	 * Обработка Ajax удаления связи с OpenID
	 *
	 */
	protected function EventAjaxDeleteOpenId() {
		/**
		 * Устанавливаем тип ответа для Ajax
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверяем существование OpenID
		 */
		$sOpenId=getRequest('openid',null,'post');
		if(!($oOpenId=$this->PluginOpenid_Openid_GetOpenId($sOpenId))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Проверяем права на удаление
		 */
		if($oOpenId->getUserId()!=$this->oUserCurrent->getId()) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Не даем возможность удалить, если у пользователя в профиле нет почты и это последний OpenID
		 */
		$aOpenId=$this->PluginOpenid_Openid_GetOpenIdByUser($this->oUserCurrent->getId());
		if (count($aOpenId)==1 and !$this->oUserCurrent->getMail()) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.openid.menu_settings_delete_last_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Удаляем
		 */
		$this->PluginOpenid_Openid_DeleteOpenId($sOpenId);
		$this->Message_AddNoticeSingle($this->Lang_Get('plugin.openid.menu_settings_delete_ok'),$this->Lang_Get('attention'));
	}
	/**
	 * При завершении экшена загружаем переменные в шаблон
	 *
	 */
	public function EventShutdown() {
		$iCountTopicFavourite=$this->Topic_GetCountTopicsFavouriteByUserId($this->oUserCurrent->getId());
		$iCountTopicUser=$this->Topic_GetCountTopicsPersonalByUser($this->oUserCurrent->getId(),1);
		$iCountCommentUser=$this->Comment_GetCountCommentsByUserId($this->oUserCurrent->getId(),'topic');
		$iCountCommentFavourite=$this->Comment_GetCountCommentsFavouriteByUserId($this->oUserCurrent->getId());
		$iCountNoteUser=$this->User_GetCountUserNotesByUserId($this->oUserCurrent->getId());

		$this->Viewer_Assign('oUserProfile',$this->oUserCurrent);
		$this->Viewer_Assign('iCountWallUser',$this->Wall_GetCountWall(array('wall_user_id'=>$this->oUserCurrent->getId(),'pid'=>null)));
		/**
		 * Общее число публикация и избранного
		 */
		$this->Viewer_Assign('iCountCreated',$iCountNoteUser+$iCountTopicUser+$iCountCommentUser);
		$this->Viewer_Assign('iCountFavourite',$iCountCommentFavourite+$iCountTopicFavourite);
		$this->Viewer_Assign('iCountFriendsUser',$this->User_GetCountUsersFriend($this->oUserCurrent->getId()));

		/**
		 * Загружаем в шаблон необходимые переменные
		 */
		$this->Viewer_Assign('sMenuItemSelect',$this->sMenuItemSelect);
		$this->Viewer_Assign('sMenuSubItemSelect',$this->sMenuSubItemSelect);
	}
}
?>