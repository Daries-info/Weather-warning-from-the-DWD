<div class="pointer" data-weather-info-full-view="{lang}wcf.weatherWarning.dwd{/lang}"
	title="{lang}wcf.weatherWarning.viewFullSize{/lang}">
	<div class="weatherWarningMap">
		<img src="{unsafe:$germanyMap}" class="germanyMap" alt="">

		{if $viewMapInfo}
			<div class="germanyMapInfo">
				<div class="legendBoxContainer">
					<div>
						<div class="legendBox warningLegend1"
							style="--weatherWarningLegendColor: rgba(136, 14, 79, 1)">
						</div>
						<div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend1{/lang}</div>
					</div>
					<div>
						<div class="legendBox warningLegend2"
							style="--weatherWarningLegendColor: rgba(229, 57, 53, 1)">
						</div>
						<div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend2{/lang}</div>
					</div>
					<div>
						<div class="legendBox warningLegend3"
							style="--weatherWarningLegendColor: rgba(255, 146, 0, 1)">
						</div>
						<div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend3{/lang}</div>
					</div>
					<div>
						<div class="legendBox warningLegend4"
							style="--weatherWarningLegendColor: rgba(255, 235, 59, 1)">
						</div>
						<div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend4{/lang}</div>
					</div>
					<div>
						<div class="legendBox preliminary warningLegend5"
							style="--weatherWarningLegendColor: rgba(255, 255, 255, 1)"></div>
						<div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend5{/lang}</div>
					</div>
					{if $__wcf->getLanguage()->getFixedLanguageCode() === "de"}
						<div>
							<div class="legendBox warningLegend6"
								style="--weatherWarningLegendColor: rgba(158, 70, 248, 1)">
							</div>
							<div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend6{/lang}</div>
						</div>
					{/if}
					<div>
						<div class="legendBox warningLegend7"
							style="--weatherWarningLegendColor: rgba(204, 153, 255, 1)">
						</div>
						<div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend7{/lang}</div>
					</div>
					<div>
						<div class="legendBox warningLegend8"
							style="--weatherWarningLegendColor: rgba(254, 104, 254, 1)">
						</div>
						<div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend8{/lang}</div>
					</div>
					<div>
						<div class="legendBox warningLegend9"
							style="--weatherWarningLegendColor: rgba(197, 229, 102, 1)">
						</div>
						<div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend9{/lang}</div>
					</div>
				</div>
			</div>
		{/if}
	</div>
</div>

<div class="weatherWarningMapButton">
	<a href="https://www.dwd.de/DE/wetter/warnungen_gemeinden/warnWetter_node.htm" class="button"
		{if EXTERNAL_LINK_TARGET_BLANK} target="_blank" {/if}>
		{icon name='info' size=24}
		<span>{lang}wcf.weatherWarning.more.information{/lang}</span>
	</a>
</div>
