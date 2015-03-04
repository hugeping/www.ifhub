<div>
    {literal}
	<script type="application/javascript">
		jQuery(function($){
			$('.datepicker').datepicker({
				dateFormat:'yy-mm-dd',
				showAnim:'slideDown'
			});		
                        $('.google_pr').click(function(el){
                            var id = el.target.dataset.id;
                            ls.ajax(aRouter['admin']+'plugins/admintopic',{'topicid':id},function(response){
                                if(!response.bStateError){
                                    $(el.target).text(response.pr);
                                }
                            },{type:'GET'});
                            return false;
                        });
		});
		function clearFilter(){
			jQuery('#from_date').val('clear');
			jQuery('#to_date').val('clear');
			return true;
		}
	</script>
	{/literal}
	<div style="float:left;">
		{include file="$sTemplatePath/inc.paging.tpl"}
	</div>
	<div style="float:right">
		<form action="{router page=admin}plugins/admintopic" method="POST" enctype="multipart/form-data">
			<input id="from_date" type="text" class="datepicker" name="from_date" size="10" value="{if $aDateFilter['from_date']}{$aDateFilter['from_date']}{/if}">
			&nbsp;-&nbsp;
			<input id="to_date" type="text" class="datepicker" name="to_date" size="10" value="{if $aDateFilter['to_date']}{$aDateFilter['to_date']}{/if}">
			<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
			<input type="submit" class="btn" value="Filter" />
			<input type="submit" class="btn" value="Clean" onclick="clearFilter()" />
		</form>
	</div>
</div>
<div class="table-container">
    <table class="table table-striped table-bordered table-condensed users-list">
	<thead>
    <tr>
	{assign var="sort_type" value="`$_aRequest.sort`"}
        {assign var="rev" value="`$_aRequest.rev`"}
        {if $sort_type eq ""}
            {assign var="sort_type" value="`$oConfig->GetValue('plugin.admintopic.default_sort')`"}
        {/if}
        {if $rev eq ""}
        	{assign var="rev" value="`$oConfig->GetValue('plugin.admintopic.default_sort_direction')`"}
        {/if}
    	<th>
    		<a href="{router page=admin}plugins/admintopic?sort=topic_id&rev={if $sort_type eq 'topic_id'}{1-$rev}{else}{$rev}{/if}">
    			{$aLang.plugin.admintopic.table_id}
    		</a>
    	</th>
    	<th>
    		<a href="{router page=admin}plugins/admintopic?sort=topic_type&rev={if $sort_type eq 'topic_type'}{1-$rev}{else}{$rev}{/if}">
    			{$aLang.plugin.admintopic.table_type}
    		</a>
    	</th>
    	<th>
    		<a href="{router page=admin}plugins/admintopic?sort=topic_title&rev={if $sort_type eq 'topic_title'}{1-$rev}{else}{$rev}{/if}">
    			{$aLang.plugin.admintopic.table_title}
    		</a>
    	</th>
    	<th>
    		<a href="{router page=admin}plugins/admintopic?sort=topic_date_add&rev={if $sort_type eq 'topic_date_add'}{1-$rev}{else}{$rev}{/if}">
    			{$aLang.plugin.admintopic.table_added}
    		</a>
    	</th>
    	<th>{$aLang.plugin.admintopic.table_author}</th>
    	<th>
    		<a href="{router page=admin}plugins/admintopic?sort=topic_publish&rev={if $sort_type eq 'topic_publish'}{1-$rev}{else}{$rev}{/if}">
    			{$aLang.plugin.admintopic.table_published}
    		</a><br/>
    		<a href="{router page=admin}plugins/admintopic?sort=topic_publish_index&rev={if $sort_type eq 'topic_publish_index'}{1-$rev}{else}{$rev}{/if}">
    			{$aLang.plugin.admintopic.table_index}
    		</a><br/>
    		<a href="{router page=admin}plugins/admintopic?sort=topic_publish_draft&rev={if $sort_type eq 'topic_publish_draft'}{1-$rev}{else}{$rev}{/if}">
    			{$aLang.plugin.admintopic.table_draft}
    		</a>
    	</th>
    	<th>
    		<a href="{router page=admin}plugins/admintopic?sort=topic_rating&rev={if $sort_type eq 'topic_rating'}{1-$rev}{else}{$rev}{/if}">
    			{$aLang.plugin.admintopic.table_rating}
    		</a>
    	</th>
    	<th>
    		<a href="{router page=admin}plugins/admintopic?sort=topic_count_comment&rev={if $sort_type eq 'topic_count_comment'}{1-$rev}{else}{$rev}{/if}">
    			{$aLang.plugin.admintopic.table_comments}
    		</a>
    	</th>
    	<th>{$aLang.plugin.admintopic.google_pagerank}</th>
    	<th>{$aLang.plugin.admintopic.table_manage}</th>
    </tr>
    </thead>
	{foreach from=$aAllTopics item=oTopic}
    <tr>
    	<td>{$oTopic->getId()}</td>
    	<td>{$oTopic->getType()}</td>
    	<td>
    		<a href="{$oTopic->getUrl()}">{$oTopic->getTitle()}</a>:
			{if $oConfig->GetValue('plugin.admintopic.show_stats')}
    			<hr style="margin:1px">
				<p>{$aLang.plugin.admintopic.table_wordcount}: <strong>{$oTopic->getText()|count_words}</strong><br/>
				{assign var=nausea value=$oTopic->getNausea()}
				{$aLang.plugin.admintopic.classic_n}: <strong>{$nausea['classic']}</strong><br/>
				{$aLang.plugin.admintopic.academic_n}: <strong>{$nausea['academic']}</strong>%</p>
			{/if}
    	</td>
    	<td>{$oTopic->getDateAdd()}</td>
    	<td>{$oTopic->getUser()->getLogin()}</td>
    	<td>
    		<span class="{if $oTopic->getPublish()}icon-ok{else}icon-remove{/if}"></span>{$aLang.plugin.admintopic.table_published}<br/>
	    	<span class="{if $oTopic->getPublishIndex()}icon-ok{else}icon-remove{/if}"></span>{$aLang.plugin.admintopic.table_index}<br/>
    		<span class="{if $oTopic->getPublishDraft()}icon-ok{else}icon-remove{/if}"></span>{$aLang.plugin.admintopic.table_draft}
    	</td>
    	<td>{$oTopic->getRating()}</td>
    	<td>{$oTopic->getCountComment()}</td>
    	<td><a href="#" data-id="{$oTopic->getId()}" class="google_pr">Get PR</a></td>
    	<td>
    		<a href="{cfg name='path.root.web'}/{$oTopic->getType()}/edit/{$oTopic->getId()}/" class="icon-edit" target="_blank"></a>&nbsp;
    		<a href="{router page='topic'}delete/{$oTopic->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" title="{$aLang.topic_delete}" onclick="return confirm('{$aLang.topic_delete_confirm}');" class="icon-remove"></a>
    	</td>
	</tr>
	{/foreach}
	</table>
</div>
