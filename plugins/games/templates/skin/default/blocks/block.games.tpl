<section class="block block-type-stream">

	<header class="block-header sep">
		<h3><a href="{router page='games'}">{$aLang.plugin.games.block_games}</a></h3>

	</header>
	
	<div class="block-content">
		<div class="js-block-games-content">
            <ul class="latest-list">
            {foreach from=$aGames item=oGames}
                {assign var="oUser" value=$oGames->getUser()}
                {assign var="oBlog" value=$oGames->getBlog()}

                <li>
                    <p>
                        <a href="{$oUser->getUserWebPath()}" class="author">{$oUser->getLogin()}</a>
                        <time datetime="{date_format date=$oGames->getDateAdd() format='c'}" title="{date_format date=$oGames->getDateAdd() format="j F Y, H:i"}">
                            {date_format date=$oGames->getDateAdd() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
                        </time>
                    </p>
                    <a href="{$oGames->getUrl()}#comment" class="stream-topic">{$oGames->getTitle()|escape:'html'}</a>
                    <span class="block-item-comments"><i class="icon-synio-comments-small"></i>{$oGames->getCountComment()}</span>
                </li>
            {/foreach}
            </ul>
		</div>
	</div>
</section>

