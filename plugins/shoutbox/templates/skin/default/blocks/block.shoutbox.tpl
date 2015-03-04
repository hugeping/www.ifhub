{assign var=base_path value=$oConfig->GetValue("path.root.server")}
{assign var=view_module value=$oConfig->GetValue("plugin.shoutbox.view_module")}

{if $view_module == 'Block'}

<div class="block block-type-shoutbox" id="block_shoutbox">
	<header class="block-header sep">
		<h3>{$aLang.plugin.shoutbox.shoutboxTitle}</h3>

		<ul class="nav nav-pills">
				<li><a id="shoutupdate" class="js-shout-submit" onclick="ls.shoutbox.Update();" href="#">{$aLang.plugin.shoutbox.refresh}</a></li>
				<li><a class="js-shout-submit btn btn-mini" href="{router page='shoutbox'}history">{$aLang.plugin.shoutbox.history}</a></li>
		</ul>
	</header>
	
	<div class="block-content">
		<div>
			{include file=$aTemplatePathPlugin.shoutbox|cat:'shoutbox.tpl'}</div>
		<footer></footer>
	</div>
</div>

{/if}