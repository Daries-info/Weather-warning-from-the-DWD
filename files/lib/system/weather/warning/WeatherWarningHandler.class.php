<?php

namespace wcf\system\weather\warning;

use wcf\data\weather\warning\WeatherWarning;
use wcf\system\cache\tolerant\data\WeatherWarningCacheData;
use wcf\system\cache\tolerant\WeatherWarningCache;
use wcf\system\SingletonFactory;

use function wcf\functions\exception\logThrowable;

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
            try {
                $this->cache = (new WeatherWarningCache())->getCache();
            } catch (\RuntimeException $e) {
                // Without any cached data, the cache is rebuilt within the current request.
                // A failure to reach the DWD must not break the page for the visitor.
                if (ENABLE_DEBUG_MODE) {
                    throw $e;
                }

                logThrowable($e);

                $this->cache = new WeatherWarningCacheData([], 0, '', '', []);
            }
        }

        return $this->cache;
    }
}
