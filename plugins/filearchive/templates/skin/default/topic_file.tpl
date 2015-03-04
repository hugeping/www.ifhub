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


{if !$bTopicList}
<a name="download"></a>
<div style="border: 1px dashed #CDCDCD; margin: 10px 0px; padding: 5px;">
{assign var="bOnlyUsers" value=Config::Get('plugin.filearchive.only_users')}
{assign var="bUseLimit" value=Config::Get('plugin.filearchive.use_limit')}
{assign var="iLimitRating" value=Config::Get('plugin.filearchive.limit_rating')}
{if !$oUserCurrent && $bOnlyUsers}
	<a href="{router page='registration'}">{$aLang.plugin.filearchive.topic_file_access_denied}</a>
{else}
    {if !$bOnlyUsers || ($bOnlyUsers && $oUserCurrent && (!$bUseLimit || ($bUseLimit && ($oUserCurrent->getRating()>=$iLimitRating || $oUserCurrent->getIsAdministrator()))))}
	<a href="{$oTopic->getDownloadUrl()}" title="{$aLang.plugin.filearchive.topic_file_downloads}: {$oTopic->getFileDownloads()}">{$aLang.plugin.filearchive.topic_file_download} "{$oTopic->getFileName()}" {($oTopic->getFileSize() / 1024)|string_format:$aLang.plugin.filearchive.topic_file_size}</a>
    {else}
	{$aLang.plugin.filearchive.topic_file_limit_access_denied}
    {/if}
{/if}
</div>
{/if}


{include file='topic_part_footer.tpl'}