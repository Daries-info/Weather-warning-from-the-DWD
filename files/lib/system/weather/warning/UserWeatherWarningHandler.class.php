<?php

namespace wcf\system\weather\warning;

use wcf\data\weather\warning\WeatherWarning;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Weather warning handler for the current user.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
final class UserWeatherWarningHandler extends SingletonFactory
{
    /**
     * region
     */
    private string $region = '';

    /**
     * All weather warnings from DWD.
     *
     * @var array<string, WeatherWarning[]>
     */
    private array $warnings = [];

    /**
     * Returns the current region.
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * Returns the weather warnings for the current region.
     *
     * @return WeatherWarning[]
     */
    public function getWarnings(): array
    {
        return $this->warnings[$this->getRegion()] ?? [];
    }

    /**
     * Returns false if has no warnings.
     */
    public function hasWarnings(): bool
    {
        return $this->getWarnings() !== [];
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        if (!MODULE_WEATHER_WARNING) {
            return;
        }

        $this->warnings = WeatherWarningHandler::getInstance()->getWeatherWarning();

        $this->setRegion(WEATHER_WARNING_DEFAULT_REGION);

        $user = WCF::getUser();
        if ($user->userID) {
            $userRegion = $user->getUserOption('weatherWarningRegion');

            if ($userRegion !== null && $userRegion !== '') {
                $this->setRegion($userRegion);
            }
        }

        EventHandler::getInstance()->fireAction($this, 'init');
    }

    /**
     * Sets the region
     */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }
}
