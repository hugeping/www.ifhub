{include file='header.tpl'}
{include file='menu.blog_edit.tpl'}

{hook run='st_assign_filepath' sFilename='list_edit.tpl' assign='sTempPath'}
{include file=$sTempPath}

{include file='footer.tpl'}