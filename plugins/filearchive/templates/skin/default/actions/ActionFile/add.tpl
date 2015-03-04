{if $sEvent=='add'}
    {if Config::Get('view.skin') == 'social' || Config::Get('view.skin') == 'mobile'}
	{include file='header.tpl' menu='create'}
    {else}
	{include file='header.tpl' menu_content='create'}
    {/if}
{else}
	{include file='header.tpl'}
	<h2 class="page-header">{$aLang.plugin.filearchive.topic_file_edit}</h2>
{/if}


{include file='editor.tpl'}

{hook run='add_topic_file_begin'}


<form action="" method="POST" enctype="multipart/form-data" id="form-topic-add" class="wrapper-content">
	{hook run='form_add_topic_file_begin'}

	<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />


	<p><label for="blog_id">{$aLang.topic_create_blog}</label>
	<select name="blog_id" id="blog_id" onChange="ls.blog.loadInfo(jQuery(this).val());" class="input-width-full">
		<option value="0">{$aLang.topic_create_blog_personal}</option>
		{foreach from=$aBlogsAllow item=oBlog}
			<option value="{$oBlog->getId()}" {if $_aRequest.blog_id==$oBlog->getId()}selected{/if}>{$oBlog->getTitle()|escape:'html'}</option>
		{/foreach}
	</select>
	<small class="note">{$aLang.topic_create_blog_notice}</small></p>


	<script type="text/javascript">
		jQuery(document).ready(function($){
			ls.blog.loadInfo($('#blog_id').val());
		});
    </script>


	<p><label for="topic_title">{$aLang.topic_create_title}:</label>
	<input type="text" id="topic_title" name="topic_title" value="{$_aRequest.topic_title}" class="input-text input-width-full" />
	<small class="note">{$aLang.topic_create_title_notice}</small></p>


{if $bEdit}
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $("#topic_file_new").click(function () {
                if ($(this).is(':checked')) {
                    $('#topic_file_form').slideDown(300);
                } else {
                    $('#topic_file_form').slideUp();
                }
            });
        });
    </script>
    <p>{$aLang.plugin.filearchive.topic_file_create}: <a href="{$_aRequest.topic_file_url}" target="_blank">"{$_aRequest.topic_file_name}" {($_aRequest.topic_file_size / 1024)|string_format:$aLang.plugin.filearchive.topic_file_size}</a><br/>
    <label><input type="checkbox" id="topic_file_new" name="topic_file_new" class="input-checkbox">{$aLang.plugin.filearchive.topic_file_change}</label></p>
{/if}
    <p id="topic_file_form" style="display: {if $bEdit}none{else}block{/if};"><label for="topic_file">{$aLang.plugin.filearchive.topic_file_create}:</label>
    <input type="hidden" name="MAX_FILE_SIZE" value="{Config::Get('plugin.filearchive.max_size')}" />
	<input type="file" id="topic_file" name="topic_file" value="{$_aRequest.topic_file}" class="input-text input-width-full" />
	<small class="note">{(Config::Get('plugin.filearchive.max_size') / 1024)|string_format:$aLang.plugin.filearchive.topic_file_create_notice}</small></p>


	<label for="topic_text">{Config::Get('plugin.filearchive.text_max_length')|string_format:$aLang.plugin.filearchive.topic_file_create_text}:</label>
	<textarea name="topic_text" id="topic_text" rows="10" class="input-text input-width-full mce-editor markitup-editor input-width-full">{$_aRequest.topic_text}</textarea>


	<p><label for="topic_tags">{$aLang.topic_create_tags}:</label>
	<input type="text" id="topic_tags" name="topic_tags" value="{$_aRequest.topic_tags}" class="input-text input-width-full autocomplete-tags-sep" /><br />
	<small class="note">{$aLang.topic_create_tags_notice}</small></p>


	<p><label for="topic_forbid_comment">
	<input type="checkbox" id="topic_forbid_comment" name="topic_forbid_comment" class="input-checkbox" value="1" {if $_aRequest.topic_forbid_comment==1}checked{/if} />
	{$aLang.topic_create_forbid_comment}</label>
	<small class="note">{$aLang.topic_create_forbid_comment_notice}</small></p>


	{if $oUserCurrent->isAdministrator()}
		<p><label for="topic_publish_index">
		<input type="checkbox" id="topic_publish_index" name="topic_publish_index" class="input-checkbox" value="1" {if $_aRequest.topic_publish_index==1}checked{/if} />
		{$aLang.topic_create_publish_index}</label>
		<small class="note">{$aLang.topic_create_publish_index_notice}</small></p>
	{/if}

	<input type="hidden" name="topic_type" value="file" />

	{hook run='form_add_topic_file_end'}


	<button type="submit"  name="submit_topic_publish" id="submit_topic_publish" class="button button-primary fl-r">{$aLang.topic_create_submit_publish}</button>
	<button type="submit"  name="submit_preview" onclick="jQuery('#text_preview').parent().show(); ls.topic.preview('form-topic-add','text_preview'); return false;" class="button">{$aLang.topic_create_submit_preview}</button>
	<button type="submit"  name="submit_topic_save" id="submit_topic_save" class="button">{$aLang.topic_create_submit_save}</button>
</form>

<div class="topic-preview" id="text_preview"></div>

{hook run='add_topic_file_end'}


{include file='footer.tpl'}