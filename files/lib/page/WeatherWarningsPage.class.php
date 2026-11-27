<?php

namespace wcf\page;

use wcf\system\listView\user\WeatherWarningListView;
use wcf\system\WCF;
use wcf\system\weather\warning\WeatherWarningHandler;

/**
 * Shows the weather warnings.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2026 Daries.info
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 *
 * @extends AbstractListViewPage<WeatherWarningListView>
 */
final class WeatherWarningsPage extends AbstractListViewPage
{
    public $neededModules = ['MODULE_WEATHER_WARNING'];

    #[\Override]
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'warningTime' => WeatherWarningHandler::getInstance()->getWeatherWarningTime(),
            'hasWeatherWarningData' => WeatherWarningHandler::getInstance()->getWeatherWarningTime() !== 0,
        ]);
    }

    #[\Override]
    protected function createListView(): WeatherWarningListView
    {
        return new WeatherWarningListView();
    }
}
