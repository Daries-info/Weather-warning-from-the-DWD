<div class="headline">
	<img src="{$warning->getIconUrl()}" alt="{$warning->getHeadline()}" loading="lazy">
	<span>{$warning->getHeadline()}</span>
</div>

{if $warning->getWarnLevel()}
	<div class="warnLevel">
		<div class="warnColor"></div>
		<div class="levelRules level{$warning->getWarnLevel()}"></div>
	</div>
{/if}

<dl class="plain dataList containerContent">
	<dt><label>{lang}wcf.weatherWarning.start{/lang}</label></dt>
	<dd>{time time=$warning->getStart() type='plainTime'}</dd>

	<dt><label>{lang}wcf.weatherWarning.end{/lang}</label></dt>
	<dd>
		{if $warning->getEnd()}
			{time time=$warning->getEnd() type='plainTime'}
		{else}
			{lang}wcf.weatherWarning.noEnd{/lang}
		{/if}
	</dd>
</dl>

<div class="description small">
	{$warning->getDescription()}
</div>

{if !$warning->getInstruction()|empty}
	<div class="instruction small">
		{$warning->getInstruction()}
	</div>
{/if}
