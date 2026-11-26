<section class="section">
    <h2 class="sectionTitle">{lang}wcf.acp.box.settings{/lang}</h2>

    <dl>
        <dt><label for="map">{lang}wcf.weatherWarning.box.settings.map{/lang}</label></dt>
        <dd>
            <select name="map" id="map">
                {foreach from=$mapOptions key=optionKey item=optionValue}
                    <option value="{$optionKey}"{if $map === $optionKey} selected{/if}>{$optionValue}</option>
                {/foreach}
            </select>
        </dd>
    </dl>

    <dl>
        <dt></dt>
        <dd>
            <label>
                <input type="checkbox" name="viewMapInfo" value="1"{if $viewMapInfo} checked{/if}>
                {lang}wcf.weatherWarning.box.settings.viewMapInfo{/lang}
            </label>
        </dd>
    </dl>

    {event name='fields'}
</section>
