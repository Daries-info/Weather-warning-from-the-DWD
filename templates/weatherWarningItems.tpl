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
					{include file='__weatherWarning'}
				</li>
{/foreach}
