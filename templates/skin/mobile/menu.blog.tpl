{if $sMenuItemSelect=='index'}
	<ul class="nav-foldable">
		<li {if $sMenuSubItemSelect=='good'}class="active"{/if}><a href="{cfg name='path.root.web'}/">{$aLang.blog_menu_all_good}</a></li>
		<li {if $sMenuSubItemSelect=='new'}class="active"{/if}>
			<a href="{router page='index'}newall/" title="{$aLang.blog_menu_top_period_all}">{$aLang.blog_menu_all_new}</a>
		</li>
		{if $iCountTopicsNew>0}
			<li><a href="{router page='index'}new/" title="{$aLang.blog_menu_top_period_24h}">{$aLang.blog_menu_all_new_24h} ({$iCountTopicsNew})</a></li>
		{/if}
		<li {if $sMenuSubItemSelect=='discussed'}class="active"{/if}><a href="{router page='index'}discussed/">{$aLang.blog_menu_all_discussed}</a></li>
		<li {if $sMenuSubItemSelect=='top'}class="active"{/if}><a href="{router page='index'}top/">{$aLang.blog_menu_all_top}</a></li>
	
		{if $oUserCurrent}
			<li {if $sMenuItemSelect=='feed'}class="active"{/if}>
				<a href="{router page='feed'}">{$aLang.userfeed_title}</a>
			</li>
		{/if}

		{hook run='menu_blog'}
		{hook run='menu_blog_index_item'}
	</ul>
{/if}

{if $sMenuItemSelect=='games'}
	<ul class="nav-foldable">
		<li {if $sMenuSubItemSelect=='good'}class="active"{/if}><a href="{cfg name='path.root.web'}/games/">{$aLang.blog_menu_collective_good}</a></li>
		<li {if $sMenuSubItemSelect=='new'}class="active"{/if}>
			<a href="{cfg name='path.root.web'}/games/newall/" title="{$aLang.blog_menu_top_period_all}">{$aLang.blog_menu_collective_new}</a>
			{if $iCountTopicsBlogNew>0} <a href="{cfg name='path.root.web'}/games/new/" class="new" title="{$aLang.blog_menu_top_period_24h}">+{$iCountTopicsBlogNew}</a>{/if}
		</li>
		<li {if $sMenuSubItemSelect=='discussed'}class="active"{/if}><a href="{cfg name='path.root.web'}/games/discussed/">{$aLang.blog_menu_collective_discussed}</a></li>
		<li {if $sMenuSubItemSelect=='top'}class="active"{/if}><a href="{cfg name='path.root.web'}/games/top/">{$aLang.blog_menu_collective_top}</a></li>
		<li {if $sMenuSubItemSelect=='views'}class="active"{/if}><a href="{cfg name='path.root.web'}/games/views/">{$aLang.plugin.views.views}</a></li>
	</ul>
{/if}

{if $sMenuItemSelect=='engines'}
	<ul class="nav-foldable">
		<li {if $sMenuSubItemSelect=='good'}class="active"{/if}><a href="{cfg name='path.root.web'}/engines/">{$aLang.blog_menu_collective_good}</a></li>
		<li {if $sMenuSubItemSelect=='new'}class="active"{/if}>
			<a href="{cfg name='path.root.web'}/engines/newall/" title="{$aLang.blog_menu_top_period_all}">{$aLang.blog_menu_collective_new}</a>
			{if $iCountTopicsBlogNew>0} <a href="{cfg name='path.root.web'}/engines/new/" class="new" title="{$aLang.blog_menu_top_period_24h}">+{$iCountTopicsBlogNew}</a>{/if}
		</li>
		<li {if $sMenuSubItemSelect=='discussed'}class="active"{/if}><a href="{cfg name='path.root.web'}/engines/discussed/">{$aLang.blog_menu_collective_discussed}</a></li>
		<li {if $sMenuSubItemSelect=='top'}class="active"{/if}><a href="{cfg name='path.root.web'}/engines/top/">{$aLang.blog_menu_collective_top}</a></li>
		<li {if $sMenuSubItemSelect=='views'}class="active"{/if}><a href="{cfg name='path.root.web'}/engines/views/">{$aLang.plugin.views.views}</a></li>
	</ul>
{/if}

{if $sPeriodSelectCurrent}
	<ul class="nav-foldable">
		<li {if $sPeriodSelectCurrent=='1'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=1">{$aLang.blog_menu_top_period_24h}</a></li>
		<li {if $sPeriodSelectCurrent=='7'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=7">{$aLang.blog_menu_top_period_7d}</a></li>
		<li {if $sPeriodSelectCurrent=='30'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=30">{$aLang.blog_menu_top_period_30d}</a></li>
		<li {if $sPeriodSelectCurrent=='all'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=all">{$aLang.blog_menu_top_period_all}</a></li>
	</ul>
{/if}