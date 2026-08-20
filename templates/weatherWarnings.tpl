{capture assign='contentDescription'}
	{if $warningTime}
		{lang}wcf.weatherWarning.warningTime{/lang}
	{/if}
{/capture}

{include file='header'}

{if $hasWeatherWarningData}
	<div class="section {$listView->getContainerCssClassName()}">
		{unsafe:$listView->render()}
	</div>
{else}
	<p class="info">{lang}wcf.weatherWarning.noData{/lang}</p>
{/if}

{include file='footer'}
