<h2 class="boxTitle">{lang}wcf.weatherWarning.dwd.region{/lang}</h2>

{if $warnings|count > 1}
	<div class="weatherWarningSlider" role="region" aria-roledescription="{lang}wcf.weatherWarning.slider{/lang}" aria-label="{lang}wcf.weatherWarning.dwd.region{/lang}">
{/if}

<div class="pointer" data-weather-info-full-view="{lang}wcf.weatherWarning.dwd.region{/lang}"
	title="{lang}wcf.weatherWarning.viewFullSize{/lang}">
	<div class="weatherWarningContent">
		{if !$region|empty}
			{foreach from=$warnings item=warning name=weatherWarnings}
				<div class="weatherWarningRegion warningBox{if $tpl[foreach][weatherWarnings][first]} active{/if}">
					{include file='__weatherWarning' showLocation=false}
				</div>
			{foreachelse}
				<p>{lang}wcf.weatherWarning.empty{/lang}</p>
			{/foreach}
		{else}
			<p>{lang}wcf.weatherWarning.empty.region{/lang}</p>
		{/if}
	</div>
</div>

{if $warnings|count > 1}
		<div class="weatherWarningSlider__nav">
			<button type="button" class="weatherWarningSlider__button" data-slider-previous aria-label="{lang}wcf.weatherWarning.slider.previous{/lang}">
				{icon name='chevron-left'}
			</button>
			<div class="weatherWarningSlider__dots">
				{section name=slide loop=$warnings}
					<button type="button" class="weatherWarningSlider__dot" data-slider-dot="{$slide}"{if $slide === 0} aria-current="true"{/if} aria-label="{lang}wcf.weatherWarning.slider.slide{/lang}"></button>
				{/section}
			</div>
			<span class="weatherWarningSlider__counter" aria-live="off"><span data-slider-current>1</span> / {#$warnings|count}</span>
			<button type="button" class="weatherWarningSlider__button" data-slider-next aria-label="{lang}wcf.weatherWarning.slider.next{/lang}">
				{icon name='chevron-right'}
			</button>
		</div>
	</div>

	<script data-relocate="true">
		require(['Daries/WeatherWarning/Ui/Slider'], ({ setup }) => setup());
	</script>
{/if}

<div class="weatherWarningFooter">
	{if $warningTime}
		<div class="warningTime">{lang}wcf.weatherWarning.warningTime{/lang}</div>
	{/if}
	<div class="allWarnings">{lang}wcf.weatherWarning.allWarnings{/lang}</div>
</div>
