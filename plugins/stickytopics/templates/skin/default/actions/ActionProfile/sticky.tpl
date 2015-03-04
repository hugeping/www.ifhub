{assign var="sidebarPosition" value='left'}
{include file='header.tpl' menu='people'}

{include file='actions/ActionProfile/profile_top.tpl'}
{include file='menu.profile_created.tpl'}

{hook run='st_assign_filepath' sFilename='list_edit.tpl' assign='sTempPath'}
{include file=$sTempPath}

{include file='footer.tpl'}
