{assign var="sidebarPosition" value='left'}
{include file='header.tpl'}

{include file='menu.settings.tpl'}

<form action="" method="POST" enctype="multipart/form-data" class="wrapper-content">


	<fieldset>
		<legend>{$aLang.plugin.openid.menu_settings_title}</legend>


{if count($aOpenId)}

	{literal}
	<script language="JavaScript" type="text/javascript">
	function deleteOpenID(openid,obj) {
		
		ls.ajax(aRouter['settings']+'openid/ajaxdeleteopenid/', {openid:openid}, function(resp){
			if (resp) {
				if (resp.bStateError) {
					ls.msg.error(resp.sMsgTitle,resp.sMsg);
				} else {
					ls.msg.notice(resp.sMsgTitle,resp.sMsg);
					$(obj).parent().fadeOut();
				}
			} else {
				ls.msg.error('Error','Please try again later');
			}
		}.bind(this));
		return false;
	}
	</script>
	{/literal}

	<ul>
	{foreach from=$aOpenId item=oOpenId}
		<li>{$oOpenId->getOpenid()|escape:'html'} <a href="#" onclick="return deleteOpenID('{$oOpenId->getOpenid()|escape:'html'}',this);"><img src="{$aTemplateWebPathPlugin.openid}img/delete.png" alt="{$aLang.plugin.openid.menu_settings_delete}" title="{$aLang.plugin.openid.menu_settings_delete}"/></a></li>
	{/foreach}
	</ul>
{else}
	{$aLang.plugin.openid.menu_settings_empty}
{/if}

	</fieldset>
</form>



{include file='footer.tpl'}