<?php

namespace wcf\system\weather\warning;

use wcf\data\weather\warning\WeatherWarning;
use wcf\system\registry\RegistryHandler;
use wcf\system\SingletonFactory;

/**
 * Weather warning handler.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
final class WeatherWarningHandler extends SingletonFactory
{
    /**
     * Package name for the registration action.
     */
    public const PACKAGE_NAME = "dev.daries.weatherWarning";

    /** @var array<string, WeatherWarning[]> */
    private array $warnings;

    /**
     * Returns the forest fire hazard index.
     */
    public function getForestFireHazardIndexWBI(): string
    {
        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "forestFireHazardIndexWBI") ?? "";
    }

    /**
     * Returns the map of Germany.
     */
    public function getGermanyMap(string $key): string
    {
        $key = \sprintf('germanyMap_%s', $key);

        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, $key) ?? "";
    }

    /**
     * Returns the grassland fire index.
     */
    public function getGrasslandFireIndex(): string
    {
        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "grasslandFireIndex") ?? "";
    }

    /**
     * Returns the weather warnings.
     *
     * @return array<string, WeatherWarning[]>
     */
    public function getWeatherWarning(): array
    {
        if (!isset($this->warnings)) {
            $this->warnings = [];

            $weatherWarning = RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "weatherWarning");

            if ($weatherWarning !== null) {
                $result = @\unserialize($weatherWarning);

                if (\is_array($result)) {
                    $this->warnings = $result;
                }
            }
        }

        return $this->warnings;
    }

    /**
     * Returns the time of the weather warnings.
     */
    public function getWeatherWarningTime(): int
    {
        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "weatherWarningTime") ?? 0;
    }
}
