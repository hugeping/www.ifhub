{assign var=view_module value=$oConfig->GetValue("plugin.shoutbox.view_module")}
{assign var=shoutbox_height value=$oConfig->GetValue("plugin.shoutbox.shoutbox_height")}
{assign var=view_controlls value=$oConfig->GetValue("plugin.shoutbox.view_controlls")}

<input type="hidden" id="security_sb_key" value="by hellcore">
<div id='shoutbox_module' class='{if $view_module == Block}sbmodule_block{else}sbmodule{/if}'>

	{if $view_controlls == top}
		{include file=$aTemplatePathPlugin.shoutbox|cat:'shoutbox_controlls.tpl'}
	{/if}

	<!--draw shouts-->
	<div id="scrollbar1">

		<div class="scrollbar" style="height:{$shoutbox_height};">
			<div class="track" style="height:{$shoutbox_height};">
				<div class="thumb" style="top: 0px; height: 30px;">
					<div class="end">
					</div>
				</div>
			</div>
		</div>

		<div class="viewport" style="height:{$shoutbox_height};">
			<div class="overview" style="top: 0px;">
				<div id="shoutbox_error_area"></div>
				<div id="shouts" style="top: 0px;">{$iUPIntervals}</div>
			</div>
		</div>
	</div>

	{if $view_controlls == bottom}
		{include file=$aTemplatePathPlugin.shoutbox|cat:'shoutbox_controlls.tpl'}
	{/if}

	<div style="float:right;color:gray"><span id="sbcp"></span></div>

</div>

{$aHTMLJSTemplate}