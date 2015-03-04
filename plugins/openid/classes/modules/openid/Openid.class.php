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
 * Модуль OpenID авторизации
 *
 */
class PluginOpenid_ModuleOpenid extends Module {

	protected $oConsumer=null;
	protected $oMapper;

	/**
	 * Инициализация модуля
	 */
	public function Init() {
		/**
		 * Подключаем маппер
		 */
		$this->oMapper=Engine::GetMapper(__CLASS__);
		/**
		 * Если нужно то отключаем использование библиотеки GMP
		 */
		if (Config::Get('plugin.openid.buggy_gmp')) {
			define('Auth_OpenID_BUGGY_GMP', true);
		}
		/**
		 * Подключаем необходимые библиотеки для работы с OpenID
		 */
		require_once(Plugin::GetPath(__CLASS__).'classes/lib/external/php-openid-2.1.3/config.php');
		require_once('Auth/OpenID/Consumer.php');
		require_once('Auth/OpenID/FileStore.php');
		require_once('Auth/OpenID/SReg.php');
		require_once('Auth/OpenID/AX.php');
		/**
		 * Создаем объект OpenID с файловым хранилищем
		 */
		$this->oConsumer=new Auth_OpenID_Consumer(new Auth_OpenID_FileStore(Config::Get('plugin.openid.file_store')));
	}
	/**
	 * Запускает процесс OpenID авторизации
	 *
	 * @param unknown_type $sOpenId
	 * @param unknown_type $sPath
	 * @return unknown
	 */
	public function Login($sOpenId,$sPath) {
		/**
		 * Начинаем...
		 */
		$auth_request = $this->oConsumer->begin($sOpenId);
		if (!$auth_request) {
			return false;
		}
		/**
		 * Подключаем использование "sreg" для получения дополнительных данных от OpenID провайдера
		 */
		$sreg_request = Auth_OpenID_SRegRequest::build(array('nickname'),array('fullname', 'email'));
		if ($sreg_request) {
			$auth_request->addExtension($sreg_request);
		}
		/**
		 * Подключаем использование "ax" для получения дополнительных данных от OpenID провайдера
		 */
		$ax_request = new Auth_OpenID_AX_FetchRequest();
		if ($ax_request) {
			$ax_request->add(Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email',1,1, 'email'));
			$auth_request->addExtension($ax_request);
		}
		/**
		 * Определяем тип редиректа на сервер провайдера OpenID
		 */
		if ($auth_request->shouldSendRedirect()) {
			/**
			 * Обычный редирект через header
			 */
			$redirect_url = $auth_request->redirectURL($this->getTrustRoot(),$sPath);
			if (Auth_OpenID::isFailure($redirect_url)) {
				return false;
			} else {
				Router::Location($redirect_url);
			}
		} else {
			/**
			 * JavaScript редирект
			 */
			$form_html = $auth_request->htmlMarkup($this->getTrustRoot(), $sPath,false, array('id' => 'openid_message'));
			if (Auth_OpenID::isFailure($form_html)) {
				return false;
			} else {
				print $form_html;
				exit();
			}
		}
	}
	/**
	 * Верификация данных от OpenID провайдера
	 *
	 * @param unknown_type $sPath
	 * @return unknown
	 */
	public function Verify($sPath) {
		$aReturn=array(
			'status' => false,
			'msg' => '',
		);

		$response = $this->oConsumer->complete($sPath);
		if ($response->status == Auth_OpenID_CANCEL) {
			/**
			 * Пользователь отменил авторизацию
			 */
			$aReturn['msg']=$this->Lang_Get('plugin.openid.result_cancel');
		} else if ($response->status == Auth_OpenID_FAILURE) {
			/**
			 * Ошибка авторизации
			 */
			$aReturn['msg']=$response->message;
		} else if ($response->status == Auth_OpenID_SUCCESS) {
			/**
			 * Авторизация прошла успешно
			 */
			$aReturn['msg']=$this->Lang_Get('plugin.openid.result_success');
			$aReturn['status']=true;
			$aReturn['id']=$response->getDisplayIdentifier();
			/**
			 * Достаем дополнительные данные
			 */
			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
			$sreg = $sreg_resp->contents();
			$aReturn['sreg']=$sreg;
			/**
			 * Достаем дополнительные данные
			 */
			$ax_resp=new Auth_OpenID_AX_FetchResponse();
			if ($ax=$ax_resp->fromSuccessResponse($response)) {
				$aDataAx=$ax->data;
				if (isset($aDataAx['http://axschema.org/contact/email']) and isset($aDataAx['http://axschema.org/contact/email'][0])) {
					$aReturn['ax']['email']=$aDataAx['http://axschema.org/contact/email'][0];
				}
			}
		}
		return $aReturn;
	}
	/**
	 * Возвращает полный путь до веб-сервера
	 *
	 * @return unknown
	 */
	protected function getTrustRoot() {
		return sprintf("%s://%s:%s%s/",
					   $this->getScheme(), array_shift(explode(':', $_SERVER['HTTP_HOST'])),
					   $_SERVER['SERVER_PORT'],
					   '');
	}
	/**
	 * Получает текущую схему протокола HTTP
	 *
	 * @return unknown
	 */
	protected function getScheme() {
		$sScheme='http';
		if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']=='on') {
			$sScheme.='s';
		}
		return $sScheme;
	}
	/**
	 * Создает связь OpenID
	 *
	 * @param PluginOpenid_ModuleOpenid_EntityOpenid $oOpenId
	 * @return unknown
	 */
	public function AddOpenId(PluginOpenid_ModuleOpenid_EntityOpenid $oOpenId) {
		return $this->oMapper->AddOpenId($oOpenId);
	}
	/**
	 * Получает связь OpenID по идентификатору
	 *
	 * @param unknown_type $sOpenId
	 * @return unknown
	 */
	public function GetOpenId($sOpenId) {
		return $this->oMapper->GetOpenId($sOpenId);
	}
	/**
	 * Получает пользователя по идентификатору OpenID
	 *
	 * @param unknown_type $sOpenId
	 * @return unknown
	 */
	public function GetUserByOpenId($sOpenId) {
		return $this->oMapper->GetUserByOpenId($sOpenId);
	}
	/**
	 * Удаляет связь OpenID у пользователя
	 *
	 * @param unknown_type $sOpenId
	 * @return unknown
	 */
	public function DeleteOpenId($sOpenId) {
		return $this->oMapper->DeleteOpenId($sOpenId);
	}
	/**
	 * Создает временные данные
	 *
	 * @param PluginOpenid_ModuleOpenid_EntityTmp $oTmp
	 * @return unknown
	 */
	public function AddTmp(PluginOpenid_ModuleOpenid_EntityTmp $oTmp) {
		return $this->oMapper->AddTmp($oTmp);
	}
	/**
	 * Обновляет временные данные
	 *
	 * @param PluginOpenid_ModuleOpenid_EntityTmp $oTmp
	 * @return unknown
	 */
	public function UpdateTmp(PluginOpenid_ModuleOpenid_EntityTmp $oTmp) {
		return $this->oMapper->UpdateTmp($oTmp);
	}
	/**
	 * Получает временные данные по ключу
	 *
	 * @param unknown_type $sKey
	 * @return unknown
	 */
	public function GetTmp($sKey) {
		return $this->oMapper->GetTmp($sKey);
	}
	/**
	 * Получает временные данные по ключу подтверждения почты
	 *
	 * @param unknown_type $sKey
	 * @return unknown
	 */
	public function GetTmpByConfirmMailKey($sKey) {
		if (!is_string($sKey)) {
			return false;
		}
		return $this->oMapper->GetTmpByConfirmMailKey($sKey);
	}
	/**
	 * Удаляет временные данные
	 *
	 * @param unknown_type $sKey
	 * @return unknown
	 */
	public function DeleteTmp($sKey) {
		return $this->oMapper->DeleteTmp($sKey);
	}
	/**
	 * Получает список связей OpenID пользователя
	 *
	 * @param unknown_type $sUserId
	 * @return unknown
	 */
	public function GetOpenIdByUser($sUserId) {
		if (!is_string($sUserId)) {
			return false;
		}
		return $this->oMapper->GetOpenIdByUser($sUserId);
	}
}
?>