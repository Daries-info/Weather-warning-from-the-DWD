{if $view->hasOwnRegionSection()}
	{assign var='region' value=$view->getOwnRegion()}

	<section class="section weatherWarning__gridContainer">
		<header class="sectionHeader">
			<h2 class="sectionTitle">
				{if $__wcf->getUserWeatherWarningHandler()->isDefaultRegion()}
					{lang}wcf.weatherWarning.defaultRegion{/lang}
				{else}
					{lang}wcf.weatherWarning.ownRegion{/lang}
				{/if}
				· {$region}
			</h2>
		</header>

		{if $view->getOwnRegionItems()}
			<ol class="weatherWarning__grid">
				{foreach from=$view->getOwnRegionItems() item='warning'}
					<li class="weatherWarning__card weatherWarning__card--ownRegion">
						{include file='__weatherWarning' showLocation=true}
					</li>
				{/foreach}
			</ol>
		{else}
			<p class="info">{lang}wcf.weatherWarning.empty{/lang}</p>
		{/if}
	</section>
{/if}

{if $view->getOtherRegionItems()}
	<section class="section weatherWarning__gridContainer">
		{if $view->hasOwnRegionSection()}
			<header class="sectionHeader">
				<h2 class="sectionTitle">{lang}wcf.weatherWarning.otherRegions{/lang}</h2>
			</header>
		{/if}

		<ol class="weatherWarning__grid">
			{foreach from=$view->getOtherRegionItems() item='warning'}
				<li class="weatherWarning__card">
					{include file='__weatherWarning' showLocation=true}
				</li>
			{/foreach}
		</ol>
	</section>
{/if}
