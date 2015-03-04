{assign var=view_module value=$oConfig->GetValue("plugin.shoutbox.view_module")}

{if $oUserCurrent}

<table class="table inputtable">
	<tbody>
		<form method="post" enctype="multipart/form-data" onsubmit="return false;">
			<tr>

				{if $view_module == Block}

					<td>
						<div style="margin-bottom:5px;">
							{include file=$aTemplatePathPlugin.shoutbox|cat:'shoutbox_query_field.tpl'}
						</div>
						<div>{include file=$aTemplatePathPlugin.shoutbox|cat:'shoutbox_forms.tpl'}</div>
					</td>

				{else}

					<td class="inline formleft">
						{include file=$aTemplatePathPlugin.shoutbox|cat:'shoutbox_query_field.tpl'}
					</td>

					<td nowrap="nowrap" class="inline formright">
						{include file=$aTemplatePathPlugin.shoutbox|cat:'shoutbox_forms.tpl'}
					</td>

				{/if}

			</tr>
		</form>
	</tbody>
</table>

{/if}