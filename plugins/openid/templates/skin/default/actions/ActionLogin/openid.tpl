{assign var="noSidebar" value=true}
{include file='header.tpl'}

<link rel="stylesheet" type="text/css" href="{$aTemplateWebPathPlugin.openid}css/style.css" media="all" />

<div id="vk_api_transport"></div>
<script src="http://vkontakte.ru/js/api/openapi.js" type="text/javascript" charset="windows-1251"></script>

<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>

<div class="openid-block">
	<h1 class="openid-header">{$aLang.plugin.openid.enter_title} <img src="{$aTemplateWebPathPlugin.openid}img/openid.png" alt="openid" class="openid-img" title="{$aLang.openid}" alt="{$aLang.openid}"/></h1>
	
	<form method="post" action="{router page='login'}openid/enter/" name="fopenid" id="openid_form">
		<div style="overflow: hidden; zoom: 1;">
			<input type="text" style="float: left" class="openid-text" maxlength="255" name="open_login" id="open_login" />
			<input type="hidden" name="submit_open_login" id="submit_open_login_hidden" value="go"/>
			<input type="hidden" value="{$_aRequest.return}" name="return" />
			<a href="#" class="openid-login" onclick="getEl('openid_form').submit(); return false;"><span>{$aLang.plugin.openid.enter}</span></a>
		</div>
		
		<div class="openid-services">
			<p>{$aLang.plugin.openid.choose_service}</p>

<!--			<a href="javascript: openid_fb()"><img src="{$aTemplateWebPathPlugin.openid}img/openid_fb.png" alt="facebook" width="154px" height="22px" /></a>
			<a href="javascript: openid_twitter()"><img src="{$aTemplateWebPathPlugin.openid}img/openid_twitter.png" alt="twitter" width="151px" height="24px" /></a>
			<br> -->
			<a href="javascript: openid_yandex()"><img src="{$aTemplateWebPathPlugin.openid}img/openid_yandex.png" alt="yandex" width="47px" height="21px" /></a>
			<a href="javascript: openid_google()"><img src="{$aTemplateWebPathPlugin.openid}img/openid_google.png" class="google"  alt="google" width="63px" height="21px" /></a>
			<a href="javascript: openid_rambler()"><img src="{$aTemplateWebPathPlugin.openid}img/openid_rambler.png" alt="rambler" width="84px" height="21px" /></a>
		<!--	<a href="javascript: openid_vk()"><img src="{$aTemplateWebPathPlugin.openid}img/openid_vk.png" alt="vkontakte" width="84px" height="21px" /></a> -->
		</div>					
	</form>
</div>
		
		
<script language="JavaScript" type="text/javascript">
var sVkTransportPath='{cfg name='plugin.openid.vk.transport_path'}';
var iVkAppId='{cfg name='plugin.openid.vk.id'}';
var iFbAppId='{cfg name='plugin.openid.fb.id'}';
var sVkLoginPath='{$aRouter.login}'+'openid/vk/';
var sFbLoginPath='{$aRouter.login}'+'openid/fb/';
var sTwitterLoginPath='{$aRouter.login}'+'openid/twitter/?authorize=1';
{literal}
	function getEl(id) {
		return document.getElementById(id);
	}

	function openid_yandex() {
		getEl('open_login').value='openid.yandex.ru';		
		getEl('openid_form').submit();
	}
	
	function openid_rambler() {
		getEl('open_login').value='rambler.ru';		
		getEl('openid_form').submit();
	}
	
	function openid_google() {
		getEl('open_login').value='https://www.google.com/accounts/o8/id';		
		getEl('openid_form').submit();
	}
	
	function openid_vk() {		
		VK.Auth.getLoginStatus(function(response) {
			if (response.session) {
				window.location = sVkLoginPath;
			} else {
				VK.Auth.login(function(response) {
					if (response.session) {
						window.location = sVkLoginPath;
					}
				},VK.access.FRIENDS);				
			}
		});
	}
	
	function openid_fb() {		
		FB.getLoginStatus(function(response) {
			if (response.session) {
				window.location = sFbLoginPath;
			} else {
				//FB.login(null,{scope:'read_stream,publish_stream,offline_access,email'});
				FB.login(function(response) {
					console.log('fsdfs',response);
					if (response.authResponse) {
						window.location = sFbLoginPath;
					}
				},{scope:'read_stream,publish_stream,offline_access,email'});
			}
		});
	}
	
	function openid_twitter() {
		window.location = sTwitterLoginPath;
	}
	
	VK.init({apiId: iVkAppId, nameTransportPath: sVkTransportPath});
	FB.init({appId: iFbAppId, status: true, cookie: true, xfbml: true});
		
</script>
{/literal}

{include file='footer.tpl'}