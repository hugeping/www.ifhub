{if count($aTopic)>0}
<table class="table table-stickytopics-list">
	<thead>
		<tr>
			<th class='actions'>{$aLang.plugin.stickytopics.admin_table_actions}</th>
			<th class='title'>{$aLang.plugin.stickytopics.admin_table_topic_title}</th>
		</tr>
	</thead>
	
	<tbody>
		{foreach from=$aTopic item=oTopic name=fr}
			<tr class='{if $bStickyList}st_stickytopic{else}st_foundtopic{/if}' id='stickytopic_{$oTopic->getId()}'>
				<td>
{hook run='st_assign_webpath' sFilename='images/add.png' assign='sTempPath'}
					<a class='st_add' {if $bStickyList}style='visibility: hidden;'{/if} title='{$aLang.plugin.stickytopics.admin_table_action_add}' href='#' onclick='ls.stickytopics.addTopic({$oTopic->getId()}); return false;'><img src='{$sTempPath}'/></a>
{hook run='st_assign_webpath' sFilename='images/totop_16.png' assign='sTempPath'}
					<a class='st_up' {if $smarty.foreach.fr.first || !$bStickyList}style='visibility: hidden;'{/if} title='{$aLang.plugin.stickytopics.admin_table_action_totop}' href='#' onclick='ls.stickytopics.moveTopic({$oTopic->getId()},-1); return false;'><img src='{$sTempPath}'/></a>
{hook run='st_assign_webpath' sFilename='images/tobottom_16.png' assign='sTempPath'}
					<a class='st_down' {if $smarty.foreach.fr.last || !$bStickyList}style='visibility: hidden;'{/if} title='{$aLang.plugin.stickytopics.admin_table_action_tobottom}' href='#' onclick='ls.stickytopics.moveTopic({$oTopic->getId()},1); return false;'><img src='{$sTempPath}'/></a>
{hook run='st_assign_webpath' sFilename='images/delete.png' assign='sTempPath'}
					<a class='st_delete' {if !$bStickyList}style='visibility: hidden;'{/if} title='{$aLang.plugin.stickytopics.admin_table_action_delete}' href='#' onclick='ls.stickytopics.deleteTopic({$oTopic->getId()}); return false;'><img src='{$sTempPath}'/></a>
				</td>
				<td><a href="{$oTopic->getUrl()}" target="_blank">({$oTopic->getId()}) {$oTopic->getTitle()|escape:'html'}</a></td>
			</tr>
		{/foreach}
	</tbody>
		<tr>
			<th class='actions'></th>
			<th class='title'></th>
		</tr>
</table>
{else}
{$aLang.plugin.stickytopics.topics_not_found}
{/if}