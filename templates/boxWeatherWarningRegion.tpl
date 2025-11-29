<h2 class="boxTitle">{lang}wcf.weatherWarning.dwd.region{/lang}</h2>

<div class="pointer" data-weather-info-full-view="{lang}wcf.weatherWarning.dwd.region{/lang}"
	title="{lang}wcf.weatherWarning.viewFullSize{/lang}">
	<div class="weatherWarningContent">
		{if !$region|empty}
			{foreach from=$warnings item=warning}
				<div class="weatherWarningRegion warningBox">
					<div class="headline">
						{unsafe:$warning->getIcon()}
						<span>{$warning->getHeadline()}</span>
					</div>

					<div class="warnLevel">
						<div class="warnColor"></div>
						<div class="levelRules level{$warning->getLevel()}"></div>
					</div>

					<dl class="plain dataList containerContent">
						<dt><label>{lang}wcf.weatherWarning.start{/lang}</label></dt>
						<dd>{time time=$warning->getStart() type='plainTime'}</dd>

						{if $warning->getEnd()}
							<dt><label>{lang}wcf.weatherWarning.end{/lang}</label></dt>
							<dd>{time time=$warning->getEnd() type='plainTime'}</dd>
						{/if}
					</dl>

					<div class="description small">
						{$warning->getDescription()}
					</div>

					{if !$warning->getInstruction()|empty}
						<div class="instruction small">
							{$warning->getInstruction()}
						</div>
					{/if}
				</div>
			{foreachelse}
				<p>{lang}wcf.weatherWarning.empty{/lang}</p>
			{/foreach}
		{else}
			<p>{lang}wcf.weatherWarning.empty.region{/lang}</p>
		{/if}
	</div>
</div>

<div class="weatherWarningFooter">
	{if $warningTime}
		<div class="warningTime">{lang}wcf.weatherWarning.warningTime{/lang}</div>
	{/if}
	<div class="allWarnings">{lang}wcf.weatherWarning.allWarnings{/lang}</div>
</div>
