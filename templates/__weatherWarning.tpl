<article class="weatherWarning weatherWarning--type{$warning->getType()} weatherWarning--level{$warning->getWarnLevel()}">
	<header class="weatherWarning__header">
		<img src="{$warning->getIconUrl()}" class="weatherWarning__icon" width="50" height="50" alt="" loading="lazy">
		<div class="weatherWarning__title">
			<h3 class="weatherWarning__headline">{$warning->getHeadline()}</h3>
			{if $warning->getWarnLevel() && $warning->getMaxWarnLevel() > 1}
				<div class="weatherWarning__level">
					<span class="weatherWarning__levelBar" aria-hidden="true">
						{section name=segment loop=$warning->getMaxWarnLevel()}
							<span{if $segment < $warning->getWarnLevel()} class="active"{/if}></span>
						{/section}
					</span>
					<span>{lang}wcf.weatherWarning.level{/lang}</span>
				</div>
			{/if}
		</div>
	</header>

	<dl class="plain weatherWarning__period">
		<div>
			<dt>{lang}wcf.weatherWarning.start{/lang}</dt>
			<dd>{time time=$warning->getStart() type='plainTime'}</dd>
		</div>
		<div>
			<dt>{lang}wcf.weatherWarning.end{/lang}</dt>
			<dd>
				{if $warning->getEnd()}
					{time time=$warning->getEnd() type='plainTime'}
				{else}
					{lang}wcf.weatherWarning.noEnd{/lang}
				{/if}
			</dd>
		</div>
	</dl>

	<div class="weatherWarning__description">
		{$warning->getDescription()}
	</div>

	{if !$warning->getInstruction()|empty}
		<div class="info weatherWarning__instruction">
			{$warning->getInstruction()}
		</div>
	{/if}
</article>
