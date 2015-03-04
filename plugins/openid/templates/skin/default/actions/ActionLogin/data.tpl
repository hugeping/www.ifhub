{assign var="noSidebar" value=true}
{include file='header.tpl'}


<link rel="stylesheet" type="text/css" href="{$aTemplateWebPathPlugin.openid}css/style.css" media="all" />

<div class="openid-block step-two">
	<h1 class="openid-header">{$aLang.plugin.openid.continue}</h1>

	<ul>
		<li {if !$_aRequest.submit_mail}class="active"{/if} id="li_data"><a href="javascript:showFormData()" ><span>{$aLang.plugin.openid.type_new}</span></a></li>
		<li id="li_mail" {if $_aRequest.submit_mail}class="active"{/if}><a href="javascript:showFormMail()" ><span>{$aLang.plugin.openid.type_exists}</span></a></li>
	</ul>
	
	
	<form method="post" action="{router page='login'}openid/data/" id="form_data" {if $_aRequest.submit_mail}style="display: none;"{/if}>			
		<p>
			<label>{$aLang.plugin.openid.login}</label>
			<input type="text" class="openid-text" maxlength="50" name="login" value="{$_aRequest.login}" />
		</p>
		<p style="margin-bottom: 18px;">
			{if $oConfig->GetValue('plugin.openid.mail_required')}
				<label>{$aLang.plugin.openid.mail}</label>
			{else}
				<a href="javascript:toggleMail()" class="openid-mail">{$aLang.plugin.openid.mail_toggle}</a>
			{/if}
			<input type="text" class="openid-text" style="margin-top: 5px;{if $oConfig->GetValue('plugin.openid.mail_required') or $_aRequest.mail}display: block;{else}display: none;{/if}" maxlength="50" name="mail" value="{$_aRequest.mail}" id="mail"/>
		</p>
		<input type="hidden" value="go"  name="submit_data">
		<a href="#" class="openid-ok" name="submit_data"  onclick="getEl('form_data').submit(); return false;"><span>{$aLang.plugin.openid.type_new_send}</span></a>
	</form>

	
	<form method="post" action="{router page='login'}openid/data/"  id="form_mail" {if $_aRequest.submit_mail}style="display: block;"{else}style="display: none;"{/if}>							
		<p>
			<label>{$aLang.plugin.openid.mail}</label>
			<input type="text" class="openid-text" maxlength="50" name="mail" value="{$_aRequest.mail}" />	
		</p>
		<input type="hidden" value="go"  name="submit_mail">
		<a href="#" class="openid-ok"  onclick="getEl('form_mail').submit(); return false;"><span>{$aLang.plugin.openid.type_exists_send}</span></a>
	</form>
</div>	
					
{literal}
<script language="JavaScript" type="text/javascript">
	function getEl(id) {
		return document.getElementById(id);
	}

	function showFormData() {
		getEl('form_mail').style.display='none';
		getEl('form_data').style.display='block';
		getEl('li_data').className='active';
		getEl('li_mail').className='';		
	}
	
	function showFormMail() {
		getEl('form_data').style.display='none';
		getEl('form_mail').style.display='block';
		getEl('li_data').className='';
		getEl('li_mail').className='active';		
	}
	
	function toggleMail(id) {
		if (getEl('mail').style.display=='none') {
			getEl('mail').style.display='block';
		} else {
			getEl('mail').style.display='none';
		}
	}		
</script>
{/literal}

{include file='footer.tpl'}