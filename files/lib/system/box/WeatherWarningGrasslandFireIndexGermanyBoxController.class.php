<?php

namespace wcf\system\box;

use wcf\system\WCF;
use wcf\system\weather\warning\WeatherWarningHandler;

/**
 * Box that shows the german grassland fire index map.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */
class WeatherWarningGrasslandFireIndexGermanyBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected function loadContent(): void
    {
        if (!MODULE_WEATHER_WARNING || !WEATHER_WARNING_ENABLE_GRASSLAND_FIRE_INDEX) {
            return;
        }

        $user = WCF::getUser();
        if ($user->userID && !$user->getUserOption('weatherWarningGrasslandFireIndexGermanyEnable')) {
            return;
        }

        $grasslandFireIndexMap = WeatherWarningHandler::getInstance()->getGrasslandFireIndex();
        if ($grasslandFireIndexMap === "") {
            return;
        }

        $this->content = WCF::getTPL()->fetch(
            'boxWeatherWarningGrasslandFireIndexGermany',
            'wcf',
            [
                'grasslandFireIndexMap' => $grasslandFireIndexMap,
            ],
            true
        );
    }
}
