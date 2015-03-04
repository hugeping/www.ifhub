{foreach from=$aShouts item=aShout}

	{assign var="aUser" value=$aShout['author']}
	{assign var="aView" value=$aShout['view']}

	<tr id="sbpost {$aShout['id']}" class="{$aView['classes']}">
		
		<td class="uinfo_block">

			{if NOT $aShout['modpost']}
			<a target="_blank" href="{$aUser->getUserWebPath()}">
				<img src="{$aUser->getProfileAvatarPath(48)}" style="float:left; width:32px;height:32px">
			</a>
			{/if}

		</td>

		<td class='postinfo_block'>
			{if $aView['viewmoderator']}
				{if $aShout['status'] == 1}
					<a href="#" class="action-icons restore" onclick="ls.shoutbox.Moderate({$aShout['id']}, (event.ctrlKey ? 2 : 3)); return false;" title=""><i class="icon irestore"></i></a>
				{else}
					<a href="#" class="action-icons delete" onclick="ls.shoutbox.Moderate({$aShout['id']}, (event.ctrlKey ? 2 : 1)); return false;" title=""><i class="icon idelete"></i></a>
				{/if}
			{/if}

			{if NOT $aShout['modpost']}
			<div class='uinfo_line_block'>
				<div>
				<a href="#" class="nickname" onclick="ls.shoutbox.QuoteAuthor('{$aUser->getLogin()}'); return false;">
					{$aUser->getLogin()}
				</a>: 
					<span class="block_date">
						<span title="{$aShout['date_dt']}"><i class="icon idate"></i> {$aShout['date_tm']}</span>
					</span>
				</div>
			</div>
			{/if}

			<div class="{$aView['postclasses']}">{$aView['postpref']}{$aShout['message']}{$aView['postsufx']}</div>

		</td>

	</tr>

{/foreach}	