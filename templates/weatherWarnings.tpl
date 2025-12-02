{capture assign='contentDescription'}
	{if $warningTime}
		{lang}wcf.weatherWarning.warningTime{/lang}
	{/if}
{/capture}

{include file='header'}

{hascontent}
<div class="paginationTop">
	{content}
	{pages print=true assign=pagesLinks controller="WeatherWarnings" link="pageNo=%d"}
	{/content}
</div>
{/hascontent}

{if !$weatherWarnings|empty}
	{foreach from=$weatherWarnings key=regionName item=warnings}
		<section class="section sectionContainerList">
			<header class="sectionHeader">
				<h2 class="sectionTitle">{$regionName}</h2>
			</header>

			<ol class="containerList userList">
				{foreach from=$warnings item=warning}
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
			</ol>
		</section>
	{/foreach}

	<footer class="contentFooter">
		{hascontent}
		<div class="paginationBottom">
			{content}{@$pagesLinks}{/content}
		</div>
		{/hascontent}

		{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
		{/hascontent}
	</footer>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
