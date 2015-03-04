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
 * Обрабатывает авторизацию через OpenId
 *
 */
class PluginOpenid_ActionLogin extends ActionPlugin {
	/**
	 * Инициализация
	 *
	 * @return null
	 */
	public function Init() {
		/**
		 * Не пускаем авторизованных
		 */
		if ($this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('registration_is_authorization'),$this->Lang_Get('attention'));
			return Router::Action('error');
		}
	}

	protected function RegisterEvent() {
		$this->AddEventPreg('/^login$/i','/^$/i','EventLogin');
		$this->AddEventPreg('/^login$/i','/^enter$/i','/^(finish)?$/i','EventOpenId');
		$this->AddEventPreg('/^login$/i','/^data$/i','/^$/i','EventData');
		$this->AddEventPreg('/^login$/i','/^vk$/i','/^$/i','EventVk');
		$this->AddEventPreg('/^login$/i','/^fb$/i','/^$/i','EventFacebook');
		$this->AddEventPreg('/^login$/i','/^twitter$/i','/^$/i','EventTwitter');
		$this->AddEventPreg('/^login$/i','/^confirm$/i','/^$/i','EventConfirmMail');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	/**
	 * Отображение формы ввода Openid
	 *
	 */
	protected function EventLogin() {
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('openid');
	}

	/**
	 * Подтверждение email для связи с OpenId
	 */
	protected function EventConfirmMail() {
		/**
		 * Проверяем валидность ключа подтверждения почты
		 */
		if (!($oKey=$this->PluginOpenid_Openid_GetTmpByConfirmMailKey(getRequest('confirm_key')))) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.openid.confirm_mail_key_no_valid'));
			return Router::Action('error');
		}

		/**
		 * Если пользователь подтвердил связь с Openid
		 */
		if (getRequest('submit_confirm',null,'post')) {
			$this->Security_ValidateSendForm();
			/**
			 * А не занят ли уже Openid?
			 */
			if ($this->PluginOpenid_Openid_GetOpenId($oKey->getOpenid())) {
				$this->Message_AddErrorSingle($this->Lang_Get('plugin.openid.confirm_mail_busy'));
				return Router::Action('error');
			}
			/**
			 * Не занят ли адрес электронной почты
			 */
			if (!($oUser=$this->User_GetUserByMail($oKey->getConfirmMail()))) {
				$this->Message_AddErrorSingle($this->Lang_Get('password_reminder_bad_email'));
				return Router::Action('error');
			}
			/**
			 * Привязываем OpenId к аккаунту
			 */
			$oOpenId=Engine::GetEntity('PluginOpenid_Openid');
			$oOpenId->setUserId($oUser->getId());
			$oOpenId->setOpenid($oKey->getOpenid());
			$oOpenId->setDate(date("Y-m-d H:i:s"));
			$this->PluginOpenid_Openid_AddOpenId($oOpenId);
			/**
			 * Удаляем временные данные
			 */
			$this->PluginOpenid_Openid_DeleteTmp($oKey->getKey());
			setcookie('openidkey','',1,Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
			/**
			 * Авторизуем
			 */
			$this->User_Authorization($oUser);
			Router::Location(Config::Get('path.root.web').'/');

			/**
			 * Если пользователь отказался подтверждать связь с Openid
			 */
		} elseif (getRequest('submit_cancel',null,'post')) {
			$this->Security_ValidateSendForm();
			/**
			 * Удаляем временные данные
			 */
			$this->PluginOpenid_Openid_DeleteTmp($oKey->getKey());
			Router::Location(Config::Get('path.root.web').'/');
		}
		/**
		 * Отображаем форму подтверждения
		 */
		$this->Viewer_Assign('oKey',$oKey);
		/**
		 * Загружаем в шаблон e-mail полученный от OpenID провайдера
		 */
		if ($aData=@unserialize($oKey->getData()) and is_array($aData)) {
			if (isset($aData['mail'])) {
				$this->Viewer_Assign('sMailOpenId',$aData['mail']);
			}
		}
		$this->SetTemplateAction('confirm_mail');
	}

	/**
	 * Обработка дополнительных данных
	 *
	 */
	protected function EventData() {
		$this->SetTemplateAction('data');

		/**
		 * Проверяем наличие временного ключа в куках
		 */
		$bKeyValid=false;
		if (isset($_COOKIE['openidkey']) and $sKey=$_COOKIE['openidkey']) {
			if ($oKey=$this->PluginOpenid_Openid_GetTmp($sKey)) {
				if (strtotime($oKey->getDate())>=time()-Config::Get('plugin.openid.time_key_limit')) {
					// ключ валиден
					$bKeyValid=true;
				}
			}
		}
		/**
		 * Если ключ не валиден
		 */
		if (!$bKeyValid) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.openid.key_no_valid'));
			return Router::Action('error');
		}
		/**
		 * Если есть связь с OpenId, то авторизуем
		 */
		if ($oUser=$this->PluginOpenid_Openid_GetUserByOpenId($oKey->getOpenid())) {
			$this->User_Authorization($oUser);
			Router::Location(Config::Get('path.root.web').'/');
		}
		/**
		 * Устанавливаем дефолтное значение полей
		 */
		if (!getRequest('submit_data',null,'post') and !getRequest('submit_mail',null,'post')) {
			if ($aData=@unserialize($oKey->getData()) and is_array($aData)) {
				if (isset($aData['login'])) {
					$_REQUEST['login']=$aData['login'];
				}
				if (isset($aData['mail'])) {
					$_REQUEST['mail']=$aData['mail'];
				}
			}
		}
		/**
		 * Отправили форму с даными для нового пользователя
		 */
		if (getRequest('submit_data',null,'post')) {
			$bError=false;
			/**
			 * Проверка логина
			 */
			if (!func_check(getRequest('login'),'login',3,30)) {
				$this->Message_AddError($this->Lang_Get('registration_login_error'),$this->Lang_Get('error'));
				$bError=true;
			}
			/**
			 * Проверка занятости логина
			 */
			if (!$bError) {
				if ($this->User_GetUserByLogin(getRequest('login'))) {
					$this->Message_AddError($this->Lang_Get('registration_login_error_used'),$this->Lang_Get('error'));
					$bError=true;
				}
			}
			/**
			 * Проверка почты
			 */
			if ((Config::Get('plugin.openid.mail_required') or (!Config::Get('plugin.openid.mail_required') and getRequest('mail')) ) and !func_check(getRequest('mail'),'mail')) {
				$this->Message_AddError($this->Lang_Get('registration_mail_error'),$this->Lang_Get('error'));
				$bError=true;
			}
			/**
			 * Проверка занятости почты
			 */
			if (!$bError) {
				if (getRequest('mail') and $this->User_GetUserByMail(getRequest('mail'))) {
					$this->Message_AddError($this->Lang_Get('registration_mail_error_used'),$this->Lang_Get('error'));
					$bError=true;
				}
			}
			/**
			 * Если всё ок
			 */
			if (!$bError) {
				/**
				 * Создаем пользователя
				 */
				$oUser=Engine::GetEntity('User');
				$oUser->setLogin(getRequest('login'));
				$sPassword='';
				if (getRequest('mail')) {
					$oUser->setMail(getRequest('mail'));
					$sPassword=func_generator(7);
					$oUser->setPassword(func_encrypt($sPassword));
				} else {
					$oUser->setMail(null);
					$oUser->setPassword('');
				}
				$oUser->setDateRegister(date("Y-m-d H:i:s"));
				$oUser->setIpRegister(func_getIp());
				$oUser->setActivate(1);
				$oUser->setActivateKey(null);
				/**
				 * Регистрируем
				 */
				if ($this->User_Add($oUser)) {
					/**
					 * Отправляем уведомление если есть пароль
					 */
					if ($oUser->getPassword()) {
						$this->Notify_SendRegistration($oUser,$sPassword);
					}
					/**
					 * Создаём связь пользователя с OpenId
					 */
					$oOpenId=Engine::GetEntity('PluginOpenid_Openid');
					$oOpenId->setUserId($oUser->getId());
					$oOpenId->setOpenid($oKey->getOpenid());
					$oOpenId->setDate(date("Y-m-d H:i:s"));
					$this->PluginOpenid_Openid_AddOpenId($oOpenId);
					/**
					 * Удаляем временные данные
					 */
					$this->PluginOpenid_Openid_DeleteTmp($oKey->getKey());
					setcookie('openidkey','',1,Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
					/**
					 * Авторизуем
					 */
					$this->User_Authorization($oUser,true);
					Router::Location(Config::Get('path.root.web').'/');
				}
			}
			/**
			 * Отправили форму для существующего пользователя
			 */
		} elseif (getRequest('submit_mail',null,'post')) {
			/**
			 * Проверяем есть ли пользователь с таким email, если есть то отправляем ему код активации текущего OpenId
			 */
			$bError=false;
			if (!func_check(getRequest('mail'),'mail')) {
				$this->Message_AddError($this->Lang_Get('registration_mail_error'),$this->Lang_Get('error'));
				$bError=true;
			}
			if (!$bError) {
				if (!($oUser=$this->User_GetUserByMail(getRequest('mail')))) {
					$this->Message_AddError($this->Lang_Get('password_reminder_bad_email'),$this->Lang_Get('error'));
					$bError=true;
				}
			}

			if (!$bError) {
				/**
				 * Генерируем ключь подтверждения
				 */
				$oKey->setConfirmMail($oUser->getMail());
				$oKey->setConfirmMailKey(func_generator(32));
				$this->PluginOpenid_Openid_UpdateTmp($oKey);
				/**
				 * Отправляем уведомление с активацией
				 */
				$this->Notify_Send(
					$oUser,
					'notify.confirm_mail.tpl',
					$this->Lang_Get('plugin.openid.confirm_mail_subject'),
					array(
						'oKey'=>$oKey
					),
					__CLASS__
				);
				/**
				 * Показываем сообщение о том, что письмо отправлено
				 */
				$this->Message_AddErrorSingle($this->Lang_Get('plugin.openid.confirm_mail_send'));
				return Router::Action('error');
			}
		}
	}

	/**
	 * OpenId авторизация
	 *
	 */
	protected function EventOpenId() {
		$bFinish=false;
		if ($this->GetParam(1)) {
			$bFinish=true;
		}
		/**
		 * Путь обратного редиректа с сервера OpenID
		 */
		$sPathReturn=Router::GetPath('login').'openid/enter/finish/';
		/**
		 * Если сработал редирект
		 */
		if ($bFinish) {
			/**
			 * Проверяем корректность авторизации
			 */
			if ($aReturn=$this->PluginOpenid_Openid_Verify($sPathReturn) and $aReturn['status']) {
				$this->Message_AddNotice($aReturn['msg'],$this->Lang_Get('attention'));
				$sOpenId=$aReturn['id'];
				/**
				 * Небольшая страховка
				 */
				$sOpenId=preg_replace("@^vk_@i",'open_vk_',$sOpenId);
				/**
				 * Если такой open_id уже есть у пользователя то авторизуем его под ним
				 */
				if ($oUser=$this->PluginOpenid_Openid_GetUserByOpenId($sOpenId)) {
					$this->User_Authorization($oUser);
					Router::Location(Config::Get('path.root.web').'/');
				} else {
					/**
					 * Получаем дополнительные данные
					 */
					$aData=array();
					if (isset($aReturn['sreg']) and isset($aReturn['sreg']['nickname'])) {
						$aData['login']=$aReturn['sreg']['nickname'];
					}
					if (isset($aReturn['sreg']) and isset($aReturn['sreg']['email'])) {
						$aData['mail']=$aReturn['sreg']['email'];
					}
					if (isset($aReturn['ax']) and isset($aReturn['ax']['email'])) {
						$aData['mail']=$aReturn['ax']['email'];
					}
					/**
					 * Заполняем временную таблицу, пишем в куки ключ и перенаправляем на страницу ввода дополнительных данных
					 */
					$oTmp=Engine::GetEntity('PluginOpenid_Openid_Tmp');
					$oTmp->setKey(func_generator(32));
					$oTmp->setOpenid($sOpenId);
					$oTmp->setData(serialize($aData));
					$oTmp->setDate(date("Y-m-d H:i:s"));
					$this->PluginOpenid_Openid_AddTmp($oTmp);

					setcookie('openidkey',$oTmp->getKey(),time()+Config::Get('plugin.openid.time_key_limit'),Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
					Router::Location(Router::GetPath('login').'openid/data/');
				}
			} else {
				$this->Message_AddErrorSingle($aReturn['msg'],$this->Lang_Get('error'));
			}
		}
		/**
		 * Начало авторизации
		 */
		if (getRequest('submit_open_login')) {
			/**
			 * Здесь происходит редирект на сервер OpenID
			 */
			if (!is_string(getRequest('open_login')) or !$this->PluginOpenid_Openid_Login(getRequest('open_login'),$sPathReturn)) {
				$this->Message_AddErrorSingle($this->Lang_Get('plugin.openid.result_error'),$this->Lang_Get('error'));
			}
		}
		/**
		 * Шаблон
		 */
		$this->SetTemplateAction('openid');
	}

	/**
	 * Авторизация ВКонтакте
	 *
	 */
	protected function EventVk() {
		/**
		 * Читаем куку и проверяем подпись
		 */
		$sCookieName='vk_app_'.Config::Get('plugin.openid.vk.id');
		if (isset($_COOKIE[$sCookieName])) {
			/**
			 * Парсим параметры из куки
			 */
			$aVars=explode('&',$_COOKIE[$sCookieName]);
			$aParams=array(
				'expire'=>null,
				'mid'=>null,
				'secret'=>null,
				'sid'=>null,
				'sig'=>null,
			);
			foreach ($aVars as $sVar) {
				$aNV=explode('=',$sVar);
				if ($aNV and count($aNV)==2) {
					$aParams[$aNV[0]]=$aNV[1];
				}
			}
			/**
			 * Строим хеш для проверки валидности авторизации
			 */
			$sHash=md5("expire={$aParams['expire']}mid={$aParams['mid']}secret={$aParams['secret']}sid={$aParams['sid']}".Config::Get('plugin.openid.vk.secure_key'));
			/**
			 * Успешная авторизация
			 */
			if ($sHash==$aParams['sig']) {
				$sOpenId='vk_'.$aParams['mid'];
				/**
				 * Если уже есть связь с этим OpenID то авторизуем
				 */
				if ($oUser=$this->PluginOpenid_Openid_GetUserByOpenId($sOpenId)) {
					$this->User_Authorization($oUser);
					Router::Location(Config::Get('path.root.web').'/');
				} else {
					/**
					 * Связи нет
					 */
					$aData=array();
					/**
					 * Заполняем данные (логин)
					 */


					/**
					 * Заполняем временную таблицу, пишем в куки ключ и перенаправляем на страницу ввода дополнительных данных
					 */
					$oTmp=Engine::GetEntity('PluginOpenid_Openid_Tmp');
					$oTmp->setKey(func_generator(32));
					$oTmp->setOpenid($sOpenId);
					$oTmp->setData(serialize($aData));
					$oTmp->setDate(date("Y-m-d H:i:s"));
					$this->PluginOpenid_Openid_AddTmp($oTmp);

					setcookie('openidkey',$oTmp->getKey(),time()+Config::Get('plugin.openid.time_key_limit'),Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
					Router::Location(Router::GetPath('login').'openid/data/');
				}
			} else {
				setcookie($sCookieName,'',1,Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
				$this->Message_AddErrorSingle($this->Lang_Get('plugin.openid.result_error_vk'),$this->Lang_Get('error'));
			}
		}
		$this->SetTemplateAction('openid');
	}

	/**
	 * Авторизация через Facebook
	 *
	 */
	protected function EventFacebook() {
		/**
		 * Читаем куку и проверяем подпись
		 */
		$sCookieName='fbsr_'.Config::Get('plugin.openid.fb.id');
		if (isset($_COOKIE[$sCookieName]) and is_string($_COOKIE[$sCookieName])) {
			$aParams=$this->parse_signed_facebook($_COOKIE[$sCookieName],Config::Get('plugin.openid.fb.secret'));

			if ($aParams and isset($aParams['user_id'])) {
				$sOpenId='fb_'.$aParams['user_id'];
				/**
				 * Если уже есть связь с этим OpenID то авторизуем
				 */
				if ($oUser=$this->PluginOpenid_Openid_GetUserByOpenId($sOpenId)) {
					$this->User_Authorization($oUser);
					Router::Location(Config::Get('path.root.web').'/');
				} else {
					/**
					 * Связи нет
					 */
					$aData=array();
					/**
					 * Заполняем данные (логин)
					 */


					/**
					 * Заполняем временную таблицу, пишем в куки ключ и перенаправляем на страницу ввода дополнительных данных
					 */
					$oTmp=Engine::GetEntity('PluginOpenid_Openid_Tmp');
					$oTmp->setKey(func_generator(32));
					$oTmp->setOpenid($sOpenId);
					$oTmp->setData(serialize($aData));
					$oTmp->setDate(date("Y-m-d H:i:s"));
					$this->PluginOpenid_Openid_AddTmp($oTmp);

					setcookie('openidkey',$oTmp->getKey(),time()+Config::Get('plugin.openid.time_key_limit'),Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
					Router::Location(Router::GetPath('login').'openid/data/');
				}
			} else {
				setcookie($sCookieName,'',1,Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
				$this->Message_AddErrorSingle($this->Lang_Get('plugin.openid.result_error_fb'),$this->Lang_Get('error'));
			}
		}
		$this->SetTemplateAction('openid');
	}

	/**
	 * Авторизация через Twitter
	 *
	 */
	protected function EventTwitter() {

		if (getRequest('callback')) {
			if ($this->PluginOpenid_Oauth_VerifyTwitter() and $data=$this->PluginOpenid_Oauth_GetTwitter('account/verify_credentials')) {

				$sOpenId='twitter_'.$data->screen_name;

				/**
				 * Если уже есть связь с этим OpenID то авторизуем
				 */
				if ($oUser=$this->PluginOpenid_Openid_GetUserByOpenId($sOpenId)) {
					$this->User_Authorization($oUser);
					Router::Location(Config::Get('path.root.web').'/');
				} else {
					/**
					 * Связи нет
					 */
					$aData=array();
					/**
					 * Заполняем данные (логин)
					 */


					/**
					 * Заполняем временную таблицу, пишем в куки ключ и перенаправляем на страницу ввода дополнительных данных
					 */
					$oTmp=Engine::GetEntity('PluginOpenid_Openid_Tmp');
					$oTmp->setKey(func_generator(32));
					$oTmp->setOpenid($sOpenId);
					$oTmp->setData(serialize($aData));
					$oTmp->setDate(date("Y-m-d H:i:s"));
					$this->PluginOpenid_Openid_AddTmp($oTmp);

					setcookie('openidkey',$oTmp->getKey(),time()+Config::Get('plugin.openid.time_key_limit'),Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'));
					Router::Location(Router::GetPath('login').'openid/data/');
				}


			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('plugin.openid.result_error_twitter'),$this->Lang_Get('error'));
			}
		}

		if (getRequest('authorize')) {
			if (!$this->PluginOpenid_Oauth_LoginTwitter(Router::GetPath('login').'openid/twitter/?callback=1')) {
				$this->Message_AddErrorSingle($this->Lang_Get('plugin.openid.result_error_twitter'),$this->Lang_Get('error'));
			}
		}

		$this->SetTemplateAction('openid');
	}


	protected function parse_signed_facebook($signed_request, $secret) {
		list($encoded_sig, $payload) = explode('.', $signed_request, 2);

		// decode the data
		$sig = $this->base64_url_decode($encoded_sig);
		$data = json_decode($this->base64_url_decode($payload), true);

		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
			return null;
		}

		// check sig
		$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
		if ($sig !== $expected_sig) {
			return null;
		}

		return $data;
	}

	protected function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}
}
?>