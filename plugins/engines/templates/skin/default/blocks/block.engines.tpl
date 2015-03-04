<section class="block block-type-stream">

	<header class="block-header sep">
		<h3><a href="{router page='engines'}">{$aLang.plugin.engines.block_engines}</a></h3>

	</header>
	
	<div class="block-content">
		<div class="js-block-engines-content">
            <ul class="latest-list">
            {foreach from=$aEngines item=oEngines}
                {assign var="oUser" value=$oEngines->getUser()}
                {assign var="oBlog" value=$oEngines->getBlog()}

                <li>
                    <p>
                        <a href="{$oUser->getUserWebPath()}" class="author">{$oUser->getLogin()}</a>
                        <time datetime="{date_format date=$oEngines->getDateAdd() format='c'}" title="{date_format date=$oEngines->getDateAdd() format="j F Y, H:i"}">
                            {date_format date=$oEngines->getDateAdd() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
                        </time>
                    </p>
                    <a href="{$oEngines->getUrl()}#comment" class="stream-topic">{$oEngines->getTitle()|escape:'html'}</a>
                    <span class="block-item-comments"><i class="icon-synio-comments-small"></i>{$oEngines->getCountComment()}</span>
                </li>
            {/foreach}
            </ul>
		</div>
	</div>
</section>

