{include file='textOptionType'}
<script data-relocate="true">
	require(['Daries/WeatherWarning/Ui/Region/Search/Input'], function(UiWeatherWarningRegionSearchInput) {
		new UiWeatherWarningRegionSearchInput(document.querySelector('input[id="{$option->optionName}"]'));
	});
</script>
