{include file='header.tpl'}

<h3>{$aLang.plugin.shoutbox.historyTitle}</h3>

{include file='paging.tpl'}

<div id='shoutbox_module' class='sbmodule'>
	<table class="table sb_table">
		<tbody id="shoutsbody">
			{$aHTML}
		</tbody>
	</table>
</div>

{$aHTMLJSTemplate}

{include file='paging.tpl'}

{include file='footer.tpl'}