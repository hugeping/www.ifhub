{include file='topic_part_header.tpl'}
    
<div class="topic-content text">
	{hook run='topic_content_begin' topic=$oTopic bTopicList=$bTopicList}

	{if $bTopicList}
		{$oTopic->getTextShort()}

		{if $oTopic->getTextShort()!=$oTopic->getText()}
			<br/>
			<a href="{$oTopic->getUrl()}#cut" title="{$aLang.topic_read_more}">
				{if $oTopic->getCutText()}
					{$oTopic->getCutText()}
				{else}
					{$aLang.topic_read_more} &rarr;
				{/if}
			</a>
		{/if}
	{else}
		{$oTopic->getText()}
	{/if}
	
	{hook run='topic_content_end' topic=$oTopic bTopicList=$bTopicList}
</div>
<div class="topic topic-type-engines">
	<div>
        <b>{$aLang.plugin.engines.topic_field_link1}: </b><a href="{$oTopic->getFieldLink1()}">{$oTopic->getFieldLink1()}</a>
        </div><div>
        <b>{$aLang.plugin.engines.topic_field_string1}: </b>{$oTopic->getFieldString1()|escape:'html'}
        </div>
</div>

{include file='topic_part_footer.tpl'}