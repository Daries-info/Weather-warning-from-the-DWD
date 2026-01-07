<?php

namespace wcf\system\weather\warning;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use wcf\data\weather\warning\WeatherWarning;
use wcf\system\exception\SystemException;
use wcf\system\io\HttpFactory;
use wcf\system\registry\RegistryHandler;
use wcf\system\SingletonFactory;
use wcf\util\JSON;

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
     */
    public function getWeatherWarning(): array
    {
        $weatherWarning = RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "weatherWarning");

        return $weatherWarning !== null ? \unserialize($weatherWarning) : [];
    }

    /**
     * Returns the time of the weather warnings.
     */
    public function getWeatherWarningTime(): int
    {
        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "weatherWarningTime") ?? 0;
    }
}
