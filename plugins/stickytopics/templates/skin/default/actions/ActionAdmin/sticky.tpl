{assign var="noSidebar" value=true}
{include file='header.tpl'}

<h2 class='page-header'>{$aLang.plugin.stickytopics.edit_index_sticky}</h2>
{hook run='st_assign_filepath' sFilename='list_edit.tpl' assign='sTempPath'}
{include file=$sTempPath}

{include file='footer.tpl'}
