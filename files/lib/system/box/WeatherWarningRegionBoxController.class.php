<?php

namespace wcf\system\box;

use wcf\system\WCF;
use wcf\system\weather\warning\UserWeatherWarningHandler;
use wcf\system\weather\warning\WeatherWarningHandler;

/**
 * Box that shows the region warning weather information.
 *
 * @author Marco Daries, Alexander Langer (Source of ideas)
 * @copyright 2020-2026 Daries.info
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
final class WeatherWarningRegionBoxController extends AbstractBoxController
{
    #[\Override]
    protected function loadContent(): void
    {
        if (!MODULE_WEATHER_WARNING) {
            return;
        }

        $user = WCF::getUser();
        if (
            $user->userID
            && !$user->getUserOption('weatherWarningRegionEnable')
        ) {
            return;
        }

        $weatherWarningHandler = UserWeatherWarningHandler::getInstance();
        $region = $weatherWarningHandler->getRegion();
        $warnings = $weatherWarningHandler->getWarnings();
        $warningTime = WeatherWarningHandler::getInstance()->getWeatherWarningTime();

        $this->content = WCF::getTPL()->render(
            'wcf',
            'boxWeatherWarningRegion',
            [
                'region' => $region,
                'warnings' => $warnings,
                'warningTime' => $warningTime,
            ]
        );
    }
}
