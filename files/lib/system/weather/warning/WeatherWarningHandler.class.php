<?php

namespace wcf\system\weather\warning;

use wcf\data\weather\warning\WeatherWarning;
use wcf\system\cache\eager\data\WeatherWarningCacheData;
use wcf\system\cache\eager\WeatherWarningCache;
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
    private WeatherWarningCacheData $cache;

    /**
     * Returns the forest fire hazard index.
     */
    public function getForestFireHazardIndexWBI(): string
    {
        return $this->getCache()->forestFireHazardIndexWBI;
    }

    /**
     * Returns the map of Germany.
     */
    public function getGermanyMap(string $key): string
    {
        return $this->getCache()->getGermanyMap($key);
    }

    /**
     * Returns the grassland fire index.
     */
    public function getGrasslandFireIndex(): string
    {
        return $this->getCache()->grasslandFireIndex;
    }

    /**
     * Returns the weather warnings.
     *
     * @return array<string, WeatherWarning[]>
     */
    public function getWeatherWarning(): array
    {
        return $this->getCache()->warnings;
    }

    /**
     * Returns the time of the weather warnings.
     */
    public function getWeatherWarningTime(): int
    {
        return $this->getCache()->time;
    }

    private function getCache(): WeatherWarningCacheData
    {
        if (!isset($this->cache)) {
            $this->cache = (new WeatherWarningCache())->getCache();
        }

        return $this->cache;
    }
}
