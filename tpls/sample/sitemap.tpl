{strip}
<div class="post">
<h2>Sitemap</h2>
<div class="content">
{foreach from=$data key=position item=menu_data}
	{*<h2>{$position}</h2>*}
	<ul>
	{foreach from=$menu_data key=name item=mc}
		<li>
			<a href="?{$mc.menu.url}">{$mc.menu.title}</a>
			{foreach from=$mc.content.data item=content}
				{foreach from=$content.entries item=c}
				<div class="pad">
					&ndash; <a href="{$c.url_open}">{$c.title}</a>
					{if $c.descr}<div class="pad text">{$c.descr|rssBody}</div>{/if}
				</div>
				{/foreach}
			{/foreach}
			
			{if $mc.sub}
				<div class="pad">
					<ul>
					{foreach from=$mc.sub item=sub}
						<li>
							<a href="?{$sub.menu.url}">{$sub.menu.title}</a>
							{foreach  from=$sub.content.data item=content}
								{foreach from=$content.entries item=c}
								<div class="pad">
									&ndash; <a href="{$c.url_open}">{$c.title}</a>
									{if $c.descr}<div class="pad text">{$c.descr|rssBody}</div>{/if}
								</div>
								{/foreach}
							{/foreach}
						</li>
					{/foreach}
					</ul>
				</div>
			{/if}
		</li>
	{/foreach}
	</ul>
{/foreach}
</div>
</div>
{/strip}

