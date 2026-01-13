{capture assign='contentDescription'}
	{if $warningTime}
		{lang}wcf.weatherWarning.warningTime{/lang}
	{/if}
{/capture}

{include file='header'}

<div class="section {$listView->getContainerCssClassName()}">
	{unsafe:$listView->render()}
</div>

{include file='footer'}
