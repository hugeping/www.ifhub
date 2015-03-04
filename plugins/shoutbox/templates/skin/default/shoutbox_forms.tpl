{assign var=view_module value=$oConfig->GetValue("plugin.shoutbox.view_module")}

<div style="float:right;">

	<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}">
	
	<input type="hidden" id="shoutlastid" value="-1">

	<button class="js-shout-submit btn btn-mini btn-success" onclick="ls.shoutbox.Add(jQuery('#shouttext').val());">{$aLang.plugin.shoutbox.addPost}</button>

	{if $view_module == 'HomePage'}
		<button id="shoutupdate" class="js-shout-submit btn btn-mini" onclick="ls.shoutbox.Update();">{$aLang.plugin.shoutbox.refresh}</button>

		{if $oConfig->GetValue("plugin.shoutbox.allow_view_history")}
			<a class="js-shout-submit btn btn-mini" href="{router page='shoutbox'}history">{$aLang.plugin.shoutbox.history}</a>
		{/if}
	{/if}

	<select id="shoutswitcher" class="btn btn-mini" onchange="ls.shoutbox.SetInterval(this.value)">
		<option value="0" {if $iUPInterval==0}selected{/if}>{$aLang.plugin.shoutbox.disabled}</option>
		<option value="10" {if $iUPInterval==10}selected{/if}>{$aLang.plugin.shoutbox.sec10}</option>
		<option value="30" {if $iUPInterval==30}selected{/if}>{$aLang.plugin.shoutbox.sec30}</option>
		<option value="60" {if $iUPInterval==60}selected{/if}>{$aLang.plugin.shoutbox.min1}</option>
		<option value="300" {if $iUPInterval==300}selected{/if}>{$aLang.plugin.shoutbox.min5}</option>
	</select>
	
</div>