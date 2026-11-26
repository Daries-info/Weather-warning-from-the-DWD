<?php

namespace wcf\system\weather\warning;

use wcf\data\weather\warning\WeatherWarning;
use wcf\system\cache\CacheHandler;
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
    /**
     * Marks a failed rebuild of the empty weather warning cache. Only its existence matters,
     * the cache source drops it after the lifetime of the weather warning cache.
     */
    private const string REBUILD_FAILED_CACHE_NAME = 'weatherWarningRebuildFailed';

    private WeatherWarningCacheData $cache;

    /**
     * @var array<string, WeatherWarning[]>
     */
    private array $activeWarnings;

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
     * Returns the weather warnings that have not expired yet.
     *
     * @return array<string, WeatherWarning[]>
     */
    public function getWeatherWarning(): array
    {
        if (!isset($this->activeWarnings)) {
            $this->activeWarnings = [];

            foreach ($this->getCache()->warnings as $regionName => $warnings) {
                $warnings = \array_values(\array_filter(
                    $warnings,
                    static fn(WeatherWarning $warning): bool => !$warning->isExpired()
                ));

                if ($warnings !== []) {
                    $this->activeWarnings[$regionName] = $warnings;
                }
            }
        }

        return $this->activeWarnings;
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
            $this->cache = $this->loadCache();
        }

        return $this->cache;
    }

    /**
     * Without any cached data, the cache is rebuilt within the current request. A failure to reach
     * the DWD must not break the page for the visitor, and it is not retried before the DWD would be
     * requested again anyway, so that visitors are not delayed by a timeout on every page.
     */
    private function loadCache(): WeatherWarningCacheData
    {
        $weatherWarningCache = new WeatherWarningCache();
        $cacheSource = CacheHandler::getInstance()->getCacheSource();

        if ($cacheSource->get(self::REBUILD_FAILED_CACHE_NAME, $weatherWarningCache->getLifetime()) !== null) {
            return new WeatherWarningCacheData([], 0, '', '', []);
        }

        try {
            return $weatherWarningCache->getCache();
        } catch (\RuntimeException $e) {
            if (ENABLE_DEBUG_MODE) {
                throw $e;
            }

            logThrowable($e);

            $cacheSource->set(self::REBUILD_FAILED_CACHE_NAME, true, $weatherWarningCache->getLifetime());

            return new WeatherWarningCacheData([], 0, '', '', []);
        }
    }
}
