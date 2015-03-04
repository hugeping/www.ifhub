<script type="text/javascript">
ls.stickytopics.targetType='{$sTargetType}';
ls.stickytopics.targetId={$iTargetId};
</script>

<div id='stickytopics_list'>
{hook run='st_assign_filepath' sFilename='topic_list.tpl' assign='sTempPath'}
{include file=$sTempPath bStickyList=true}
</div>

<form method="post" enctype="multipart/form-data" class="stickytopics">
	<p><label for="search_topic">{$aLang.plugin.stickytopics.search_topic_title}:</label>
	<input type="text" id="search_topic" name="search_topic" value="" class="input-text input-width-full" />
	<small class="note">{$aLang.plugin.stickytopics.search_topic_title_note}</small></p>
	<button type="submit"  name="submit_blog_add" class="button button-primary" onclick='ls.stickytopics.findTopics($("#search_topic").val(),1);return false;'>{$aLang.plugin.stickytopics.search_find}</button>
</form>

<div id='stickytopics_find_list'></div>

