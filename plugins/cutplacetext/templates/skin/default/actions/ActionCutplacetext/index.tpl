{assign var="bNoSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl'}

  <div class="CutplacetextEditor">
    <h2 class="page-header">{$aLang.plugin.cutplacetext.Title}</h2>
    
    <form action="{router page='cutplacetext'}" method="post" enctype="application/x-www-form-urlencoded">
      <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
      <textarea name="cutplacetext" rows="10" class="input-text input-width-full">{$oConfig->GetValue("plugin.cutplacetext.Text_Source")|escape:'html'}</textarea>
      <br /><br />
      <input type="submit" name="submit_edit_text_content" value="{$aLang.plugin.cutplacetext.Submit}" class="button button-primary" />
    </form>
  </div>

{include file='footer.tpl'}