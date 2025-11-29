<?php

namespace wcf\system\box;

use wcf\system\WCF;
use wcf\system\weather\warning\UserWeatherWarningHandler;
use wcf\system\weather\warning\WeatherWarningHandler;

/**
 * Box that shows the region warning weather information.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */
class WeatherWarningRegionBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected function loadContent(): void
    {
        if (!MODULE_WEATHER_WARNING) {
            return;
        }

        $user = WCF::getUser();
        $weatherWarningHandler = UserWeatherWarningHandler::getInstance();
        $region = $weatherWarningHandler->getRegion();
        $warnings = $weatherWarningHandler->getWarnings();
        $warningTime = WeatherWarningHandler::getInstance()->getWeatherWarningTime();

        if (
            $user->userID
            && !$user->getUserOption('weatherWarningRegionEnable')
        ) {
            return;
        }

        $this->content = WCF::getTPL()->fetch(
            'boxWeatherWarningRegion',
            'wcf',
            [
                'region' => $region,
                'warnings' => $warnings,
                'warningTime' => $warningTime,
            ],
            true
        );
    }
}
