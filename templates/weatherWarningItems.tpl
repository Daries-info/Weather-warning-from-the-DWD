{assign var='__isNewRegion' value=true}
{assign var='__currentRegionName' value=''}
{assign var='__shouldCloseRegion' value=false}

{foreach from=$view->getItems() item='warning'}
	{if $__currentRegionName !== $warning->getRegionName()}
		{if !$__isNewRegion}
			{assign var='__shouldCloseRegion' value=true}
		{/if}

		{assign var='__isNewRegion' value=true}
		{assign var='__currentRegionName' value=$warning->getRegionName()}
	{/if}

	{if $__shouldCloseRegion}
			</ol>
		</section>
		{assign var='__shouldCloseRegion' value=false}
	{/if}


	{if $__isNewRegion}
		{assign var='__isNewRegion' value=false}

		<section class="section sectionContainerList">
			<header class="sectionHeader">
				<h2 class="sectionTitle">{$warning->getRegionName()}</h2>
			</header>

			<ol class="containerList weatherWarning__listView">
	{/if}

				<li class="weatherWarningRegion">
					<div class="headline">
						{@$warning->getIcon()}
						<span>{$warning->getHeadline()}</span>
					</div>
					<div class="warnLevel">
						<div class="warnColor"></div>
						<div class="levelRules level{$warning->getLevel()}"></div>
					</div>
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
				</li>
{/foreach}
