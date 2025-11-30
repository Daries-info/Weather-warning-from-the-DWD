<?php

namespace wcf\system\box;

use wcf\system\WCF;
use wcf\system\weather\warning\WeatherWarningHandler;

/**
 * Box that shows the german forest fire hazard index map.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
class WeatherWarningForestFireHazardIndexGermanyBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected function loadContent(): void
    {
        if (!MODULE_WEATHER_WARNING || !WEATHER_WARNING_ENABLE_FOREST_FIRE_HAZARD_INDEX_WBI) {
            return;
        }

        $user = WCF::getUser();
        if ($user->userID && !$user->getUserOption('weatherWarningForestFireHazardIndexGermanyEnable')) {
            return;
        }

        $forestFireHazardIndexMap = WeatherWarningHandler::getInstance()->getForestFireHazardIndexWBI();
        if ($forestFireHazardIndexMap === "") {
            return;
        }

        $this->content = WCF::getTPL()->fetch(
            'boxWeatherWarningForestFireHazardIndexGermany',
            'wcf',
            [
                'forestFireHazardIndexMap' => $forestFireHazardIndexMap,
            ],
            true
        );
    }
}
