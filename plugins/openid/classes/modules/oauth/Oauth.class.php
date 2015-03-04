<?
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
 * OAuth
 *
 */
class PluginOpenid_ModuleOauth extends Module {
	
	protected $OAuthTwitter;
	
	/**
	 * Инициализация модуля
	 *
	 */
	public function Init() {
		require_once(Plugin::GetPath(__CLASS__).'classes/lib/external/OAuth/twitteroauth/twitteroauth.php');
	}
		
	public function LoginTwitter($sPath) {		
		$OAuth = new TwitterOAuth(Config::Get('plugin.openid.twitter.token'), Config::Get('plugin.openid.twitter.token_secret'));		
		if (!($aRequestToken = $OAuth->getRequestToken($sPath))) {
			return false;
		}		
		$_SESSION['twitter_oauth_token'] = $sToken = $aRequestToken['oauth_token'];
		$_SESSION['twitter_oauth_token_secret'] = $aRequestToken['oauth_token_secret'];
		$_SESSION['oauth_return_path'] = $sPath;
				
		switch ($OAuth->http_code) {
			case 200:				
				$sUrl = $OAuth->getAuthorizeURL($sToken);
				header('Location: '.$sUrl);
				break;
			default:
				return false;
		}
	}
	
	public function VerifyTwitter() {		
		if (isset($_REQUEST['twitter_oauth_token']) && $_SESSION['twitter_oauth_token'] !== $_REQUEST['twitter_oauth_token']) {
			//$_SESSION['twitter_oauth_status'] = 'oldtoken';
			unset($_SESSION['twitter_oauth_token']);
			return false;
		}
		
		if (!isset($_SESSION['twitter_oauth_token']) or !isset($_SESSION['twitter_oauth_token_secret'])) {
			return false;
		}
		
		$OAuth = new TwitterOAuth(Config::Get('plugin.openid.twitter.token'), Config::Get('plugin.openid.twitter.token_secret'), $_SESSION['twitter_oauth_token'], $_SESSION['twitter_oauth_token_secret']);		
		$aAccessToken = $OAuth->getAccessToken($_REQUEST['oauth_verifier']);		
		$_SESSION['twitter_access_token'] = $aAccessToken;

		unset($_SESSION['twitter_oauth_token']);
		unset($_SESSION['twitter_oauth_token_secret']);
		
		if (200 == $OAuth->http_code) {
			return true;
		} else {
			unset($_SESSION['twitter_access_token']);
		}
		return false;
	}
	
	protected function CheckTwitter() {
		if (empty($_SESSION['twitter_access_token']) || empty($_SESSION['twitter_access_token']['oauth_token']) || empty($_SESSION['twitter_access_token']['oauth_token_secret'])) {
			unset($_SESSION['twitter_access_token']);
			return false;
		}		
		$aAccessToken = $_SESSION['twitter_access_token'];		
		$OAuth = new TwitterOAuth(Config::Get('plugin.openid.twitter.token'), Config::Get('plugin.openid.twitter.token_secret'), $aAccessToken['oauth_token'], $aAccessToken['oauth_token_secret']);
		if ($OAuth) {
			$this->OAuthTwitter=$OAuth;
			return true;
		}
		return false;
	}
	
	public function GetTwitter($sCmd,$aParams=array()) {
		if (!$this->CheckTwitter()) {
			return false;
		}
		return $this->OAuthTwitter->get($sCmd);
	}
}
?>