{include file='topic_part_header.tpl'}
    
<div class="topic-content text">
	{hook run='topic_content_begin' topic=$oTopic bTopicList=$bTopicList}
	
	{$oTopic->getText()}
	
	{hook run='topic_content_end' topic=$oTopic bTopicList=$bTopicList}
</div>
<div class="topic topic-type-games">
        <b>{$aLang.plugin.games.topic_field_link1}:</b> <a href="{$oTopic->getFieldLink1()}">{$oTopic->getFieldLink1()}</a>
</div>

{include file='topic_part_footer.tpl'}