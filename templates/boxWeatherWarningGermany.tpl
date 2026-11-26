<div class="pointer" data-weather-info-full-view="{lang}wcf.weatherWarning.dwd{/lang}"
	title="{lang}wcf.weatherWarning.viewFullSize{/lang}">
	<div class="weatherWarningMap">
		<img src="{$germanyMap}" class="germanyMap" alt="">

		{if $viewMapInfo}
			<div class="germanyMapInfo">
				<div class="legendBoxContainer">
					<div title="{lang}wcf.weatherWarning.dwd.legend4{/lang}">
						<span class="legendBox" style="--weatherWarningLegendColor: rgba(255, 235, 59, 1)"></span>
						<span class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend4.short{/lang}</span>
					</div>
					<div title="{lang}wcf.weatherWarning.dwd.legend3{/lang}">
						<span class="legendBox" style="--weatherWarningLegendColor: rgba(255, 146, 0, 1)"></span>
						<span class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend3.short{/lang}</span>
					</div>
					<div title="{lang}wcf.weatherWarning.dwd.legend2{/lang}">
						<span class="legendBox" style="--weatherWarningLegendColor: rgba(229, 57, 53, 1)"></span>
						<span class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend2.short{/lang}</span>
					</div>
					<div title="{lang}wcf.weatherWarning.dwd.legend1{/lang}">
						<span class="legendBox" style="--weatherWarningLegendColor: rgba(136, 14, 79, 1)"></span>
						<span class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend1.short{/lang}</span>
					</div>
					<div title="{lang}wcf.weatherWarning.dwd.legend5{/lang}">
						<span class="legendBox preliminary" style="--weatherWarningLegendColor: rgba(255, 255, 255, 1)"></span>
						<span class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend5.short{/lang}</span>
					</div>
					<div title="{lang}wcf.weatherWarning.dwd.legend6{/lang}">
						<span class="legendBox" style="--weatherWarningLegendColor: rgba(158, 70, 248, 1)"></span>
						<span class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend6.short{/lang}</span>
					</div>
					<div title="{lang}wcf.weatherWarning.dwd.legend7{/lang}">
						<span class="legendBox" style="--weatherWarningLegendColor: rgba(204, 153, 255, 1)"></span>
						<span class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend7.short{/lang}</span>
					</div>
					<div title="{lang}wcf.weatherWarning.dwd.legend8{/lang}">
						<span class="legendBox" style="--weatherWarningLegendColor: rgba(254, 104, 254, 1)"></span>
						<span class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend8.short{/lang}</span>
					</div>
					<div title="{lang}wcf.weatherWarning.dwd.legend9{/lang}">
						<span class="legendBox" style="--weatherWarningLegendColor: rgba(197, 229, 102, 1)"></span>
						<span class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend9.short{/lang}</span>
					</div>
				</div>
			</div>
		{/if}
	</div>
</div>

<div class="weatherWarningFooter">
	<a href="https://www.dwd.de/DE/wetter/warnungen_gemeinden/warnWetter_node.htm" class="externalURL" rel="nofollow noopener"{if EXTERNAL_LINK_TARGET_BLANK} target="_blank"{/if}>{lang}wcf.weatherWarning.more.information{/lang}</a>
</div>
