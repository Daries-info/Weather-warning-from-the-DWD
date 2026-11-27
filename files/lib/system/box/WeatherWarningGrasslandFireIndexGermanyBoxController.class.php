<?php

namespace wcf\system\box;

use wcf\system\WCF;
use wcf\system\weather\warning\WeatherWarningHandler;

/**
 * Box that shows the german grassland fire index map.
 *
 * @author  Marco D., Alexander Langer (Source of ideas)
 * @copyright   2020-2026 Daries.info
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
final class WeatherWarningGrasslandFireIndexGermanyBoxController extends AbstractBoxController
{
    #[\Override]
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
        if ($grasslandFireIndexMap === '') {
            return;
        }

        $this->content = WCF::getTPL()->render(
            'wcf',
            'boxWeatherWarningGrasslandFireIndexGermany',
            [
                'grasslandFireIndexMap' => $grasslandFireIndexMap,
            ]
        );
    }
}
